<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Reel;
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
     * Get all active services
     */
    public function getServices()
    {
        $services = Service::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'services' => $services
        ]);
    }

    /**
     * Get available slots
     */
    public function getSlots(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $slots = $this->availabilityService->getAvailableSlots(
            $request->service_id,
            $request->date
        );

        return response()->json([
            'success' => true,
            'slots' => $slots
        ]);
    }

    /**
     * Get API status & configuration flags for Mobile Client
     */
    public function getApiConfig()
    {
        return response()->json([
            'success' => true,
            'config' => [
                'api_enabled' => Setting::get('api_enabled', '1') === '1',
                'clinic_booking_enabled' => Setting::get('clinic_booking_enabled', '1') === '1',
                'online_booking_enabled' => Setting::get('online_booking_enabled', '1') === '1',
                'chat_enabled' => Setting::get('chat_enabled', '1') === '1',
                'voice_enabled' => Setting::get('voice_enabled', '1') === '1',
                'video_enabled' => Setting::get('video_enabled', '1') === '1',
                'max_reschedule_allowed' => (int) Setting::get('max_reschedule_allowed', '2'),
                'min_reschedule_notice_hours' => (int) Setting::get('min_reschedule_notice_hours', '24'),
            ]
        ]);
    }

    /**
     * Initialize booking checkout (Step 1: Save temporary data & Generate Stripe Intent)
     */
    public function initializeCheckout(Request $request)
    {
        if (Setting::get('api_enabled', '1') === '0') {
            return response()->json([
                'success' => false,
                'message' => 'خادم الـ API في حالة صيانة حالياً، يرجى المحاولة لاحقاً.'
            ], 503);
        }

        $rules = [
            'service_id' => 'required|exists:services,id',
            'booking_type' => 'nullable|in:clinic,online',
            'consultation_type' => 'nullable|in:clinic,chat,voice,video',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];

        // If guest (not logged in), validate name, phone, password
        if (!$request->user()) {
            $rules['name'] = 'required|string|max:255';
            $rules['phone'] = 'required|string|max:20';
            $rules['password'] = 'required|string|min:6';
            $rules['email'] = 'nullable|email|max:255';
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

        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes($duration);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');

        return DB::transaction(function () use ($request, $service, $dateStr, $startTimeStr, $endTimeStr, $bookingType, $consultationType, $calculatedPrice) {
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

            $patientId = $request->user() ? $request->user()->id : null;
            $tempUserData = null;

            if (!$patientId) {
                $tempUserData = [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email ?? null,
                    'password' => $request->password,
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

            // Create Stripe PaymentIntent
            $clientSecret = null;
            $paymentIntentId = null;

            try {
                $stripeSecret = config('services.stripe.secret');
                if (!empty($stripeSecret) && !str_contains($stripeSecret, 'placeholder')) {
                    Stripe::setApiKey($stripeSecret);
                    $intent = PaymentIntent::create([
                        'amount' => (int) ($calculatedPrice * 100), // amount in cents/halalas
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
                    'message' => 'فشل الاتصال ببوابة الدفع Stripe: ' . $e->getMessage()
                ], 500);
            }

            // Record payment log
            Payment::create([
                'booking_id' => $booking->id,
                'payment_intent_id' => $paymentIntentId,
                'amount' => $calculatedPrice,
                'currency' => 'usd',
                'status' => 'Pending',
            ]);

            return response()->json([
                'success' => true,
                'booking_reference' => $bookingRef,
                'client_secret' => $clientSecret,
                'amount' => $calculatedPrice,
                'booking' => $booking
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
        $patient = $this->checkoutService->confirmBookingPayment($booking, $payment);

        // Generate Auth token for the user so client app is automatically logged in
        $token = $patient->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تأكيد الحجز وإنشاء الحساب بنجاح!',
            'token' => $token,
            'user' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'phone' => $patient->phone,
                'email' => $patient->email,
                'role' => $patient->role,
            ],
            'booking' => $booking->fresh(['service', 'patient'])
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
}
