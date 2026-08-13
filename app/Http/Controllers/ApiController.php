<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiController extends Controller
{
    protected AvailabilityService $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Mobile login API
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.'
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
     * Mobile register API
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
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
     * Create booking from mobile
     */
    public function createBooking(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'payment_intent_id' => 'required|string', // generated from client-side stripe flow
        ]);

        $patient = $request->user();
        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration;

        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes($duration);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');

        return DB::transaction(function () use ($request, $patient, $service, $dateStr, $startTimeStr, $endTimeStr) {
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

            // Create booking
            $booking = Booking::create([
                'booking_reference' => $bookingRef,
                'patient_id' => $patient->id,
                'service_id' => $service->id,
                'date' => $dateStr,
                'start_time' => $startTimeStr,
                'end_time' => $endTimeStr,
                'status' => 'Confirmed', // marked as Confirmed since mobile client paid via Stripe PaymentSheet before calling this
            ]);

            // Create payment logs
            Payment::create([
                'booking_id' => $booking->id,
                'payment_intent_id' => $request->payment_intent_id,
                'amount' => $service->price,
                'currency' => 'usd',
                'status' => 'Paid', // marked as Paid directly
            ]);

            return response()->json([
                'success' => true,
                'booking_reference' => $bookingRef,
                'booking' => $booking
            ], 201);
        });
    }

    /**
     * Get patient bookings list
     */
    public function getPatientBookings(Request $request)
    {
        $patient = $request->user();
        $bookings = Booking::where('patient_id', $patient->id)
            ->with(['service', 'payment'])
            ->orderBy('date', 'desc')
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
}
