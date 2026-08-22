<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class BookingController extends Controller
{
    protected AvailabilityService $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Get available slots for a specific service and date.
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

        return response()->json($slots);
    }

    /**
     * Store a new booking and generate Stripe PaymentIntent.
     */
    public function store(Request $request)
    {
        // 1. Validation rules
        $rules = [
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
        ];

        // If user is guest, we require registration fields
        if (!Auth::check()) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|string|email|max:255';
            $rules['phone'] = 'required|string|max:20';
            $rules['password'] = 'required|string|min:8';
        }

        $request->validate($rules);

        // 2. Fetch service details
        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration;

        // Calculate start and end times
        $timeString = str_replace(['ص', 'م'], ['AM', 'PM'], $request->start_time);
        $startTime = Carbon::parse(trim($timeString));
        $endTime = $startTime->copy()->addMinutes($duration);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');

        // Start DB Transaction to prevent race conditions (Double Booking)
        return DB::transaction(function () use ($request, $service, $dateStr, $startTimeStr, $endTimeStr, $duration) {
            
            $patientId = null;
            $tempUserData = null;

            if (Auth::check()) {
                $patientId = Auth::id();
            } else {
                $tempUserData = [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email ?? null,
                    'password' => $request->password,
                ];
            }

            // 3. Double Booking prevention check (Lock table for writing)
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
                return response()->json([
                    'message' => 'عذراً، هذا الموعد تم حجزه للتو من قِبل مستخدم آخر. يرجى اختيار موعد آخر.',
                ], 422);
            }

            // 4. Generate unique booking reference
            do {
                $bookingRef = 'BK-' . strtoupper(Str::random(8));
            } while (Booking::where('booking_reference', $bookingRef)->exists());

            // 5. Create Booking record
            $booking = Booking::create([
                'booking_reference' => $bookingRef,
                'patient_id' => $patientId,
                'service_id' => $service->id,
                'date' => $dateStr,
                'start_time' => $startTimeStr,
                'end_time' => $endTimeStr,
                'title' => $request->title ?? $service->name,
                'notes' => $request->notes ?? null,
                'temp_user_data' => $tempUserData,
                'status' => 'AwaitingPayment',
            ]);

            // 6. Create Stripe PaymentIntent
            $clientSecret = null;
            $paymentIntentId = null;

            try {
                $stripeSecret = config('services.stripe.secret');
                if (!empty($stripeSecret) && !str_contains($stripeSecret, 'placeholder')) {
                    Stripe::setApiKey($stripeSecret);
                    $intent = PaymentIntent::create([
                        'amount' => (int) ($service->price * 100), // amount in cents
                        'currency' => 'usd',
                        'metadata' => [
                            'booking_reference' => $bookingRef,
                        ],
                    ]);
                    $clientSecret = $intent->client_secret;
                    $paymentIntentId = $intent->id;
                } else {
                    // For local development when keys are not yet configured
                    $clientSecret = 'mock_secret_' . Str::random(20);
                    $paymentIntentId = 'mock_pi_' . Str::random(20);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'فشل الاتصال ببوابة الدفع Stripe: ' . $e->getMessage(),
                ], 500);
            }

            // 7. Create Payment record
            Payment::create([
                'booking_id' => $booking->id,
                'payment_intent_id' => $paymentIntentId,
                'amount' => $service->price,
                'currency' => 'usd',
                'status' => 'Pending',
            ]);

            return response()->json([
                'success' => true,
                'booking_reference' => $bookingRef,
                'client_secret' => $clientSecret,
                'price' => $service->price,
            ]);
        });
    }
}
