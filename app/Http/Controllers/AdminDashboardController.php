<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Service;
use App\Models\Availability;
use App\Models\BlockedTime;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Reel;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Refund;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard Statistics
     */
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');

        $stats = [
            'today_bookings' => Booking::where('date', $today)->count(),
            'upcoming_bookings' => Booking::where('date', '>=', $today)->whereIn('status', ['Confirmed', 'AwaitingPayment'])->count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'completed' => Booking::where('status', 'Completed')->count(),
            'revenue' => Payment::where('status', 'Paid')->sum('amount'),
        ];

        $todayAppointments = Booking::where('date', $today)
            ->with(['patient', 'service', 'payment'])
            ->orderBy('start_time', 'asc')
            ->get();

        // Also pass all active patients and services for the quick booking modal in Dashboard
        $allPatients = User::where('role', 'patient')->orderBy('name', 'asc')->get();
        $allServices = Service::where('is_active', true)->get();

        return view('admin.dashboard', compact('stats', 'todayAppointments', 'allPatients', 'allServices'));
    }

    /**
     * Booking management
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['patient', 'service', 'payment'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('booking_reference', 'like', "%{$search}%");
        }

        $bookings = $query->paginate(15);
        $allPatients = User::where('role', 'patient')->orderBy('name', 'asc')->get();
        $allServices = Service::where('is_active', true)->get();

        return view('admin.bookings', compact('bookings', 'allPatients', 'allServices'));
    }

    /**
     * Update booking status (Completed, NoShow, CancelledByDoctor)
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Confirmed,Completed,NoShow,CancelledByDoctor',
        ]);

        $booking = Booking::findOrFail($id);
        $oldStatus = $booking->status;
        $newStatus = $request->status;

        if ($newStatus === 'CancelledByDoctor' && $oldStatus !== 'CancelledByDoctor') {
            // Process refund if patient paid
            $payment = $booking->payment;
            if ($payment && $payment->status === 'Paid') {
                try {
                    $stripeSecret = config('services.stripe.secret');
                    if (!empty($stripeSecret) && !str_contains($stripeSecret, 'placeholder') && !str_starts_with($payment->payment_intent_id, 'mock_')) {
                        Stripe::setApiKey($stripeSecret);
                        Refund::create([
                            'payment_intent' => $payment->payment_intent_id,
                        ]);
                        $payment->update(['status' => 'Refunded', 'refunded_amount' => $payment->amount]);
                    } else {
                        // Mock refund for local
                        $payment->update(['status' => 'Refunded', 'refunded_amount' => $payment->amount]);
                    }
                } catch (\Exception $e) {
                    Log::error('Stripe Refund Error: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'فشل في إرجاع المبلغ مالياً عبر Stripe: ' . $e->getMessage());
                }
            }
        }

        $booking->update(['status' => $newStatus]);
        
        // Log Notification action
        if ($newStatus === 'CancelledByDoctor' && Setting::get('notify_cancellation') === '1') {
            Log::info("Email Notification: Booking {$booking->booking_reference} cancelled by Doctor. Email sent to {$booking->patient->email}");
        }

        return redirect()->back()->with('success', 'تم تحديث حالة الحجز بنجاح.');
    }

    /**
     * Reschedule booking date & time by Admin
     */
    public function rescheduleBooking(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
        ]);

        $booking = Booking::findOrFail($id);
        $service = $booking->service;
        $duration = $service ? $service->duration : 30;

        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes($duration);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');

        // Check for double booking overlap excluding current booking
        $overlapExists = Booking::where('date', $dateStr)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['AwaitingPayment', 'Confirmed', 'Completed'])
            ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                $query->where('start_time', '<', $endTimeStr)
                      ->where('end_time', '>', $startTimeStr);
            })
            ->exists();

        if ($overlapExists) {
            return redirect()->back()->with('error', 'عذراً، هذا الموعد الجديد يتعارض مع حجز آخر.');
        }

        $booking->update([
            'date' => $dateStr,
            'start_time' => $startTimeStr,
            'end_time' => $endTimeStr,
            'rescheduled_at' => now(),
            'reschedule_count' => $booking->reschedule_count + 1,
        ]);

        Log::info("Booking {$booking->booking_reference} rescheduled by Admin to {$dateStr} {$startTimeStr}");

        return redirect()->back()->with('success', 'تم تعديل موعد الحجز وإعادة جدولته بنجاح.');
    }

    /**
     * Store manual booking created by Admin/Doctor
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'booking_type' => 'nullable|in:clinic,online',
            'consultation_type' => 'nullable|in:clinic,chat,voice,video',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'payment_status' => 'required|in:Paid,Pending',
        ]);

        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration;

        $bookingType = $request->booking_type ?? 'clinic';
        $consultationType = $request->consultation_type ?? ($bookingType === 'clinic' ? 'clinic' : 'video');
        $calculatedPrice = $service->getPriceForChannel($consultationType);

        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes($duration);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');

        return DB::transaction(function () use ($request, $service, $dateStr, $startTimeStr, $endTimeStr, $bookingType, $consultationType, $calculatedPrice) {
            
            // Double booking prevention check
            $overlapExists = Booking::where('date', $dateStr)
                ->whereIn('status', ['AwaitingPayment', 'Confirmed', 'Completed'])
                ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                    $query->where(function ($q) use ($startTimeStr, $endTimeStr) {
                        $q->where('start_time', '<', $endTimeStr)
                          ->where('end_time', '>', $startTimeStr);
                    });
                })
                ->lockForUpdate()
                ->exists();

            if ($overlapExists) {
                return redirect()->back()->with('error', 'عذراً، هذا الموعد يتعارض مع حجز قائم بالفعل.');
            }

            // Generate booking reference
            do {
                $bookingRef = 'BK-' . strtoupper(Str::random(8));
            } while (Booking::where('booking_reference', $bookingRef)->exists());

            // Create Booking
            $booking = Booking::create([
                'booking_reference' => $bookingRef,
                'patient_id' => $request->patient_id,
                'service_id' => $service->id,
                'booking_type' => $bookingType,
                'consultation_type' => $consultationType,
                'price' => $calculatedPrice,
                'date' => $dateStr,
                'start_time' => $startTimeStr,
                'end_time' => $endTimeStr,
                'status' => 'Confirmed', // Confirmed directly by admin
            ]);

            // Create Payment
            Payment::create([
                'booking_id' => $booking->id,
                'payment_intent_id' => 'cash_clinic_' . Str::random(10),
                'amount' => $calculatedPrice,
                'currency' => 'usd',
                'status' => $request->payment_status,
            ]);

            // Log Notification action
            if (Setting::get('notify_new_booking') === '1') {
                $patient = User::find($request->patient_id);
                Log::info("Email Notification: New Booking {$bookingRef} created manually. Email sent to {$patient->email}");
            }

            return redirect()->back()->with('success', 'تم تسجيل الحجز يدوياً بنجاح.');
        });
    }

    /**
     * Patients list
     */
    public function patients(Request $request)
    {
        $query = User::where('role', 'patient')
            ->withCount('bookings')
            ->orderBy('bookings_count', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->paginate(15);
        return view('admin.patients', compact('patients'));
    }

    /**
     * Store new Patient manually by Admin/Doctor
     */
    public function storePatient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'patient',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'تم إضافة حساب المريض يدوياً بنجاح.');
    }

    /**
     * Patient history details
     */
    public function patientDetails($id)
    {
        $patient = User::where('role', 'patient')->findOrFail($id);
        $bookings = Booking::where('patient_id', $id)
            ->with(['service', 'payment'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('admin.patient_details', compact('patient', 'bookings'));
    }

    /**
     * Payments logs list
     */
    public function payments(Request $request)
    {
        $query = Payment::with(['booking.patient', 'booking.service'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(15);
        return view('admin.payments', compact('payments'));
    }

    /**
     * Manage Services
     */
    public function services()
    {
        $services = Service::all();
        return view('admin.services', compact('services'));
    }

    /**
     * Store new Service
     */
    public function storeService(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|in:clinic,online,both',
            'price' => 'required|numeric|min:0',
            'clinic_price' => 'nullable|numeric|min:0',
            'chat_price' => 'nullable|numeric|min:0',
            'voice_price' => 'nullable|numeric|min:0',
            'video_price' => 'nullable|numeric|min:0',
            'duration' => 'required|integer|min:5',
        ]);

        Service::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type ?? 'both',
            'price' => $request->price,
            'clinic_price' => $request->clinic_price ?? $request->price,
            'chat_price' => $request->chat_price ?? $request->price,
            'voice_price' => $request->voice_price ?? $request->price,
            'video_price' => $request->video_price ?? $request->price,
            'duration' => $request->duration,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'تم إضافة الخدمة وتخصيص أسعار القنوات بنجاح.');
    }

    /**
     * Update existing Service
     */
    public function updateService(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|in:clinic,online,both',
            'price' => 'required|numeric|min:0',
            'clinic_price' => 'nullable|numeric|min:0',
            'chat_price' => 'nullable|numeric|min:0',
            'voice_price' => 'nullable|numeric|min:0',
            'video_price' => 'nullable|numeric|min:0',
            'duration' => 'required|integer|min:5',
        ]);

        $service = Service::findOrFail($id);
        $service->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type ?? 'both',
            'price' => $request->price,
            'clinic_price' => $request->clinic_price ?? $request->price,
            'chat_price' => $request->chat_price ?? $request->price,
            'voice_price' => $request->voice_price ?? $request->price,
            'video_price' => $request->video_price ?? $request->price,
            'duration' => $request->duration,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'تم تحديث بيانات الخدمة وأسعار القنوات بنجاح.');
    }

    /**
     * API Control Settings View
     */
    public function apiControl()
    {
        $settings = [
            'api_enabled' => Setting::get('api_enabled', '1'),
            'clinic_booking_enabled' => Setting::get('clinic_booking_enabled', '1'),
            'online_booking_enabled' => Setting::get('online_booking_enabled', '1'),
            'chat_enabled' => Setting::get('chat_enabled', '1'),
            'voice_enabled' => Setting::get('voice_enabled', '1'),
            'video_enabled' => Setting::get('video_enabled', '1'),
            'max_reschedule_allowed' => Setting::get('max_reschedule_allowed', '2'),
            'min_reschedule_notice_hours' => Setting::get('min_reschedule_notice_hours', '24'),
        ];

        // Fetch Tokens
        $tokens = DB::table('personal_access_tokens')
            ->join('users', 'personal_access_tokens.tokenable_id', '=', 'users.id')
            ->select('personal_access_tokens.*', 'users.name as user_name', 'users.email as user_email', 'users.phone as user_phone')
            ->orderBy('personal_access_tokens.created_at', 'desc')
            ->limit(20)
            ->get();

        $stats = [
            'total_tokens' => DB::table('personal_access_tokens')->count(),
            'total_api_bookings' => Booking::count(),
            'online_bookings' => Booking::where('booking_type', 'online')->count(),
            'clinic_bookings' => Booking::where('booking_type', 'clinic')->count(),
            'chat_bookings' => Booking::where('consultation_type', 'chat')->count(),
            'voice_bookings' => Booking::where('consultation_type', 'voice')->count(),
            'video_bookings' => Booking::where('consultation_type', 'video')->count(),
        ];

        return view('admin.api_control', compact('settings', 'tokens', 'stats'));
    }

    /**
     * Update API Control Settings
     */
    public function updateApiControl(Request $request)
    {
        $request->validate([
            'max_reschedule_allowed' => 'required|integer|min:0',
            'min_reschedule_notice_hours' => 'required|integer|min:0',
        ]);

        Setting::set('api_enabled', $request->has('api_enabled') ? '1' : '0');
        Setting::set('clinic_booking_enabled', $request->has('clinic_booking_enabled') ? '1' : '0');
        Setting::set('online_booking_enabled', $request->has('online_booking_enabled') ? '1' : '0');
        Setting::set('chat_enabled', $request->has('chat_enabled') ? '1' : '0');
        Setting::set('voice_enabled', $request->has('voice_enabled') ? '1' : '0');
        Setting::set('video_enabled', $request->has('video_enabled') ? '1' : '0');
        Setting::set('max_reschedule_allowed', (string) $request->max_reschedule_allowed);
        Setting::set('min_reschedule_notice_hours', (string) $request->min_reschedule_notice_hours);

        return redirect()->back()->with('success', 'تم حفظ وتطبيق إعدادات الـ API بنجاح.');
    }

    /**
     * Revoke personal access token
     */
    public function revokeToken($id)
    {
        DB::table('personal_access_tokens')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'تم إلغاء رمـز الـ Token بنجاح.');
    }

    /**
     * Manage availability schedules & blocked times
     */
    public function availability()
    {
        $availabilities = Availability::orderBy('day_of_week', 'asc')->get();
        $blockedTimes = BlockedTime::where('date', '>=', today()->format('Y-m-d'))
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.availability', compact('availabilities', 'blockedTimes'));
    }

    /**
     * Save weekly schedules
     */
    public function updateAvailability(Request $request)
    {
        $request->validate([
            'days' => 'required|array',
            'days.*.start_time' => 'required|string',
            'days.*.end_time' => 'required|string',
        ]);

        foreach ($request->days as $day => $times) {
            if (isset($times['active']) && $times['active'] == '1') {
                Availability::updateOrCreate(
                    ['day_of_week' => $day],
                    [
                        'start_time' => $times['start_time'],
                        'end_time' => $times['end_time']
                    ]
                );
            } else {
                Availability::where('day_of_week', $day)->delete();
            }
        }

        return redirect()->back()->with('success', 'تم حفظ مواعيد العمل الأسبوعية بنجاح.');
    }

    /**
     * Add off day / Block time
     */
    public function storeBlockedTime(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'reason' => 'nullable|string|max:255',
        ]);

        BlockedTime::create([
            'date' => $request->date,
            'start_time' => $request->start_time ?: null,
            'end_time' => $request->end_time ?: null,
            'reason' => $request->reason,
        ]);

        return redirect()->back()->with('success', 'تم تسجيل الإجازة / وقت الحظر بنجاح.');
    }

    /**
     * Delete blocked time
     */
    public function deleteBlockedTime($id)
    {
        $blocked = BlockedTime::findOrFail($id);
        $blocked->delete();
        return redirect()->back()->with('success', 'تم حذف موعد الحظر/الإجازة بنجاح.');
    }

    /**
     * Show Doctor Portfolio & Content Editor
     */
    public function portfolio()
    {
        $doctor = Auth::user();
        $profile = DoctorProfile::firstOrCreate(['user_id' => $doctor->id]);
        $reels = Reel::latest()->get();
        $testimonials = Testimonial::latest()->get();
        return view('admin.portfolio', compact('profile', 'reels', 'testimonials'));
    }

    /**
     * Update Doctor Portfolio
     */
    public function updatePortfolio(Request $request)
    {
        $doctor = Auth::user();
        $profile = DoctorProfile::where('user_id', $doctor->id)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'bio' => 'required|string',
            'bio_en' => 'nullable|string',
            'education' => 'nullable|array',
            'experience' => 'nullable|array',
            'certificates' => 'nullable|array',
            'specialties' => 'nullable|array',
            'specialties_en' => 'nullable|array',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'hero_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'about_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'site_logo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        // Filter empty values from arrays
        $education = array_values(array_filter($request->education ?? []));
        $experience = array_values(array_filter($request->experience ?? []));
        $certificates = array_values(array_filter($request->certificates ?? []));
        $specialties = array_values(array_filter($request->specialties ?? []));
        $specialties_en = array_values(array_filter($request->specialties_en ?? []));

        $social_links = [
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'instagram' => $request->instagram,
            'linkedin' => $request->linkedin,
        ];

        // Hero Image Upload
        $heroImage = $profile->hero_image;
        if ($request->hasFile('hero_image_file')) {
            $path = $request->file('hero_image_file')->store('branding', 'public');
            $heroImage = asset('storage/' . $path);
        }

        // About Image Upload
        $aboutImage = $profile->about_image;
        if ($request->hasFile('about_image_file')) {
            $path = $request->file('about_image_file')->store('branding', 'public');
            $aboutImage = asset('storage/' . $path);
        }

        // Site Logo Upload
        if ($request->hasFile('site_logo_file')) {
            $path = $request->file('site_logo_file')->store('branding', 'public');
            Setting::set('site_logo', asset('storage/' . $path));
        }

        // Handle gallery image uploads
        $gallery = $profile->gallery ?? [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $path = $file->store('gallery', 'public');
                $gallery[] = asset('storage/' . $path);
            }
        }

        $profile->update([
            'title' => $request->title,
            'title_en' => $request->title_en ?: null,
            'bio' => $request->bio,
            'bio_en' => $request->bio_en ?: null,
            'hero_image' => $heroImage,
            'about_image' => $aboutImage,
            'education' => $education,
            'experience' => $experience,
            'certificates' => $certificates,
            'specialties' => $specialties,
            'specialties_en' => $specialties_en,
            'social_links' => $social_links,
            'gallery' => $gallery,
        ]);

        return redirect()->back()->with('success', 'تم تحديث بيانات الصفحة والعدسات بنجاح.');
    }

    /**
     * Delete image from gallery
     */
    public function deleteGalleryImage(Request $request)
    {
        $request->validate(['image_url' => 'required|string']);

        $doctor = Auth::user();
        $profile = DoctorProfile::where('user_id', $doctor->id)->firstOrFail();
        $gallery = $profile->gallery ?? [];

        $gallery = array_values(array_filter($gallery, function ($url) use ($request) {
            return $url !== $request->image_url;
        }));

        if (str_contains($request->image_url, '/storage/gallery/')) {
            $filename = basename($request->image_url);
            Storage::disk('public')->delete('gallery/' . $filename);
        }

        $profile->update(['gallery' => $gallery]);

        return response()->json(['success' => true]);
    }

    /**
     * Store new Video Reel
     */
    public function storeReel(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'video_url' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'duration' => 'nullable|integer',
        ]);

        $videoUrl = $request->video_url;
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('reels', 'public');
            $videoUrl = asset('storage/' . $path);
        }

        $thumbnailUrl = 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=500&q=80';
        if ($request->hasFile('thumbnail_file')) {
            $path = $request->file('thumbnail_file')->store('reels/thumbnails', 'public');
            $thumbnailUrl = asset('storage/' . $path);
        }

        Reel::create([
            'title' => $request->title,
            'title_en' => $request->title_en ?: null,
            'video_url' => $videoUrl ?: '#',
            'thumbnail_url' => $thumbnailUrl,
            'duration' => $request->duration ?: 60,
            'platform' => 'Internal',
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'تم إضافة الفيديوه التوعوي بنجاح.');
    }

    /**
     * Delete Video Reel
     */
    public function deleteReel($id)
    {
        $reel = Reel::findOrFail($id);
        $reel->delete();
        return redirect()->back()->with('success', 'تم حذف الفيديو بنجاح.');
    }

    /**
     * Store new Testimonial
     */
    public function storeTestimonial(Request $request)
    {
        $request->validate([
            'client_name_ar' => 'required|string|max:255',
            'client_name_en' => 'nullable|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $avatarUrl = null;
        if ($request->hasFile('avatar_file')) {
            $path = $request->file('avatar_file')->store('testimonials', 'public');
            $avatarUrl = asset('storage/' . $path);
        }

        Testimonial::create([
            'client_name_ar' => $request->client_name_ar,
            'client_name_en' => $request->client_name_en ?: null,
            'content_ar' => $request->content_ar,
            'content_en' => $request->content_en ?: null,
            'rating' => $request->rating,
            'client_avatar' => $avatarUrl,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'تم إضافة رأي العميل بنجاح.');
    }

    /**
     * Delete Testimonial
     */
    public function deleteTestimonial($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();
        return redirect()->back()->with('success', 'تم حذف رأي العميل بنجاح.');
    }

    /**
     * Settings Page View
     */
    public function settings()
    {
        $settings = [
            'site_title' => Setting::get('site_title', 'إدارة العيادة'),
            'doctor_name' => Setting::get('doctor_name', 'يونس المرشد'),
            'site_logo' => Setting::get('site_logo', ''),
            'primary_color' => Setting::get('primary_color', '#3B52A4'),
            'secondary_color' => Setting::get('secondary_color', '#1e3a8a'),
            'google_site_verification' => Setting::get('google_site_verification', ''),
            'meta_description' => Setting::get('meta_description', 'احجز استشارتك النفسية الآن مع المعالج يونس المرشد. جلسات فردية وزوجية وأسرية بخبرة أكثر من 10 سنوات.'),
            'meta_keywords' => Setting::get('meta_keywords', 'معالج نفسي, استشارة نفسية, علاج نفسي, يونس المرشد, حجز موعد نفسي, اكتئاب, قلق, علاج زوجي'),
            'og_image' => Setting::get('og_image', ''),
            'google_analytics_id' => Setting::get('google_analytics_id', ''),
            'meta_pixel_id' => Setting::get('meta_pixel_id', ''),
            'notify_new_booking' => Setting::get('notify_new_booking', '1'),
            'notify_cancellation' => Setting::get('notify_cancellation', '1'),
        ];
        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings handler
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_title' => 'nullable|string|max:255',
            'doctor_name' => 'nullable|string|max:255',
            'site_logo' => 'nullable|string',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'google_site_verification' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|string',
            'og_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'google_analytics_id' => 'nullable|string|max:50',
            'meta_pixel_id' => 'nullable|string|max:50',
        ]);

        // Handle logo file upload
        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('branding', 'public');
            Setting::set('site_logo', asset('storage/' . $path));
        } elseif ($request->filled('site_logo')) {
            Setting::set('site_logo', $request->site_logo);
        }

        // Handle OG image file upload
        if ($request->hasFile('og_image_file')) {
            $path = $request->file('og_image_file')->store('branding', 'public');
            Setting::set('og_image', asset('storage/' . $path));
        } elseif ($request->filled('og_image')) {
            Setting::set('og_image', $request->og_image);
        }

        if ($request->filled('site_title')) Setting::set('site_title', $request->site_title);
        if ($request->filled('doctor_name')) Setting::set('doctor_name', $request->doctor_name);
        if ($request->filled('primary_color')) Setting::set('primary_color', $request->primary_color);
        if ($request->filled('secondary_color')) Setting::set('secondary_color', $request->secondary_color);
        Setting::set('google_site_verification', $request->google_site_verification ?? '');
        Setting::set('meta_description', $request->meta_description ?? '');
        Setting::set('meta_keywords', $request->meta_keywords ?? '');
        Setting::set('google_analytics_id', $request->google_analytics_id ?? '');
        Setting::set('meta_pixel_id', $request->meta_pixel_id ?? '');
        Setting::set('notify_new_booking', $request->has('notify_new_booking') ? '1' : '0');
        Setting::set('notify_cancellation', $request->has('notify_cancellation') ? '1' : '0');

        return redirect()->back()->with('success', 'تم حفظ جميع إعدادات المنصة، الهوية البصرية، والـ SEO بنجاح!');
    }

    /**
     * Bookings Calendar View Page
     */
    public function calendar()
    {
        return view('admin.calendar');
    }

    /**
     * Get Bookings Calendar Events API
     */
    public function getCalendarEvents(Request $request)
    {
        $bookings = Booking::with(['patient', 'service'])
            ->whereIn('status', ['Confirmed', 'Completed', 'AwaitingPayment'])
            ->get();

        $events = [];
        foreach ($bookings as $b) {
            $color = '#0d9488'; // Confirmed = Teal
            if ($b->status === 'Completed') {
                $color = '#64748b'; // Completed = Gray
            } elseif ($b->status === 'AwaitingPayment') {
                $color = '#eab308'; // AwaitingPayment = Yellow
            }

            $events[] = [
                'id' => $b->id,
                'title' => $b->patient->name . ' - ' . $b->service->title,
                'start' => $b->date->format('Y-m-d') . 'T' . $b->start_time,
                'end' => $b->date->format('Y-m-d') . 'T' . $b->end_time,
                'color' => $color,
                'extendedProps' => [
                    'patient_id' => $b->patient_id,
                    'patient_name' => $b->patient->name,
                    'patient_phone' => $b->patient->phone,
                    'service_title' => $b->service->title,
                    'status' => $b->status,
                    'price' => $b->service->price,
                    'reference' => $b->booking_reference,
                ]
            ];
        }

        return response()->json($events);
    }
}
