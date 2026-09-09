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
use App\Services\NotificationMailService;

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
            return in_array($b->status, ['Confirmed', 'AwaitingPayment', 'PendingPaymentReview', 'Pending', 'Rescheduled']) &&
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
     * Store a new booking (Modal / Web submission).
     */
    public function store(Request $request)
    {
        // Support slot as alias for start_time
        if (!$request->has('start_time') && $request->has('slot')) {
            $request->merge(['start_time' => $request->input('slot')]);
        }

        $rawStartTime = $request->input('start_time');
        if (is_array($rawStartTime)) {
            $rawStartTime = $rawStartTime['start'] ?? $rawStartTime['time_formatted'] ?? reset($rawStartTime);
            $request->merge(['start_time' => $rawStartTime]);
        }

        $rawDate = $request->input('date');
        if (is_array($rawDate)) {
            $rawDate = $rawDate['date'] ?? reset($rawDate);
            $request->merge(['date' => $rawDate]);
        }

        if ($request->input('start_time') === '[object Object]' || empty($request->input('start_time'))) {
            return response()->json(['success' => false, 'message' => 'يرجى اختيار توقيت متاح للجلسة.'], 422);
        }

        if ($request->input('date') === '[object Object]' || empty($request->input('date'))) {
            return response()->json(['success' => false, 'message' => 'يرجى اختيار تاريخ صالح للجلسة.'], 422);
        }

        // Validation rules
        $rules = [
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'booking_type' => 'nullable|in:online,clinic',
            'payment_method' => 'nullable|string',
            'transfer_number' => 'nullable|string',
        ];

        if (!Auth::check()) {
            $rules['name'] = 'required|string|max:255';
            $rules['phone'] = 'required|string|max:20';
            $rules['email'] = 'nullable|string|email|max:255';
            $rules['password'] = 'nullable|string|min:6';
        }

        $request->validate($rules);

        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration;

        $timeString = str_replace(['ص', 'م'], ['AM', 'PM'], (string)$request->start_time);
        try {
            $startTime = Carbon::parse(trim($timeString));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'تنسيق الوقت غير صالح.'], 422);
        }

        try {
            $dateStr = Carbon::parse($request->date)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'تنسيق التاريخ غير صالح.'], 422);
        }

        $endTime = $startTime->copy()->addMinutes($duration);
        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');

        return DB::transaction(function () use ($request, $service, $dateStr, $startTimeStr, $endTimeStr) {
            $patient = Auth::user();

            if (!$patient) {
                $phone = $request->phone;
                $email = $request->email;
                $name = $request->name ?: 'عميل جديد';
                $plainPassword = $request->password ?: '12345678';

                // Look up by phone if exists
                if (!empty($phone)) {
                    $digits = preg_replace('/\D/', '', $phone);
                    $last9 = strlen($digits) >= 9 ? substr($digits, -9) : $digits;
                    $patient = User::where('phone', $phone)
                        ->orWhere('phone', '+' . $digits)
                        ->orWhere('phone', 'like', '%' . $last9)
                        ->first();
                }
                if (!$patient && !empty($email)) {
                    $patient = User::where('email', $email)->first();
                }

                if (!$patient) {
                    $userEmail = !empty($email) ? $email : ('patient_' . preg_replace('/\D/', '', (string)$phone) . '@yonis-app.com');
                    $patient = User::create([
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $userEmail,
                        'password' => Hash::make($plainPassword),
                        'role' => 'patient',
                    ]);
                }

                // Log patient in
                try {
                    Auth::login($patient, true);
                } catch (\Throwable $e) {}
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

            // Handle Receipt image upload
            $receiptPath = null;
            if ($request->hasFile('receipt_image')) {
                $file = $request->file('receipt_image');
                $filename = 'receipt_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('receipts', $filename, 'public');
                $receiptPath = 'storage/' . $path;
            } elseif ($request->filled('receipt_image') && is_string($request->input('receipt_image'))) {
                $receiptPath = $request->input('receipt_image');
            }

            $bookingType = $request->input('booking_type') ?: ($service->type === 'clinic' ? 'clinic' : 'online');
            $consultationType = $bookingType === 'clinic' ? 'clinic' : ($service->getChannelType() !== 'all' ? $service->getChannelType() : 'video');
            $paymentMethod = $request->input('payment_method') ?: 'zaincash';
            $transferNumber = $request->input('transfer_number') ?: ($patient ? $patient->phone : null);
            $bookingStatus = (!empty($receiptPath) || in_array($paymentMethod, ['zaincash', 'superki'])) ? 'PendingPaymentReview' : 'AwaitingPayment';

            $price = $service->getPriceForChannel($consultationType);

            $booking = Booking::create([
                'booking_reference' => $bookingRef,
                'patient_id' => $patient ? $patient->id : null,
                'service_id' => $service->id,
                'booking_type' => $bookingType,
                'consultation_type' => $consultationType,
                'price' => $price,
                'date' => $dateStr,
                'start_time' => $startTimeStr,
                'end_time' => $endTimeStr,
                'title' => $request->title ?? $service->title,
                'notes' => $request->notes ?? null,
                'temp_user_data' => null,
                'status' => $bookingStatus,
                'payment_method' => $paymentMethod,
                'transfer_number' => $transferNumber,
                'receipt_image' => $receiptPath,
            ]);

            // Notify Doctor and Patient
            NotificationMailService::notifyDoctorNewBooking($booking, 'طلب حجز جديد');
            NotificationMailService::notifyPatientBookingReceived($booking);

            $txRef = 'TX-' . strtoupper(Str::random(10));

            return response()->json([
                'success' => true,
                'booking_reference' => $bookingRef,
                'reference' => $bookingRef,
                'transaction_reference' => $txRef,
                'price' => $price,
                'status' => $bookingStatus,
            ], 201);
        });
    }

    /**
     * Patient confirms payment — marks booking as PendingPaymentReview.
     * The doctor then verifies and confirms from the admin panel.
     */
    public function confirmPayment(Request $request, string $bookingRef)
    {
        $ref = trim($bookingRef ?: ($request->input('booking_ref') ?? ''));

        // Lookup booking by reference, uppercase reference, or ID
        $booking = Booking::where('booking_reference', $ref)
            ->orWhere('booking_reference', strtoupper($ref))
            ->orWhere('id', is_numeric($ref) ? $ref : 0)
            ->first();

        // Fallback lookup by request body if URL param was template string like {{booking_ref}}
        if (!$booking && $request->filled('booking_ref')) {
            $bodyRef = trim($request->input('booking_ref'));
            $booking = Booking::where('booking_reference', $bodyRef)
                ->orWhere('booking_reference', strtoupper($bodyRef))
                ->orWhere('id', is_numeric($bodyRef) ? $bodyRef : 0)
                ->first();
        }

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'الحجز غير موجود. يرجى التأكد من الرقم المرجعي للحجز أو إرسال طلب الحجز أولاً.',
            ], 404);
        }

        if (in_array($booking->status, ['Confirmed', 'Completed'])) {
            return response()->json([
                'success' => true,
                'message' => 'تم تأكيد هذا الحجز وقبول الدفع مسبقاً من قِبل الإدارة.',
                'booking_reference' => $booking->booking_reference,
                'status' => $booking->status,
            ], 200);
        }

        if (str_contains($booking->status, 'Cancelled')) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، هذا الحجز ملغي ولا يمكن تأكيد الدفع له.',
            ], 422);
        }

        // Acceptable statuses: AwaitingPayment, Pending, PendingPaymentReview
        $booking->status = 'PendingPaymentReview';

        $paymentMethod = $request->input('payment_method') ?: $booking->payment_method;
        $transRef = $request->input('transaction_reference') ?? $request->input('transaction_id') ?? $request->input('transfer_number') ?? $request->input('sender_phone');

        if ($paymentMethod) {
            $booking->payment_method = $paymentMethod;
        }

        // Transfer Number handling
        $transferNumber = $request->input('transfer_number') ?: $request->input('sender_phone') ?: $transRef;
        if (empty($transferNumber)) {
            $transferNumber = $booking->patient ? $booking->patient->phone : ($booking->temp_user_data['phone'] ?? null);
        }
        if ($transferNumber) {
            $booking->transfer_number = $transferNumber;
        }

        // Receipt image handling
        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = 'receipt_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('receipts', $filename, 'public');
            $booking->receipt_image = 'storage/' . $path;
        } elseif ($request->hasFile('receipt_file')) {
            $file = $request->file('receipt_file');
            $filename = 'receipt_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('receipts', $filename, 'public');
            $booking->receipt_image = 'storage/' . $path;
        } elseif ($request->filled('receipt_image') && is_string($request->input('receipt_image'))) {
            $booking->receipt_image = $request->input('receipt_image');
        }

        if ($paymentMethod || $transferNumber) {
            $noteAdd = [];
            if ($paymentMethod) $noteAdd[] = "طريقة الدفع: {$paymentMethod}";
            if ($transferNumber) $noteAdd[] = "رقم التحويل: {$transferNumber}";
            $booking->notes = trim(($booking->notes ? $booking->notes . " | " : "") . implode(' - ', $noteAdd));
        }

        $booking->save();

        if ($booking->payment) {
            $updateData = [];
            if ($transferNumber) {
                $updateData['payment_intent_id'] = $transferNumber;
            }
            if (!empty($updateData)) {
                $booking->payment->update($updateData);
            }
        }

        Log::info("Booking {$bookingRef} marked as PendingPaymentReview by patient.", [
            'payment_method' => $paymentMethod,
            'transfer_number' => $transferNumber,
            'receipt_image' => $booking->receipt_image
        ]);

        // Ensure patient user account exists and authenticate them
        if (!$booking->patient_id && !empty($booking->temp_user_data)) {
            $temp = $booking->temp_user_data;
            $phone = $temp['phone'] ?? null;
            $email = $temp['email'] ?? null;
            $name = $temp['name'] ?? 'عميل جديد';
            $password = $temp['password'] ?? '12345678';

            $user = null;
            if ($phone) {
                $digits = preg_replace('/\D/', '', $phone);
                $last9 = strlen($digits) >= 9 ? substr($digits, -9) : $digits;
                $user = User::where('phone', $phone)
                    ->orWhere('phone', '+' . $digits)
                    ->orWhere('phone', 'like', '%' . $last9)
                    ->first();
            }
            if (!$user && $email) {
                $user = User::where('email', $email)->first();
            }
            if (!$user) {
                $userEmail = !empty($email) ? $email : ('patient_' . preg_replace('/\D/', '', (string)$phone) . '@yonis-app.com');
                $user = User::create([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $userEmail,
                    'password' => Hash::make($password),
                    'role' => 'patient',
                ]);
            }
            $booking->patient_id = $user->id;
            $booking->temp_user_data = null;
            $booking->save();
        }

        if ($booking->patient) {
            Auth::login($booking->patient, true);
        }

        // Send Email Notifications (Fail-safe via NotificationMailService)
        NotificationMailService::notifyDoctorNewBooking($booking, 'إشعار تحويل وتأكيد دفع جديد');
        NotificationMailService::notifyPatientBookingReceived($booking);

        return response()->json([
            'success' => true,
            'status' => 'PendingPaymentReview',
            'message' => 'تم استلام طلبك وتأكيد الدفع بنجاح. حجزك الآن قيد المراجعة، وبعد إتمام التحقق سيصلك رقم تأكيد الحجز النهائي وتفاصيل الموعد.',
            'booking_reference' => $bookingRef,
            'payment_method' => $paymentMethod,
            'transaction_reference' => $transRef,
            'redirect_url' => route('patient.dashboard'),
        ]);
    }

    /**
     * Authenticate and redirect patient to their dashboard directly
     */
    public function goToPatientDashboard(Request $request, string $bookingRef)
    {
        $booking = Booking::with('patient')->where('booking_reference', $bookingRef)->first();
        if ($booking) {
            if (!$booking->patient_id && !empty($booking->temp_user_data)) {
                $temp = $booking->temp_user_data;
                $phone = $temp['phone'] ?? null;
                $email = $temp['email'] ?? null;
                $name = $temp['name'] ?? 'عميل جديد';
                $password = $temp['password'] ?? '12345678';

                $user = null;
                if ($phone) {
                    $digits = preg_replace('/\D/', '', $phone);
                    $last9 = strlen($digits) >= 9 ? substr($digits, -9) : $digits;
                    $user = User::where('phone', $phone)
                        ->orWhere('phone', '+' . $digits)
                        ->orWhere('phone', 'like', '%' . $last9)
                        ->first();
                }
                if (!$user && $email) {
                    $user = User::where('email', $email)->first();
                }
                if (!$user) {
                    $userEmail = !empty($email) ? $email : ('patient_' . preg_replace('/\D/', '', (string)$phone) . '@yonis-app.com');
                    $user = User::create([
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $userEmail,
                        'password' => Hash::make($password),
                        'role' => 'patient',
                    ]);
                }
                $booking->patient_id = $user->id;
                $booking->temp_user_data = null;
                $booking->save();
            }

            if ($booking->patient) {
                Auth::login($booking->patient, true);
            }
        }

        return redirect()->route('patient.dashboard');
    }

    /**
     * Handle incoming SpaceRemit Webhook notification.
     */
    public function spaceremitWebhook(Request $request)
    {
        Log::info('SpaceRemit Webhook Payload:', $request->all());

        $rawRef = $request->input('booking_ref') 
            ?? $request->input('notes') 
            ?? $request->input('order_id')
            ?? $request->input('custom_fields.booking_ref');

        $booking = null;
        if ($rawRef) {
            // 1. Try direct exact match
            $booking = Booking::where('booking_reference', trim($rawRef))->first();

            // 2. If not found directly, extract BK-... pattern from string (e.g. "Booking Ref: BK-ABC12345")
            if (!$booking && preg_match('/BK-[A-Za-z0-9_-]+/i', $rawRef, $matches)) {
                $booking = Booking::where('booking_reference', strtoupper($matches[0]))->first();
            }
        }

        $code = $request->input('code') ?? $request->input('transaction_id');
        if (!$booking && $code) {
            $payment = Payment::where('payment_intent_id', $code)->first();
            $booking = $payment?->booking;
        }

        $status = strtolower((string)($request->input('status', $request->input('payment_status', ''))));
        $isSuccessful = in_array($status, ['success', 'successful', 'paid', 'completed', '1'], true);

        if ($booking) {
            if ($isSuccessful) {
                $booking->status = 'Confirmed';
                $booking->save();

                if ($booking->payment) {
                    $booking->payment->update(['status' => 'Paid']);
                }

                Log::info("SpaceRemit Webhook: Booking {$booking->booking_reference} marked as Confirmed and Paid.");
            } else {
                Log::warning("SpaceRemit Webhook: Payment status was {$status} for booking {$booking->booking_reference}.");
            }

            return response()->json(['success' => true, 'message' => 'Webhook processed successfully'], 200);
        }

        return response()->json(['success' => true, 'message' => 'Webhook received and logged'], 200);
    }
}
