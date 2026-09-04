<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Reel;
use App\Models\Testimonial;
use App\Models\Setting;
use App\Services\AvailabilityService;
use App\Services\BookingCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;

class ApiController extends Controller
{
    protected AvailabilityService $availabilityService;
    protected BookingCheckoutService $checkoutService;

    public function __construct(AvailabilityService $availabilityService, BookingCheckoutService $checkoutService)
    {
        $this->availabilityService = $availabilityService;
        $this->checkoutService = $checkoutService;
    }

    /**
     * Mobile login API (Supports Login by Phone or Email)
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'nullable|string', // phone or email
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->login ?? $request->phone ?? $request->email;

        if (empty($identifier)) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى إدخال رقم الواتساب/الهاتف أو البريد الإلكتروني.'
            ], 422);
        }

        $user = User::where('phone', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة. يرجى التحقق من رقم الواتساب/كلمة المرور.'
            ], 401);
        }

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * Mobile register API (Manual fallback)
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email ?? ('patient_' . preg_replace('/[^0-9]/', '', $request->phone) . '@yonis-app.com'),
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'patient',
        ]);

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
            ]
        ], 201);
    }

    /**
     * Mobile logout API
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح.'
        ]);
    }

    /**
     * Get doctor portfolio details
     */
    public function getDoctorProfile()
    {
        $profile = DoctorProfile::with('user')->first();
        
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'ملف الطبيب غير متوفر حالياً.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'profile' => $profile
        ]);
    }

    /**
     * Get all active services with clear separation between Clinic and Online channel pricing
     */
    public function getServices(Request $request)
    {
        $clinicEnabled = Setting::get('clinic_booking_enabled', '1') === '1';
        $onlineEnabled = Setting::get('online_booking_enabled', '1') === '1';
        $chatEnabled = Setting::get('chat_enabled', '1') === '1';
        $voiceEnabled = Setting::get('voice_enabled', '1') === '1';
        $videoEnabled = Setting::get('video_enabled', '1') === '1';

        $currencyCode = Setting::currencyCode();
        $currencySymbol = Setting::currencySymbol();

        $allRaw = Service::where('is_active', true)->get();

        // Format Clinic Services
        $clinicServices = $allRaw->filter(function ($s) {
            return in_array($s->type, ['clinic', 'both'], true);
        })->map(function ($s) use ($currencyCode, $currencySymbol) {
            $clinicPrice = (float) ($s->clinic_price ?? $s->price);
            return [
                'id' => $s->id,
                'title' => $s->title,
                'description' => $s->description,
                'duration' => $s->duration,
                'price' => $clinicPrice,
                'clinic_price' => $clinicPrice,
                'booking_type' => 'clinic',
                'currency' => $currencyCode,
                'currency_symbol' => $currencySymbol,
                'type' => $s->type,
                'location' => Setting::get('clinic_address', 'مقر العيادة - د. يونس المرشد'),
            ];
        })->values();

        // Format Online Services with distinct channel prices (Voice, Chat, Video)
        $onlineServices = $allRaw->filter(function ($s) {
            return in_array($s->type, ['online', 'both'], true);
        })->map(function ($s) use ($chatEnabled, $voiceEnabled, $videoEnabled, $currencyCode, $currencySymbol) {
            $channels = [];

            $hasVideo = !is_null($s->video_price) && (float)$s->video_price > 0;
            $hasVoice = !is_null($s->voice_price) && (float)$s->voice_price > 0;
            $hasChat  = !is_null($s->chat_price) && (float)$s->chat_price > 0;

            // If none explicitly set, enable channels based on base price
            if (!$hasVideo && !$hasVoice && !$hasChat) {
                $hasVideo = true;
                $hasVoice = true;
                $hasChat = true;
            }

            if ($hasVideo && $videoEnabled) {
                $p = (float)($s->video_price ?: $s->price);
                $channels[] = [
                    'channel' => 'video',
                    'name' => 'مكالمة فيديو أونلاين',
                    'price' => $p,
                    'currency' => $currencyCode,
                    'currency_symbol' => $currencySymbol,
                    'duration' => $s->duration,
                    'is_enabled' => true,
                ];
            }

            if ($hasVoice && $voiceEnabled) {
                $p = (float)($s->voice_price ?: $s->price);
                $channels[] = [
                    'channel' => 'voice',
                    'name' => 'استشارة صوتية',
                    'price' => $p,
                    'currency' => $currencyCode,
                    'currency_symbol' => $currencySymbol,
                    'duration' => $s->duration,
                    'is_enabled' => true,
                ];
            }

            if ($hasChat && $chatEnabled) {
                $p = (float)($s->chat_price ?: $s->price);
                $channels[] = [
                    'channel' => 'chat',
                    'name' => 'محادثة نصية (شات)',
                    'price' => $p,
                    'currency' => $currencyCode,
                    'currency_symbol' => $currencySymbol,
                    'duration' => $s->duration,
                    'is_enabled' => true,
                ];
            }

            $primaryPrice = !empty($channels) ? $channels[0]['price'] : (float)$s->price;
            $channelType = count($channels) === 1 ? $channels[0]['channel'] : 'all';

            return [
                'id' => $s->id,
                'title' => $s->title,
                'description' => $s->description,
                'duration' => $s->duration,
                'booking_type' => 'online',
                'channel_type' => $channelType,
                'currency' => $currencyCode,
                'currency_symbol' => $currencySymbol,
                'type' => $s->type,
                'price' => $primaryPrice,
                'video_price' => $s->video_price !== null ? (float)$s->video_price : null,
                'voice_price' => $s->voice_price !== null ? (float)$s->voice_price : null,
                'chat_price' => $s->chat_price !== null ? (float)$s->chat_price : null,
                'channels' => $channels,
            ];
        })->values();

        // Filter by requested type if passed
        $requestedType = strtolower((string) $request->query('type', ''));
        if ($requestedType === 'clinic') {
            return response()->json([
                'success' => true,
                'type' => 'clinic',
                'currency' => $currencyCode,
                'currency_symbol' => $currencySymbol,
                'is_enabled' => $clinicEnabled,
                'total' => $clinicServices->count(),
                'services' => $clinicServices,
            ]);
        }

        if ($requestedType === 'online') {
            return response()->json([
                'success' => true,
                'type' => 'online',
                'currency' => $currencyCode,
                'currency_symbol' => $currencySymbol,
                'is_enabled' => $onlineEnabled,
                'channels_enabled' => [
                    'video' => $videoEnabled,
                    'voice' => $voiceEnabled,
                    'chat' => $chatEnabled,
                ],
                'total' => $onlineServices->count(),
                'services' => $onlineServices,
            ]);
        }

        return response()->json([
            'success' => true,
            'currency' => $currencyCode,
            'currency_symbol' => $currencySymbol,
            'channels_enabled' => [
                'clinic' => $clinicEnabled,
                'online' => $onlineEnabled,
                'video' => $videoEnabled,
                'voice' => $voiceEnabled,
                'chat' => $chatEnabled,
            ],
            'clinic_services' => $clinicServices,
            'online_services' => $onlineServices,
            // Full raw list for backward compatibility
            'services' => $allRaw,
        ]);
    }

    /**
     * Dedicated Clinic Services endpoint
     */
    public function getClinicServices(Request $request)
    {
        $request->merge(['type' => 'clinic']);
        return $this->getServices($request);
    }

    /**
     * Dedicated Online Services endpoint with Voice/Chat/Video breakdown
     */
    public function getOnlineServices(Request $request)
    {
        $request->merge(['type' => 'online']);
        return $this->getServices($request);
    }

    /**
     * Get available slots
     */
    public function getSlots(Request $request)
    {
        $serviceId = $request->input('service_id');
        $service = null;
        if ($serviceId) {
            $service = Service::find($serviceId);
        }
        if (!$service) {
            $service = Service::where('is_active', true)->first();
        }

        if (!$service) {
            return response()->json([
                'success' => true,
                'slots' => []
            ]);
        }

        $dateStr = $request->input('date', date('Y-m-d'));
        if (strtotime($dateStr) < strtotime(date('Y-m-d'))) {
            $dateStr = date('Y-m-d');
        }

        $slots = $this->availabilityService->getAvailableSlots(
            $service->id,
            $dateStr
        );

        return response()->json([
            'success' => true,
            'slots' => $slots
        ]);
    }

    /**
     * Alias for getSlots
     */
    public function getAvailableSlots(Request $request)
    {
        return $this->getSlots($request);
    }

    /**
     * Get API status & configuration flags for Mobile Client
     */
    public function getApiConfig()
    {
        $payZainEnabled = Setting::get('payment_zaincash_enabled', '1') === '1';
        $paySuperkiEnabled = Setting::get('payment_superki_enabled', '1') === '1';
        $payCardEnabled = Setting::get('payment_card_enabled', '0') === '1';
        $defaultPaymentMethod = $payZainEnabled ? 'zaincash' : ($paySuperkiEnabled ? 'superki' : ($payCardEnabled ? 'card' : 'zaincash'));
        $doctorProfile = DoctorProfile::first();

        return response()->json([
            'success' => true,
            'config' => [
                'api_enabled' => Setting::get('api_enabled', '1') === '1',
                'stripe_enabled' => Setting::get('stripe_enabled', '0') === '1',
                'clinic_booking_enabled' => Setting::get('clinic_booking_enabled', '1') === '1',
                'online_booking_enabled' => Setting::get('online_booking_enabled', '1') === '1',
                'chat_enabled' => Setting::get('chat_enabled', '1') === '1',
                'voice_enabled' => Setting::get('voice_enabled', '1') === '1',
                'video_enabled' => Setting::get('video_enabled', '1') === '1',
                'currency' => Setting::currencyCode(),
                'currency_code' => Setting::currencyCode(),
                'currency_symbol' => Setting::currencySymbol(),
                'default_payment_url' => Setting::get('default_payment_url', 'https://younisalmurshed.gumroad.com/l/srjlvw?wanted=true'),
                'max_reschedule_allowed' => (int) Setting::get('max_reschedule_allowed', '2'),
                'min_reschedule_notice_hours' => (int) Setting::get('min_reschedule_notice_hours', '24'),
                'hero_images' => [
                    'web' => $doctorProfile?->hero_image,
                    'mobile' => $doctorProfile?->mobile_hero_image,
                ],
                'whatsapp_widget' => [
                    'enabled' => Setting::get('whatsapp_widget_enabled', '1') === '1',
                    'number' => Setting::get('whatsapp_number', '+9647800000000'),
                    'default_message' => Setting::get('whatsapp_default_message', 'مرحباً دكتور يونس، أود الاستفسار عن حجز موعد استشارة.'),
                    'greeting' => Setting::get('whatsapp_widget_greeting', 'أهلاً بك! 👋 معك عيادة الدكتور يونس المرشد. كيف يمكننا مساعدتك اليوم؟'),
                ],
                'payment' => [
                    'default_method' => $defaultPaymentMethod,
                    'zaincash' => [
                        'enabled' => $payZainEnabled,
                        'qr'      => Setting::get('payment_zaincash_qr', ''),
                        'label'   => Setting::get('payment_zaincash_label', 'افتح تطبيق زين كاش وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال للدكتور.'),
                    ],
                    'superki' => [
                        'enabled' => $paySuperkiEnabled,
                        'qr'      => Setting::get('payment_superki_qr', ''),
                        'label'   => Setting::get('payment_superki_label', 'افتح تطبيق SuperKi وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال للدكتور.'),
                    ],
                    'card' => [
                        'enabled'      => $payCardEnabled,
                        'link'         => Setting::get('payment_card_link', ''),
                        'instructions' => Setting::get('payment_card_instructions', 'يمكنك الدفع مباشرة باستخدام أي بطاقة فيزا أو ماستر كارد بأمان وسرية تامة.'),
                    ],
                    'whatsapp_number' => Setting::get('whatsapp_number', '+9647800000000'),
                ]
            ]
        ]);
    }

    /**
     * Check if client user is registered or new before checkout
     */
    public function checkUser(Request $request)
    {
        $phone = $request->input('phone');
        $email = $request->input('email');

        if (empty($phone) && empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى تزويد رقم الهاتف أو البريد الإلكتروني للتحقق من حالة الحساب.'
            ], 422);
        }

        $user = User::query()
            ->when(!empty($phone), fn($q) => $q->where('phone', $phone))
            ->when(!empty($email), fn($q) => $q->orWhere('email', $email))
            ->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'is_registered' => true,
                'requires_account' => false,
                'requires_password' => false,
                'account_prompt' => null,
                'message' => 'العميل مسجل مسبقاً في النظام.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'is_registered' => false,
            'requires_account' => true,
            'requires_password' => true,
            'account_prompt' => 'يرجى إضافة حسابك وكلمة المرور لإتمام الحجز وإنشاء حسابك.',
            'message' => 'عميل جديد - يتطلب إضافة بيانات الحساب وكلمة المرور.',
            'user' => null
        ]);
    }

    /**
     * Initialize booking checkout (Step 1: Save temporary data, External Gumroad Link & Generate Stripe Intent)
     */
    public function initializeCheckout(Request $request)
    {
        if (Setting::get('api_enabled', '1') === '0') {
            return response()->json([
                'success' => false,
                'message' => 'خادم الـ API في حالة صيانة حالياً، يرجى المحاولة لاحقاً.'
            ], 503);
        }

        // Determine if user is already registered or logged in:
        // 1. Direct Bearer token parsing (Guaranteed to work for Sanctum outside auth middleware)
        $existingUser = null;
        $bearerToken = $request->bearerToken();
        if (!empty($bearerToken)) {
            try {
                if (class_exists(\Laravel\Sanctum\PersonalAccessToken::class)) {
                    $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($bearerToken);
                    if ($tokenModel && $tokenModel->tokenable) {
                        $existingUser = $tokenModel->tokenable;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to other detection methods
            }
        }

        // 2. Sanctum auth guard or default request user
        if (!$existingUser) {
            try {
                $existingUser = auth('sanctum')->user() ?: $request->user();
            } catch (\Throwable $e) {
                // Fallback
            }
        }

        // 3. Fallback: Lookup by phone or email if provided in request
        if (!$existingUser) {
            $checkPhone = $request->input('phone');
            $checkEmail = $request->input('email');
            if (!empty($checkPhone)) {
                $existingUser = User::where('phone', $checkPhone)->first();
            }
            if (!$existingUser && !empty($checkEmail)) {
                $existingUser = User::where('email', $checkEmail)->first();
            }
        }

        // 4. Fallback: Lookup by patient_id or user_id if provided in payload
        if (!$existingUser && ($request->filled('patient_id') || $request->filled('user_id'))) {
            $checkId = $request->input('patient_id') ?: $request->input('user_id');
            $existingUser = User::find($checkId);
        }

        $isRegistered = (bool) $existingUser;

        $rules = [
            'service_id' => 'required|exists:services,id',
            'booking_type' => 'nullable|in:clinic,online',
            'consultation_type' => 'nullable|in:clinic,chat,voice,video',
            'payment_method' => 'nullable|string|in:zaincash,superki,card,stripe',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|string|min:6',
        ];

        // If not recognized as registered user:
        if (!$isRegistered) {
            // If the client sent empty/null fields or an invalid/expired token, give a helpful 401 response
            if ($bearerToken || ($request->has('name') && is_null($request->input('name')) && is_null($request->input('phone')))) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم التعرف على حساب العميل المسجل. يرجى التأكد من تسجيل الدخول أولاً وإرسال رمز المصادقة (Bearer Token) الصالح في ترويسة الطلب Authorization: Bearer {token}',
                    'errors' => [
                        'auth' => ['رمز المصادقة (Bearer Token) مفقود أو غير صالح أو منتهي الصلاحية. قم بتسجيل الدخول أولاً عبر /api/login لتجديد التوكن.']
                    ]
                ], 401);
            }

            // Otherwise, require guest user info to create a new account
            $rules['name'] = 'required|string|max:255';
            $rules['phone'] = 'required|string|max:20';
            $rules['password'] = 'required|string|min:6';
        }

        $request->validate($rules);

        $bookingType = $request->booking_type ?? 'clinic';
        $consultationType = $request->consultation_type ?? ($bookingType === 'clinic' ? 'clinic' : 'video');

        // Check feature toggles from settings
        if ($bookingType === 'clinic' && Setting::get('clinic_booking_enabled', '1') === '0') {
            return response()->json(['success' => false, 'message' => 'عذراً، حجز العيادة موقوف حالياً من الإدارة.'], 422);
        }
        if ($bookingType === 'online' && Setting::get('online_booking_enabled', '1') === '0') {
            return response()->json(['success' => false, 'message' => 'عذراً، الحجز الأونلاين موقوف حالياً من الإدارة.'], 422);
        }
        if ($consultationType === 'chat' && Setting::get('chat_enabled', '1') === '0') {
            return response()->json(['success' => false, 'message' => 'عذراً، استشارات الشات موقوفة حالياً.'], 422);
        }
        if ($consultationType === 'voice' && Setting::get('voice_enabled', '1') === '0') {
            return response()->json(['success' => false, 'message' => 'عذراً، الاستشارات الصوتية موقوفة حالياً.'], 422);
        }
        if ($consultationType === 'video' && Setting::get('video_enabled', '1') === '0') {
            return response()->json(['success' => false, 'message' => 'عذراً، استشارات الفيديو موقوفة حالياً.'], 422);
        }

        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration;
        $calculatedPrice = $service->getPriceForChannel($consultationType);

        $timeString = str_replace(['ص', 'م'], ['AM', 'PM'], $request->start_time);
        $startTime = Carbon::parse(trim($timeString));
        $endTime = $startTime->copy()->addMinutes($duration);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');

        // External payment link (Gumroad fallback if service payment_url not configured)
        $defaultPaymentUrl = 'https://younisalmurshed.gumroad.com/l/srjlvw?wanted=true';
        $paymentUrl = !empty($service->payment_url) ? $service->payment_url : $defaultPaymentUrl;

        // Payment method resolution
        $payZainEnabled = Setting::get('payment_zaincash_enabled', '1') === '1';
        $paySuperkiEnabled = Setting::get('payment_superki_enabled', '1') === '1';
        $payCardEnabled = Setting::get('payment_card_enabled', '0') === '1';
        $stripeEnabled = Setting::get('stripe_enabled', '0') === '1';

        $defaultMethod = $payZainEnabled ? 'zaincash' : ($paySuperkiEnabled ? 'superki' : ($payCardEnabled ? 'card' : 'zaincash'));
        $paymentMethod = $request->input('payment_method') ?: $defaultMethod;

        return DB::transaction(function () use ($request, $service, $dateStr, $startTimeStr, $endTimeStr, $bookingType, $consultationType, $calculatedPrice, $existingUser, $isRegistered, $paymentUrl, $paymentMethod, $stripeEnabled) {
            // Check double booking
            $overlapExists = Booking::where('date', $dateStr)
                ->whereIn('status', ['AwaitingPayment', 'Confirmed', 'Completed'])
                ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                    $query->where('start_time', '<', $endTimeStr)
                          ->where('end_time', '>', $startTimeStr);
                })
                ->lockForUpdate()
                ->exists();

            if ($overlapExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'عذراً، هذا الموعد تم حجزه للتو. يرجى اختيار موعد آخر.'
                ], 422);
            }

            // Reference code
            do {
                $bookingRef = 'BK-' . strtoupper(Str::random(8));
            } while (Booking::where('booking_reference', $bookingRef)->exists());

            $patientId = $existingUser ? $existingUser->id : ($request->user() ? $request->user()->id : null);
            $tempUserData = null;

            if (!$patientId) {
                $tempUserData = [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email ?? null,
                    'password' => $request->password ?? '12345678',
                ];
            }

            // Create booking record with temp_user_data
            $booking = Booking::create([
                'booking_reference' => $bookingRef,
                'patient_id' => $patientId,
                'service_id' => $service->id,
                'booking_type' => $bookingType,
                'consultation_type' => $consultationType,
                'price' => $calculatedPrice,
                'date' => $dateStr,
                'start_time' => $startTimeStr,
                'end_time' => $endTimeStr,
                'title' => $request->title ?? $service->title,
                'notes' => $request->notes ?? null,
                'temp_user_data' => $tempUserData,
                'status' => 'AwaitingPayment',
            ]);

            $clientSecret = null;
            $paymentIntentId = null;

            if ($paymentMethod === 'stripe' && $stripeEnabled) {
                try {
                    $stripeSecret = config('services.stripe.secret');
                    if (!empty($stripeSecret) && !str_contains($stripeSecret, 'placeholder')) {
                        Stripe::setApiKey($stripeSecret);
                        $intent = PaymentIntent::create([
                            'amount' => (int) ($calculatedPrice * 100),
                            'currency' => 'usd',
                            'metadata' => [
                                'booking_reference' => $bookingRef,
                            ],
                        ]);
                        $clientSecret = $intent->client_secret;
                        $paymentIntentId = $intent->id;
                    } else {
                        $clientSecret = 'mock_secret_' . Str::random(20);
                        $paymentIntentId = 'mock_pi_' . Str::random(20);
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'فشل الاتصال ببوابة الدفع: ' . $e->getMessage()
                    ], 500);
                }
            } else {
                $paymentIntentId = $paymentMethod . '_' . Str::random(12);
            }

            $currencyCode = Setting::currencyCode();
            $currencySymbol = Setting::currencySymbol();

            // Record payment log
            Payment::create([
                'booking_id' => $booking->id,
                'payment_intent_id' => $paymentIntentId,
                'amount' => $calculatedPrice,
                'currency' => strtolower($currencyCode),
                'status' => 'Pending',
            ]);

            // Resolve QR and Instructions
            $zainQr = Setting::get('payment_zaincash_qr') ? asset('storage/' . Setting::get('payment_zaincash_qr')) : null;
            $superkiQr = Setting::get('payment_superki_qr') ? asset('storage/' . Setting::get('payment_superki_qr')) : null;
            $cardLink = Setting::get('payment_card_link', '');
            $cardInstructions = Setting::get('payment_card_instructions', '');
            $whatsappNumber = Setting::get('doctor_whatsapp', Setting::get('clinic_phone', '+9647700000000'));

            $activeQr = ($paymentMethod === 'superki') ? $superkiQr : $zainQr;
            $instructions = ($paymentMethod === 'card')
                ? $cardInstructions
                : (($paymentMethod === 'superki')
                    ? Setting::get('payment_superki_instructions', 'يرجى تحويل المبلغ عبر تطبيق سوبركي ومسح رمز الـ QR ثم إرسال الإشعار')
                    : Setting::get('payment_zaincash_instructions', 'يرجى تحويل المبلغ عبر تطبيق زين كاش ومسح رمز الـ QR ثم إرسال الإشعار'));

            $cleanWa = preg_replace('/[^0-9]/', '', $whatsappNumber);
            $waMsg = urlencode("مرحباً دكتور يونس، تم حجز موعد جديد برقم: {$bookingRef}\nالخدمة: {$service->title}\nالمبلغ: {$calculatedPrice} {$currencySymbol}\nطريقة الدفع: {$paymentMethod}\nيرجى مراجعة وتأكيد الحجز.");
            $waUrl = "https://wa.me/{$cleanWa}?text={$waMsg}";

            // Fail-safe Email notifications to Doctor and Patient
            \App\Services\NotificationMailService::notifyDoctorNewBooking($booking, 'حجز جديد عبر التطبيق');
            \App\Services\NotificationMailService::notifyPatientBookingReceived($booking);

            return response()->json([
                'success' => true,
                'booking_reference' => $bookingRef,
                'stripe_enabled' => $stripeEnabled,
                'client_secret' => $clientSecret,
                'amount' => $calculatedPrice,
                'currency' => $currencyCode,
                'currency_symbol' => $currencySymbol,
                'payment_method' => $paymentMethod,
                'qr_code' => $activeQr,
                'payment_instructions' => $instructions,
                'whatsapp_url' => $waUrl,
                'payment_url' => ($paymentMethod === 'card' && !empty($cardLink)) ? $cardLink : $paymentUrl,
                'is_registered' => $isRegistered,
                'requires_account' => !$isRegistered,
                'requires_password' => !$isRegistered,
                'account_prompt' => $isRegistered ? null : 'يرجى إضافة كلمة المرور لإنشاء حسابك ومتابعة الحجز',
                'booking' => $booking->fresh(['service', 'patient'])
            ], 201);
        });
    }

    /**
     * Confirm checkout after client payment completion (Step 2: Account Creation & Token generation)
     */
    public function confirmCheckout(Request $request)
    {
        $request->validate([
            'booking_reference' => 'required|string',
            'payment_intent_id' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $booking = Booking::where('booking_reference', $request->booking_reference)->firstOrFail();
        $payment = $booking->payment;

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على سجل الدفع لهذا الحجز.'
            ], 404);
        }

        // Process account creation & update booking to Confirmed
        $result = $this->checkoutService->confirmBookingPayment($booking, $payment, $request->password);
        $patient = $result['patient'];
        $isNewUser = $result['is_new_user'];

        // Generate Auth token for the user so client app is automatically logged in
        $token = $patient->createToken('mobile-token')->plainTextToken;

        $servicePaymentUrl = !empty($booking->service->payment_url) ? $booking->service->payment_url : Setting::get('default_payment_url', 'https://younisalmurshed.gumroad.com/l/srjlvw?wanted=true');

        // Notify patient that booking is confirmed
        \App\Services\NotificationMailService::notifyPatientBookingConfirmed($booking);

        return response()->json([
            'success' => true,
            'message' => $isNewUser ? 'تم تأكيد الحجز وإنشاء الحساب بنجاح!' : 'تم تأكيد الحجز بنجاح!',
            'is_registered' => true,
            'is_new_user' => $isNewUser,
            'requires_account' => null,
            'password_prompt' => null,
            'payment_status' => 'Paid',
            'payment_url' => $servicePaymentUrl,
            'token' => $token,
            'user' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'phone' => $patient->phone,
                'email' => $patient->email,
                'role' => $patient->role,
            ],
            'booking' => $booking->fresh(['service', 'patient', 'payment'])
        ]);
    }

    /**
     * Get Reels / Video Testimonials (TikTok / YouTube / Direct)
     */
    public function getReels()
    {
        $reels = Reel::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'reels' => $reels
        ]);
    }

    /**
     * Get patient bookings list (Filter by tabs: upcoming, completed, cancelled)
     */
    public function getPatientBookings(Request $request)
    {
        $patient = $request->user();
        $tab = $request->query('tab'); // upcoming, completed, cancelled

        $query = Booking::where('patient_id', $patient->id)
            ->with(['service', 'payment']);

        if ($tab === 'upcoming') {
            $query->whereIn('status', ['Confirmed', 'AwaitingPayment'])
                  ->where('date', '>=', Carbon::today()->format('Y-m-d'));
        } elseif ($tab === 'completed') {
            $query->where(function ($q) {
                $q->where('status', 'Completed')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'Confirmed')
                         ->where('date', '<', Carbon::today()->format('Y-m-d'));
                  });
            });
        } elseif ($tab === 'cancelled') {
            $query->whereIn('status', ['CancelledByPatient', 'CancelledByDoctor', 'Expired']);
        }

        $bookings = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'bookings' => $bookings
        ]);
    }

    /**
     * Cancel booking from mobile
     */
    public function cancelBooking(Request $request, $id)
    {
        $patient = $request->user();
        $booking = Booking::where('id', $id)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        if (!in_array($booking->status, ['AwaitingPayment', 'Confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إلغاء هذا الحجز في حالته الحالية.'
            ], 422);
        }

        // Calculate time diff
        $bookingDateTime = Carbon::parse($booking->date->format('Y-m-d') . ' ' . $booking->start_time);
        $hoursDiff = Carbon::now()->diffInHours($bookingDateTime, false);

        $payment = $booking->payment;
        $refundEligible = $hoursDiff >= 24;

        if ($booking->status === 'Confirmed' && $payment && $payment->status === 'Paid') {
            if ($refundEligible) {
                try {
                    $stripeSecret = config('services.stripe.secret');
                    if (!empty($stripeSecret) && !str_contains($stripeSecret, 'placeholder') && !str_starts_with($payment->payment_intent_id, 'mock_')) {
                        Stripe::setApiKey($stripeSecret);
                        Refund::create([
                            'payment_intent' => $payment->payment_intent_id,
                        ]);
                        $payment->update(['status' => 'RefundPending']);
                    } else {
                        // Mock refund
                        $payment->update(['status' => 'Refunded', 'refunded_amount' => $payment->amount]);
                    }
                    $booking->update(['status' => 'CancelledByPatient']);
                    return response()->json([
                        'success' => true,
                        'message' => 'تم إلغاء الموعد وطلب استرداد الأموال بنجاح.'
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'فشل الاسترداد المالي عبر Stripe: ' . $e->getMessage()
                    ], 500);
                }
            } else {
                $booking->update(['status' => 'CancelledByPatient']);
                return response()->json([
                    'success' => true,
                    'message' => 'تم إلغاء الموعد، ولكن لن يتم استرداد الرسوم لكون الإلغاء تم قبل أقل من 24 ساعة.'
                ]);
            }
        }

        // If unpaid
        $booking->update(['status' => 'CancelledByPatient']);
        if ($payment) {
            $payment->update(['status' => 'Failed']);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح.'
        ]);
    }

    /**
     * Reschedule booking from mobile API
     */
    public function rescheduleBooking(Request $request, $id)
    {
        if (Setting::get('api_enabled', '1') === '0') {
            return response()->json([
                'success' => false,
                'message' => 'خادم الـ API في حالة صيانة حالياً، يرجى المحاولة لاحقاً.'
            ], 503);
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
        ]);

        $patient = $request->user();
        $booking = Booking::where('id', $id)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        if (!in_array($booking->status, ['AwaitingPayment', 'Confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إعادة جدولة هذا الحجز في حالته الحالية.'
            ], 422);
        }

        $maxAllowed = (int) Setting::get('max_reschedule_allowed', '2');
        if ($booking->reschedule_count >= $maxAllowed) {
            return response()->json([
                'success' => false,
                'message' => "لقد استنفذت الحد الأقصى لتغيير الموعد المسموح به ({$maxAllowed} مرات)."
            ], 422);
        }

        $minNoticeHours = (int) Setting::get('min_reschedule_notice_hours', '24');
        $bookingDateTime = Carbon::parse($booking->date->format('Y-m-d') . ' ' . $booking->start_time);
        $hoursNotice = Carbon::now()->diffInHours($bookingDateTime, false);

        if ($hoursNotice < $minNoticeHours) {
            return response()->json([
                'success' => false,
                'message' => "يتوجب تعديل الموعد قبل {$minNoticeHours} ساعة من التوقيت المحجوز."
            ], 422);
        }

        $service = $booking->service;
        $duration = $service ? $service->duration : 30;

        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes($duration);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');

        // Overlap check
        $overlapExists = Booking::where('date', $dateStr)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['AwaitingPayment', 'Confirmed', 'Completed'])
            ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                $query->where('start_time', '<', $endTimeStr)
                      ->where('end_time', '>', $startTimeStr);
            })
            ->exists();

        if ($overlapExists) {
            return response()->json([
                'success' => false,
                'message' => 'الموعد المحدد غير متاح لتعارضه مع حجز آخر.'
            ], 422);
        }

        $booking->update([
            'date' => $dateStr,
            'start_time' => $startTimeStr,
            'end_time' => $endTimeStr,
            'rescheduled_at' => now(),
            'reschedule_count' => $booking->reschedule_count + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة جدولة وتغيير موعدك بنجاح!',
            'booking' => $booking->fresh(['service'])
        ]);
    }

    /**
     * Get client reviews & testimonials
     */
    public function getTestimonials(Request $request)
    {
        $limit = (int) $request->query('limit', 20);
        $rating = $request->query('rating');

        $query = Testimonial::where('is_active', true);

        if ($rating) {
            $query->where('rating', '>=', (int) $rating);
        }

        $testimonials = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->client_name_ar,
                    'name_ar' => $t->client_name_ar,
                    'name_en' => $t->client_name_en,
                    'avatar' => $t->client_avatar,
                    'rating' => (int) $t->rating,
                    'review' => $t->content_ar,
                    'content_ar' => $t->content_ar,
                    'content_en' => $t->content_en,
                    'created_at' => $t->created_at ? $t->created_at->toIso8601String() : null,
                ];
            });

        $totalCount = Testimonial::where('is_active', true)->count();
        $avgRating = round((float) Testimonial::where('is_active', true)->avg('rating'), 1);

        return response()->json([
            'success' => true,
            'total' => $testimonials->count(),
            'total_reviews' => $totalCount,
            'average_rating' => $avgRating ?: 5.0,
            'testimonials' => $testimonials,
            'reviews' => $testimonials, // alias
        ]);
    }

    /**
     * Submit a new client review
     */
    public function storeTestimonial(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
            'avatar' => 'nullable|string|url',
        ]);

        $review = Testimonial::create([
            'client_name_ar' => $request->name,
            'client_name_en' => $request->name_en ?? $request->name,
            'client_avatar' => $request->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
            'rating' => (int) $request->rating,
            'content_ar' => $request->review,
            'content_en' => $request->review_en ?? $request->review,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال تقييمك بنجاح، شكراً لثقتك بنا!',
            'review' => $review,
        ], 201);
    }
}

