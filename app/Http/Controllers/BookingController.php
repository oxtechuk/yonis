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
use Illuminate\Support\Facades\Log;
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
     * Patient Web Dashboard
     */
    public function patientDashboard(Request $request)
    {
        $user = $request->user() ?: Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $bookings = Booking::with(['service', 'payment'])
            ->where('patient_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $upcomingBookings = $bookings->filter(function ($b) {
            return in_array($b->status, ['Confirmed', 'AwaitingPayment', 'Rescheduled']) &&
                   (Carbon::parse($b->date)->isFuture() || Carbon::parse($b->date)->isToday());
        });

        $pastBookings = $bookings->where('status', 'Completed');
        $cancelledBookings = $bookings->filter(function ($b) {
            return str_contains($b->status, 'Cancelled') || $b->status === 'NoShow';
        });

        $services = Service::where('is_active', true)->get();

        return view('patient.dashboard', compact('user', 'bookings', 'upcomingBookings', 'pastBookings', 'cancelledBookings', 'services'));
    }

    /**
     * Cancel a booking by the patient (with IDOR protection)
     */
    public function cancelBooking(Request $request, $id)
    {
        $user = $request->user() ?: Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Strict IDOR Check: patient can only cancel their own booking
        $booking = Booking::where('id', $id)
            ->where('patient_id', $user->id)
            ->firstOrFail();

        if ($booking->status === 'Completed' || str_contains($booking->status, 'Cancelled')) {
            return redirect()->back()->with('error', 'لا يمكن إلغاء هذا الموعد.');
        }

        $booking->status = 'CancelledByPatient';
        $booking->save();

        Log::info("Booking ID {$booking->id} successfully cancelled by patient ID {$user->id}");

        return redirect()->back()->with('success', 'تم إلغاء الموعد بنجاح.');
    }

    /**
     * Booking Success View
     */
    public function bookingSuccess(Request $request)
    {
        $bookingRef = $request->query('ref');
        $booking = null;
        
        if ($bookingRef) {
            $booking = Booking::with(['service', 'patient', 'payment'])
                ->where('booking_reference', $bookingRef)
                ->first();
        }

        return view('booking.success', compact('booking', 'bookingRef'));
    }

    /**
     * Store a new booking (Legacy / Web fallback).
     */
    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
        ];

        if (!Auth::check()) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|string|email|max:255';
            $rules['phone'] = 'required|string|max:20';
            $rules['password'] = 'required|string|min:8';
        }

        $request->validate($rules);

        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration;

        $timeString = str_replace(['ص', 'م'], ['AM', 'PM'], $request->start_time);
        $startTime = Carbon::parse(trim($timeString));
        $endTime = $startTime->copy()->addMinutes($duration);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');

        return DB::transaction(function () use ($request, $service, $dateStr, $startTimeStr, $endTimeStr) {
            $patientId = Auth::check() ? Auth::id() : null;
            $tempUserData = null;

            if (!$patientId) {
                $tempUserData = [
                    'name' => strip_tags($request->name),
                    'phone' => strip_tags($request->phone),
                    'email' => $request->email ?? null,
                    'password' => $request->password,
                ];
            }

            // Double Booking prevention check
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
                    'message' => 'عذراً، هذا الموعد تم حجزه للتو. يرجى اختيار موعد آخر.',
                ], 422);
            }

            do {
                $bookingRef = 'BK-' . strtoupper(Str::random(8));
            } while (Booking::where('booking_reference', $bookingRef)->exists());

            $booking = Booking::create([
                'booking_reference' => $bookingRef,
                'patient_id' => $patientId,
                'service_id' => $service->id,
                'date' => $dateStr,
                'start_time' => $startTimeStr,
                'end_time' => $endTimeStr,
                'title' => $request->title ?? $service->title,
                'notes' => $request->notes ?? null,
                'temp_user_data' => $tempUserData,
                'status' => 'AwaitingPayment',
            ]);

            return response()->json([
                'success' => true,
                'booking_reference' => $bookingRef,
                'price' => $service->price,
            ]);
        });
    }
}
