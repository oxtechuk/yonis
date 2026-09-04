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
     * Dashboard Statistics & Interactive Analytics (Blazing Fast Single-Query Aggregations)
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $startOfMonth = Carbon::today()->startOfMonth()->toDateTimeString();
        $endOfMonth = Carbon::today()->endOfMonth()->toDateTimeString();

        // 1. Single Ultra-Fast Aggregated Query for all Bookings Statuses & Channels
        $agg = DB::table('bookings')
            ->selectRaw("
                COUNT(*) as total_all,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status IN ('CancelledByPatient', 'CancelledByDoctor') THEN 1 ELSE 0 END) as cancelled_count,
                SUM(CASE WHEN status = 'NoShow' THEN 1 ELSE 0 END) as noshow_count,
                SUM(CASE WHEN status = 'Confirmed' THEN 1 ELSE 0 END) as confirmed_count,
                SUM(CASE WHEN status = 'AwaitingPayment' THEN 1 ELSE 0 END) as awaiting_count,
                SUM(CASE WHEN date = ? THEN 1 ELSE 0 END) as today_bookings_count,
                SUM(CASE WHEN date >= ? AND status IN ('Confirmed', 'AwaitingPayment') THEN 1 ELSE 0 END) as upcoming_bookings_count,
                SUM(CASE WHEN status = 'AwaitingPayment' THEN COALESCE(price, 0) ELSE 0 END) as pending_revenue,
                SUM(CASE WHEN status IN ('Confirmed', 'Completed') THEN COALESCE(price, 0) ELSE 0 END) as bookings_revenue,
                SUM(CASE WHEN booking_type = 'clinic' THEN 1 ELSE 0 END) as clinic_count,
                SUM(CASE WHEN booking_type = 'online' AND consultation_type = 'video' THEN 1 ELSE 0 END) as video_count,
                SUM(CASE WHEN booking_type = 'online' AND consultation_type = 'voice' THEN 1 ELSE 0 END) as voice_count,
                SUM(CASE WHEN booking_type = 'online' AND consultation_type = 'chat' THEN 1 ELSE 0 END) as chat_count
            ", [$today, $today])
            ->first();

        $totalAll = (int)($agg->total_all ?? 0);
        $totalCompleted = (int)($agg->completed_count ?? 0);
        $completionRate = $totalAll > 0 ? round(($totalCompleted / $totalAll) * 100) : 100;

        // Total Paid Revenue from Payments (or fallback to bookings price)
        $paidRevenue = (float)DB::table('payments')->where('status', 'Paid')->sum('amount');
        if ($paidRevenue <= 0) {
            $paidRevenue = (float)($agg->bookings_revenue ?? 0);
        }

        // Fast Patient Counts
        $totalPatients = DB::table('users')->where('role', 'patient')->count();
        $newPatientsThisMonth = DB::table('users')->where('role', 'patient')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        $stats = [
            'today_bookings' => (int)($agg->today_bookings_count ?? 0),
            'upcoming_bookings' => (int)($agg->upcoming_bookings_count ?? 0),
            'total_patients' => $totalPatients,
            'new_patients_this_month' => $newPatientsThisMonth,
            'completed' => $totalCompleted,
            'completion_rate' => $completionRate,
            'revenue' => $paidRevenue,
            'pending_revenue' => (float)($agg->pending_revenue ?? 0),
        ];

        // 2. Fast Monthly Trend (Single Group By Query for 6 Months)
        $sixMonthsAgo = Carbon::today()->subMonths(5)->startOfMonth()->toDateString();
        $monthlyAggs = DB::table('bookings')
            ->selectRaw("
                SUBSTR(date, 1, 7) as ym,
                COUNT(*) as count,
                SUM(CASE WHEN status IN ('Confirmed', 'Completed') THEN COALESCE(price, 0) ELSE 0 END) as revenue
            ")
            ->where('date', '>=', $sixMonthsAgo)
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');

        $monthLabels = [];
        $monthlyBookings = [];
        $monthlyRevenues = [];
        $arabicMonths = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو',
            7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        for ($i = 5; $i >= 0; $i--) {
            $mDate = Carbon::today()->subMonths($i);
            $ymKey = $mDate->format('Y-m');
            $monthLabels[] = $arabicMonths[$mDate->month] . ' ' . $mDate->year;
            $entry = $monthlyAggs->get($ymKey);
            $monthlyBookings[] = (int)($entry ? $entry->count : 0);
            $monthlyRevenues[] = (float)($entry ? $entry->revenue : 0);
        }

        // 3. Channels Counts
        $clinicCount = (int)($agg->clinic_count ?? 0);
        $videoCount = (int)($agg->video_count ?? 0);
        $voiceCount = (int)($agg->voice_count ?? 0);
        $chatCount = (int)($agg->chat_count ?? 0);

        if ($clinicCount === 0 && $videoCount === 0 && $voiceCount === 0 && $chatCount === 0) {
            $clinicCount = 2; $videoCount = 4; $voiceCount = 2; $chatCount = 1;
        }

        $statusCounts = [
            'confirmed' => (int)($agg->confirmed_count ?? 0),
            'awaiting' => (int)($agg->awaiting_count ?? 0),
            'completed' => $totalCompleted,
            'cancelled' => (int)($agg->cancelled_count ?? 0),
            'noshow' => (int)($agg->noshow_count ?? 0),
        ];

        // 4. Appointments and Bookings with Eager-Loaded Relationships
        $todayAppointments = Booking::whereDate('date', $today)
            ->with(['patient:id,name,phone', 'service:id,title,price', 'payment:id,booking_id,status,amount'])
            ->orderBy('start_time', 'asc')
            ->get();

        $recentBookings = Booking::with(['patient:id,name,phone', 'service:id,title,price', 'payment:id,booking_id,status,amount'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $topServices = Service::select('id', 'title', 'price', 'duration', 'type')
            ->withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(4)
            ->get();

        $allPatients = User::where('role', 'patient')->select('id', 'name', 'phone')->orderBy('name', 'asc')->get();
        $allServices = Service::where('is_active', true)->select('id', 'title', 'price', 'duration', 'type', 'clinic_price', 'video_price', 'voice_price', 'chat_price')->get();

        return view('admin.dashboard', compact(
            'stats',
            'todayAppointments',
            'recentBookings',
            'topServices',
            'monthLabels',
            'monthlyBookings',
            'monthlyRevenues',
            'clinicCount',
            'videoCount',
            'voiceCount',
            'chatCount',
            'statusCounts',
            'allPatients',
            'allServices'
        ));
    }

    /**
     * Booking management
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['patient', 'service', 'payment'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc');

        // Status Group or specific Status Filter
        if ($request->filled('status_group')) {
            $group = $request->status_group;
            if ($group === 'pending_payment') {
                $query->whereIn('status', ['AwaitingPayment', 'PendingPaymentReview', 'Pending']);
            } elseif ($group === 'upcoming') {
                $query->where('status', 'Confirmed');
            } elseif ($group === 'completed') {
                $query->where('status', 'Completed');
            } elseif ($group === 'cancelled') {
                $query->whereIn('status', ['CancelledByPatient', 'CancelledByDoctor', 'Cancelled']);
            }
        } elseif ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })->orWhere('booking_reference', 'like', "%{$search}%");
            });
        }

        $statusCounts = [
            'all'             => Booking::count(),
            'pending_payment' => Booking::whereIn('status', ['AwaitingPayment', 'PendingPaymentReview', 'Pending'])->count(),
            'upcoming'        => Booking::where('status', 'Confirmed')->count(),
            'completed'       => Booking::where('status', 'Completed')->count(),
            'cancelled'       => Booking::whereIn('status', ['CancelledByPatient', 'CancelledByDoctor', 'Cancelled'])->count(),
        ];

        $bookings = $query->paginate(15)->withQueryString();
        $allPatients = User::where('role', 'patient')->orderBy('name', 'asc')->get();
        $allServices = Service::where('is_active', true)->get();

        return view('admin.bookings', compact('bookings', 'allPatients', 'allServices', 'statusCounts'));
    }

    /**
     * Export Bookings Report CSV
     */
    public function exportBookingsReport(Request $request)
    {
        $query = Booking::with(['patient', 'service', 'payment'])
            ->orderBy('date', 'desc');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('service_id')) $query->where('service_id', $request->service_id);
        if ($request->filled('start_date')) $query->whereDate('date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('date', '<=', $request->end_date);

        $bookings = $query->get();

        $filename = 'bookings_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($file, ['رقم المرجع', 'اسم المراجع', 'رقم الهاتف', 'الخدمة', 'التاريخ', 'الوقت', 'المبلغ', 'حالة الحجز', 'حالة الدفع']);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->booking_reference,
                    $b->patient->name ?? '',
                    $b->patient->phone ?? '',
                    $b->service->title ?? '',
                    $b->date->format('Y-m-d'),
                    $b->start_time . ' - ' . $b->end_time,
                    ($b->payment ? $b->payment->amount : ($b->service ? $b->service->price : 0)) . ' USD',
                    $b->status,
                    $b->payment->status ?? 'Unpaid'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $totalRevenue = (clone $query)->where('status', 'Paid')->sum('amount');
        $payments = $query->paginate(15)->withQueryString();

        return view('admin.payments', compact('payments', 'totalRevenue'));
    }

    /**
     * Export Payments Financial Report CSV
     */
    public function exportPaymentsReport(Request $request)
    {
        $query = Payment::with(['booking.patient', 'booking.service'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('start_date')) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('created_at', '<=', $request->end_date);

        $payments = $query->get();

        $filename = 'financial_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($file, ['رقم العملية', 'رقم المرجع', 'اسم المراجع', 'الخدمة', 'المبلغ', 'العملة', 'حالة الدفع', 'تاريخ العملية']);

            foreach ($payments as $p) {
                fputcsv($file, [
                    $p->payment_intent_id,
                    $p->booking->booking_reference ?? '',
                    $p->booking->patient->name ?? '',
                    $p->booking->service->title ?? '',
                    $p->amount,
                    strtoupper($p->currency),
                    $p->status,
                    $p->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Store new manual booking by Admin
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'booking_type' => 'required|in:clinic,online',
            'consultation_type' => 'required|in:clinic,chat,voice,video',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'payment_status' => 'required|in:Paid,Pending',
        ]);

        return DB::transaction(function () use ($request) {
            $service = Service::findOrFail($request->service_id);
            $startTime = Carbon::parse($request->start_time);
            $endTime = $startTime->copy()->addMinutes($service->duration);

            $startTimeStr = $startTime->format('H:i:s');
            $endTimeStr = $endTime->format('H:i:s');
            $dateStr = Carbon::parse($request->date)->format('Y-m-d');

            // Double booking check
            $overlapExists = Booking::where('date', $dateStr)
                ->whereIn('status', ['AwaitingPayment', 'Confirmed', 'Completed'])
                ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                    $query->where('start_time', '<', $endTimeStr)
                          ->where('end_time', '>', $startTimeStr);
                })
                ->exists();

            if ($overlapExists) {
                return redirect()->back()->with('error', 'عذراً، هذا الموعد يتعارض مع حجز آخر.');
            }

            $bookingRef = 'YN-' . strtoupper(Str::random(6));

            $calculatedPrice = $service->price;
            if ($request->consultation_type === 'chat' && isset($service->pricing['chat'])) {
                $calculatedPrice = $service->pricing['chat'];
            } elseif ($request->consultation_type === 'voice' && isset($service->pricing['voice'])) {
                $calculatedPrice = $service->pricing['voice'];
            } elseif ($request->consultation_type === 'video' && isset($service->pricing['video'])) {
                $calculatedPrice = $service->pricing['video'];
            }

            $booking = Booking::create([
                'booking_reference' => $bookingRef,
                'patient_id' => $request->patient_id,
                'service_id' => $request->service_id,
                'booking_type' => $request->booking_type,
                'consultation_type' => $request->consultation_type,
                'price' => $calculatedPrice,
                'date' => $dateStr,
                'start_time' => $startTimeStr,
                'end_time' => $endTimeStr,
                'status' => 'Confirmed',
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'payment_intent_id' => 'cash_manual_' . Str::random(8),
                'amount' => $calculatedPrice,
                'currency' => 'usd',
                'status' => $request->payment_status,
            ]);

            return redirect()->back()->with('success', 'تم إضافة الحجز بنجاح.');
        });
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
        
        // Notify Patient if appointment is confirmed by doctor
        if ($newStatus === 'Confirmed' && $oldStatus !== 'Confirmed') {
            \App\Services\NotificationMailService::notifyPatientBookingConfirmed($booking);
        }

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
            'type' => 'required|in:clinic,online',
            'channel' => 'nullable|string|in:all,video,voice,chat',
            'duration' => 'required|integer|min:5',
            'clinic_price' => 'nullable|numeric|min:0',
            'chat_price' => 'nullable|numeric|min:0',
            'voice_price' => 'nullable|numeric|min:0',
            'video_price' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $type = $request->type;
        $channel = $request->channel ?? 'all';

        if ($type === 'clinic') {
            $clinicPrice = $request->clinic_price ?? ($request->price ?? 0);
            $price = $clinicPrice;
            $chatPrice = null;
            $voicePrice = null;
            $videoPrice = null;
        } else {
            $clinicPrice = null;
            if ($channel === 'video') {
                $videoPrice = $request->video_price ?? ($request->price ?? 0);
                $chatPrice = null;
                $voicePrice = null;
                $price = $videoPrice;
            } elseif ($channel === 'voice') {
                $voicePrice = $request->voice_price ?? ($request->price ?? 0);
                $chatPrice = null;
                $videoPrice = null;
                $price = $voicePrice;
            } elseif ($channel === 'chat') {
                $chatPrice = $request->chat_price ?? ($request->price ?? 0);
                $voicePrice = null;
                $videoPrice = null;
                $price = $chatPrice;
            } else {
                $chatPrice = $request->filled('chat_price') ? (float)$request->chat_price : null;
                $voicePrice = $request->filled('voice_price') ? (float)$request->voice_price : null;
                $videoPrice = $request->filled('video_price') ? (float)$request->video_price : null;
                $price = $videoPrice ?? ($voicePrice ?? ($chatPrice ?? 0));
            }
        }

        Service::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $type,
            'price' => $price,
            'clinic_price' => $clinicPrice,
            'chat_price' => $chatPrice,
            'voice_price' => $voicePrice,
            'video_price' => $videoPrice,
            'payment_url' => null,
            'duration' => $request->duration,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'تم إضافة الخدمة وتحديد أسعارها بنجاح.');
    }

    /**
     * Update existing Service
     */
    public function updateService(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:clinic,online',
            'channel' => 'nullable|string|in:all,video,voice,chat',
            'duration' => 'required|integer|min:5',
            'clinic_price' => 'nullable|numeric|min:0',
            'chat_price' => 'nullable|numeric|min:0',
            'voice_price' => 'nullable|numeric|min:0',
            'video_price' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $service = Service::findOrFail($id);

        $type = $request->type;
        $channel = $request->channel ?? 'all';

        if ($type === 'clinic') {
            $clinicPrice = $request->clinic_price ?? ($request->price ?? 0);
            $price = $clinicPrice;
            $chatPrice = null;
            $voicePrice = null;
            $videoPrice = null;
        } else {
            $clinicPrice = null;
            if ($channel === 'video') {
                $videoPrice = $request->video_price ?? ($request->price ?? 0);
                $chatPrice = null;
                $voicePrice = null;
                $price = $videoPrice;
            } elseif ($channel === 'voice') {
                $voicePrice = $request->voice_price ?? ($request->price ?? 0);
                $chatPrice = null;
                $videoPrice = null;
                $price = $voicePrice;
            } elseif ($channel === 'chat') {
                $chatPrice = $request->chat_price ?? ($request->price ?? 0);
                $voicePrice = null;
                $videoPrice = null;
                $price = $chatPrice;
            } else {
                $chatPrice = $request->filled('chat_price') ? (float)$request->chat_price : null;
                $voicePrice = $request->filled('voice_price') ? (float)$request->voice_price : null;
                $videoPrice = $request->filled('video_price') ? (float)$request->video_price : null;
                $price = $videoPrice ?? ($voicePrice ?? ($chatPrice ?? 0));
            }
        }

        $service->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $type,
            'price' => $price,
            'clinic_price' => $clinicPrice,
            'chat_price' => $chatPrice,
            'voice_price' => $voicePrice,
            'video_price' => $videoPrice,
            'payment_url' => null,
            'duration' => $request->duration,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'تم تحديث بيانات الخدمة وتحديد أسعارها بنجاح.');
    }

    /**
     * Delete Service
     */
    public function deleteService($id)
    {
        $service = Service::findOrFail($id);
        $title = $service->title;
        
        // Delete service
        $service->delete();

        return redirect()->back()->with('success', "تم حذف الخدمة «{$title}» بنجاح.");
    }

    /**
     * API Control Settings View
     */
    public function apiControl()
    {
        $settings = [
            'api_enabled' => Setting::get('api_enabled', '1'),
            'stripe_enabled' => Setting::get('stripe_enabled', '0'),
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
        Setting::set('stripe_enabled', $request->has('stripe_enabled') ? '1' : '0');
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
            $path = $this->storePublicUpload($request->file('hero_image_file'), 'branding');
            $heroImage = asset('storage/' . $path);
        }

        // About Image Upload
        $aboutImage = $profile->about_image;
        if ($request->hasFile('about_image_file')) {
            $path = $this->storePublicUpload($request->file('about_image_file'), 'branding');
            $aboutImage = asset('storage/' . $path);
        }

        // Site Logo Upload
        if ($request->hasFile('site_logo_file')) {
            $path = $this->storePublicUpload($request->file('site_logo_file'), 'branding');
            Setting::set('site_logo', asset('storage/' . $path));
        }

        // Handle gallery image uploads
        $gallery = $profile->gallery ?? [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $path = $this->storePublicUpload($file, 'gallery');
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
     * Helper to extract YouTube Video ID from standard/shorts/embed/shortened URLs
     */
    private function extractYoutubeId(?string $url): ?string
    {
        if (!$url) return null;
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|shorts\/|watch\?v=|watch\?.+&v=))([\w-]{11})/i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Helper to detect platform from URL
     */
    private function detectPlatform(?string $url, ?string $fallback = 'youtube'): string
    {
        if (!$url) return $fallback ?: 'youtube';
        if (preg_match('/youtube\.com|youtu\.be/i', $url)) return 'youtube';
        if (preg_match('/tiktok\.com/i', $url)) return 'tiktok';
        if (preg_match('/instagram\.com/i', $url)) return 'instagram';
        return $fallback ?: 'direct';
    }

    /**
     * Store new Video Reel
     */
    public function storeReel(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'platform' => 'nullable|string',
            'video_url' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,webm,mkv|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'thumbnail_url' => 'nullable|string',
            'duration' => 'nullable|integer',
            'sort_order' => 'nullable|integer',
        ]);

        $videoUrl = $request->video_url;
        if ($request->hasFile('video_file')) {
            $path = $this->storePublicUpload($request->file('video_file'), 'reels');
            $videoUrl = asset('storage/' . $path);
        }

        // Platform detection or selection
        $platform = $request->platform;
        if (empty($platform) || $platform === 'auto') {
            $platform = $this->detectPlatform($videoUrl);
        }

        // Thumbnail resolution
        $thumbnailUrl = null;
        $thumbnailFile = $request->file('thumbnail') ?: $request->file('thumbnail_file');
        if ($thumbnailFile) {
            $path = $this->storePublicUpload($thumbnailFile, 'reels/thumbnails');
            $thumbnailUrl = asset('storage/' . $path);
        } elseif ($request->filled('thumbnail_url')) {
            $thumbnailUrl = $request->thumbnail_url;
        } else {
            $ytId = $this->extractYoutubeId($videoUrl);
            if ($ytId) {
                $thumbnailUrl = "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg";
            } else {
                $thumbnailUrl = 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=500&q=80';
            }
        }

        Reel::create([
            'title' => $request->title,
            'title_en' => $request->title_en ?: null,
            'video_url' => $videoUrl ?: '#',
            'thumbnail_url' => $thumbnailUrl,
            'duration' => $request->duration ?: 60,
            'platform' => $platform,
            'sort_order' => $request->sort_order ?: 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->back()->with('success', 'تم إضافة الفيديو التوعوي بنجاح.');
    }

    /**
     * Update Video Reel
     */
    public function updateReel(Request $request, $id)
    {
        $reel = Reel::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'platform' => 'nullable|string',
            'video_url' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,webm,mkv|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'thumbnail_url' => 'nullable|string',
            'duration' => 'nullable|integer',
            'sort_order' => 'nullable|integer',
        ]);

        $videoUrl = $reel->video_url;
        if ($request->hasFile('video_file')) {
            if (str_contains($reel->video_url, '/storage/reels/')) {
                $oldFile = 'reels/' . basename($reel->video_url);
                Storage::disk('public')->delete($oldFile);
            }
            $path = $this->storePublicUpload($request->file('video_file'), 'reels');
            $videoUrl = asset('storage/' . $path);
        } elseif ($request->filled('video_url')) {
            $videoUrl = $request->video_url;
        }

        $platform = $request->platform;
        if (empty($platform) || $platform === 'auto') {
            $platform = $this->detectPlatform($videoUrl, $reel->platform);
        }

        $thumbnailUrl = $reel->thumbnail_url;
        $thumbnailFile = $request->file('thumbnail') ?: $request->file('thumbnail_file');
        if ($thumbnailFile) {
            if (str_contains($reel->thumbnail_url, '/storage/reels/thumbnails/')) {
                $oldThumb = 'reels/thumbnails/' . basename($reel->thumbnail_url);
                Storage::disk('public')->delete($oldThumb);
            }
            $path = $this->storePublicUpload($thumbnailFile, 'reels/thumbnails');
            $thumbnailUrl = asset('storage/' . $path);
        } elseif ($request->filled('thumbnail_url')) {
            $thumbnailUrl = $request->thumbnail_url;
        } elseif ($videoUrl !== $reel->video_url && ($ytId = $this->extractYoutubeId($videoUrl))) {
            $thumbnailUrl = "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg";
        }

        $reel->update([
            'title' => $request->title,
            'title_en' => $request->title_en ?: null,
            'video_url' => $videoUrl,
            'thumbnail_url' => $thumbnailUrl,
            'platform' => $platform,
            'duration' => $request->duration ?: $reel->duration,
            'sort_order' => $request->has('sort_order') ? (int)$request->sort_order : $reel->sort_order,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $reel->is_active,
        ]);

        return redirect()->back()->with('success', 'تم تحديث الفيديو التوعوي بنجاح.');
    }

    /**
     * Delete Video Reel
     */
    public function deleteReel($id)
    {
        $reel = Reel::findOrFail($id);

        if (str_contains($reel->video_url, '/storage/reels/')) {
            $oldFile = 'reels/' . basename($reel->video_url);
            Storage::disk('public')->delete($oldFile);
        }
        if (str_contains($reel->thumbnail_url, '/storage/reels/thumbnails/')) {
            $oldThumb = 'reels/thumbnails/' . basename($reel->thumbnail_url);
            Storage::disk('public')->delete($oldThumb);
        }

        $reel->delete();
        return redirect()->back()->with('success', 'تم حذف مقطع الفيديو بنجاح.');
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
            $path = $this->storePublicUpload($request->file('avatar_file'), 'testimonials');
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
     * Update Testimonial
     */
    public function updateTestimonial(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $request->validate([
            'client_name_ar' => 'required|string|max:255',
            'client_name_en' => 'nullable|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $avatarUrl = $testimonial->client_avatar;
        if ($request->hasFile('avatar_file')) {
            if ($avatarUrl && str_contains($avatarUrl, '/storage/testimonials/')) {
                Storage::disk('public')->delete('testimonials/' . basename($avatarUrl));
            }
            $path = $this->storePublicUpload($request->file('avatar_file'), 'testimonials');
            $avatarUrl = asset('storage/' . $path);
        }

        $testimonial->update([
            'client_name_ar' => $request->client_name_ar,
            'client_name_en' => $request->client_name_en ?: null,
            'content_ar' => $request->content_ar,
            'content_en' => $request->content_en ?: null,
            'rating' => $request->rating,
            'client_avatar' => $avatarUrl,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $testimonial->is_active,
        ]);

        return redirect()->back()->with('success', 'تم تحديث رأي العميل بنجاح.');
    }

    /**
     * Delete Testimonial
     */
    public function deleteTestimonial($id)
    {
        $testimonial = Testimonial::findOrFail($id);

        if ($testimonial->client_avatar && str_contains($testimonial->client_avatar, '/storage/testimonials/')) {
            Storage::disk('public')->delete('testimonials/' . basename($testimonial->client_avatar));
        }

        $testimonial->delete();
        return redirect()->back()->with('success', 'تم حذف رأي العميل بنجاح.');
    }

    /**
     * Settings Page View
     */
    public function settings()
    {
        $settings = [
            'site_title'               => Setting::get('site_title', 'إدارة العيادة'),
            'doctor_name'              => Setting::get('doctor_name', 'يونس المرشد'),
            'site_logo'                => Setting::get('site_logo', ''),
            'footer_logo'              => Setting::get('footer_logo', ''),
            'primary_color'            => Setting::get('primary_color', '#3B52A4'),
            'secondary_color'          => Setting::get('secondary_color', '#1e3a8a'),
            'google_site_verification' => Setting::get('google_site_verification', ''),
            'meta_description'         => Setting::get('meta_description', 'احجز استشارتك النفسية الآن مع المعالج يونس المرشد. جلسات فردية وزوجية وأسرية بخبرة أكثر من 10 سنوات.'),
            'meta_keywords'            => Setting::get('meta_keywords', 'معالج نفسي, استشارة نفسية, علاج نفسي, يونس المرشد, حجز موعد نفسي, اكتئاب, قلق, علاج زوجي'),
            'og_image'                 => Setting::get('og_image', ''),
            'google_analytics_id'      => Setting::get('google_analytics_id', ''),
            'meta_pixel_id'            => Setting::get('meta_pixel_id', ''),
            'notify_new_booking'          => Setting::get('notify_new_booking', '1'),
            'notify_cancellation'         => Setting::get('notify_cancellation', '1'),
            'email_notifications_enabled' => Setting::get('email_notifications_enabled', '1'),
            'notification_email'          => Setting::get('notification_email', env('ADMIN_NOTIFICATION_EMAIL', 'dr.yonis@example.com')),
            'booking_banner_image'        => Setting::get('booking_banner_image', ''),
            // ─── إعدادات الدفع ───────────────────────────────────────────────
            'payment_zaincash_enabled' => Setting::get('payment_zaincash_enabled', '1'),
            'payment_zaincash_qr'      => Setting::get('payment_zaincash_qr', ''),
            'payment_zaincash_label'   => Setting::get('payment_zaincash_label', 'افتح تطبيق زين كاش وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال للدكتور.'),
            'payment_superki_enabled'  => Setting::get('payment_superki_enabled', '1'),
            'payment_superki_qr'       => Setting::get('payment_superki_qr', ''),
            'payment_superki_label'    => Setting::get('payment_superki_label', 'افتح تطبيق SuperKi وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال للدكتور.'),
            'payment_card_enabled'     => Setting::get('payment_card_enabled', '0'),
            'payment_card_key'         => Setting::get('payment_card_key', ''),
            'payment_card_link'        => Setting::get('payment_card_link', ''),
            'payment_card_instructions'=> Setting::get('payment_card_instructions', 'يمكنك الدفع مباشرة باستخدام أي بطاقة فيزا أو ماستر كارد صادرة محلياً أو دولياً بأمان وسرية تامة.'),
            'payment_spaceremit_enabled' => Setting::get('payment_spaceremit_enabled', '0'),
            'payment_spaceremit_key'   => Setting::get('payment_spaceremit_key', ''),
            'payment_spaceremit_currency' => Setting::get('payment_spaceremit_currency', 'USD'),
            // ─── إعدادات العملة والمدة للمنصة ──────────────────────────────────────
            'currency_code'                 => Setting::currencyCode(),
            'currency_symbol'               => Setting::currencySymbol(),
            'default_consultation_duration' => Setting::get('default_consultation_duration', '45'),
        ];
        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings handler
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_title'                   => 'nullable|string|max:255',
            'doctor_name'                  => 'nullable|string|max:255',
            'site_logo'                    => 'nullable|string',
            'logo_file'                    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'footer_logo'                  => 'nullable|string',
            'footer_logo_file'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'booking_banner_image'         => 'nullable|string',
            'booking_banner_file'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'primary_color'                => 'nullable|string|max:20',
            'secondary_color'              => 'nullable|string|max:20',
            'google_site_verification'     => 'nullable|string|max:255',
            'meta_description'             => 'nullable|string',
            'meta_keywords'                => 'nullable|string',
            'og_image'                     => 'nullable|string',
            'og_image_file'                => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'google_analytics_id'          => 'nullable|string|max:50',
            'meta_pixel_id'                => 'nullable|string|max:50',
            // Payment validation
            'payment_zaincash_qr_file'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'payment_superki_qr_file'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'payment_zaincash_label'       => 'nullable|string|max:500',
            'payment_superki_label'        => 'nullable|string|max:500',
            'payment_card_key'             => 'nullable|string|max:255',
            'payment_card_link'            => 'nullable|string|max:500',
            'payment_card_instructions'    => 'nullable|string|max:500',
            'payment_spaceremit_key'       => 'nullable|string|max:255',
            'payment_spaceremit_currency'  => 'nullable|string|max:10',
        ]);

        // Handle header logo file upload
        if ($request->hasFile('logo_file')) {
            $path = $this->storePublicUpload($request->file('logo_file'), 'branding');
            Setting::set('site_logo', asset('storage/' . $path));
        } elseif ($request->filled('site_logo')) {
            Setting::set('site_logo', $request->site_logo);
        }

        // Handle footer logo file upload
        if ($request->hasFile('footer_logo_file')) {
            $path = $this->storePublicUpload($request->file('footer_logo_file'), 'branding');
            Setting::set('footer_logo', asset('storage/' . $path));
        } elseif ($request->has('footer_logo')) {
            Setting::set('footer_logo', $request->footer_logo);
        }

        // Handle booking banner image file upload
        if ($request->hasFile('booking_banner_file')) {
            $path = $this->storePublicUpload($request->file('booking_banner_file'), 'branding');
            Setting::set('booking_banner_image', asset('storage/' . $path));
        } elseif ($request->has('booking_banner_image')) {
            Setting::set('booking_banner_image', $request->booking_banner_image);
        }

        // Handle OG image file upload
        if ($request->hasFile('og_image_file')) {
            $path = $this->storePublicUpload($request->file('og_image_file'), 'branding');
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

        // ─── إعدادات الدفع ─────────────────────────────────────────────────
        // زين كاش
        Setting::set('payment_zaincash_enabled', $request->has('payment_zaincash_enabled') ? '1' : '0');
        Setting::set('payment_zaincash_label', $request->payment_zaincash_label ?? '');
        if ($request->hasFile('payment_zaincash_qr_file')) {
            $path = $this->storePublicUpload($request->file('payment_zaincash_qr_file'), 'payments');
            Setting::set('payment_zaincash_qr', asset('storage/' . $path));
        } elseif ($request->filled('payment_zaincash_qr')) {
            Setting::set('payment_zaincash_qr', $request->payment_zaincash_qr);
        }

        // SuperKi
        Setting::set('payment_superki_enabled', $request->has('payment_superki_enabled') ? '1' : '0');
        Setting::set('payment_superki_label', $request->payment_superki_label ?? '');
        if ($request->hasFile('payment_superki_qr_file')) {
            $path = $this->storePublicUpload($request->file('payment_superki_qr_file'), 'payments');
            Setting::set('payment_superki_qr', asset('storage/' . $path));
        } elseif ($request->filled('payment_superki_qr')) {
            Setting::set('payment_superki_qr', $request->payment_superki_qr);
        }

        // فيزا وماستر كارد
        Setting::set('payment_card_enabled', $request->has('payment_card_enabled') ? '1' : '0');
        if ($request->has('payment_card_key')) Setting::set('payment_card_key', $request->payment_card_key ?? '');
        if ($request->has('payment_card_link')) Setting::set('payment_card_link', $request->payment_card_link ?? '');
        if ($request->has('payment_card_instructions')) Setting::set('payment_card_instructions', $request->payment_card_instructions ?? '');

        // SpaceRemit
        Setting::set('payment_spaceremit_enabled', $request->has('payment_spaceremit_enabled') ? '1' : '0');
        Setting::set('payment_spaceremit_key', $request->payment_spaceremit_key ?? '');
        Setting::set('payment_spaceremit_currency', $request->payment_spaceremit_currency ?? 'USD');

        // إعدادات تنبيهات البريد الإلكتروني
        Setting::set('email_notifications_enabled', $request->has('email_notifications_enabled') ? '1' : '0');
        if ($request->filled('notification_email')) {
            Setting::set('notification_email', $request->notification_email);
        }

        // إعدادات العملة الرسمية للمنصة
        if ($request->filled('currency_code')) {
            $cCode = strtoupper(trim($request->currency_code));
            Setting::set('currency_code', $cCode);
            Setting::set('currency', $cCode);
        }
        if ($request->filled('currency_symbol')) {
            Setting::set('currency_symbol', trim($request->currency_symbol));
        }

        // مدة الاستشارة الافتراضية
        if ($request->filled('default_consultation_duration')) {
            Setting::set('default_consultation_duration', max(5, (int) $request->default_consultation_duration));
        }

        return redirect()->back()->with('success', 'تم حفظ جميع الإعدادات بنجاح!');
    }

    /**
     * Send test email to verify SMTP credentials from Admin settings
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            \App\Services\NotificationMailService::sendTestEmail($request->email);
            return response()->json([
                'success' => true,
                'message' => '✅ تم إرسال بريد الاختبار بنجاح إلى: ' . $request->email . '. يرجى تفقد صندوق الوارد أو البريد غير الهام (Spam).'
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SMTP Test Email Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل إرسال البريد: ' . $e->getMessage() . '. يرجى مراجعة إعدادات MAIL_PASSWORD و MAIL_USERNAME في ملف .env'
            ], 500);
        }
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
            ->whereIn('status', ['Confirmed', 'Completed', 'AwaitingPayment', 'Pending', 'PendingPaymentReview', 'CancelledByPatient', 'CancelledByDoctor', 'NoShow'])
            ->get();

        $events = [];
        foreach ($bookings as $b) {
            $status = $b->status;
            $color = '#10b981'; // Confirmed = Emerald Green
            $statusLabel = 'مؤكد';

            if ($status === 'Completed') {
                $color = '#3b82f6'; // Completed = Blue
                $statusLabel = 'مكتمل';
            } elseif ($status === 'PendingPaymentReview') {
                $color = '#f97316'; // Orange — awaiting doctor verification
                $statusLabel = 'انتظار مراجعة الدفع';
            } elseif ($status === 'AwaitingPayment' || $status === 'Pending') {
                $color = '#f59e0b'; // AwaitingPayment = Amber Yellow
                $statusLabel = 'بانتظار الدفع';
            } elseif (str_contains($status, 'Cancelled')) {
                $color = '#ef4444'; // Cancelled = Red
                $statusLabel = 'ملغي';
            } elseif ($status === 'NoShow') {
                $color = '#64748b'; // NoShow = Slate Gray
                $statusLabel = 'لم يحضر';
            }

            $patientName = $b->patient?->name ?? ($b->temp_user_data['name'] ?? 'عميل (طلب جديد)');
            $patientPhone = $b->patient?->phone ?? ($b->temp_user_data['phone'] ?? 'غير متوفر');
            $serviceTitle = $b->service?->title ?? 'جلسة استشارة';
            $servicePrice = $b->service?->price ?? 0;

            // Normalize Date & Time
            $dateStr = $b->date instanceof \DateTimeInterface ? $b->date->format('Y-m-d') : substr((string)$b->date, 0, 10);
            
            $startTime = '09:00:00';
            if (!empty($b->start_time)) {
                try {
                    $startTime = \Carbon\Carbon::parse($b->start_time)->format('H:i:s');
                } catch (\Exception $e) {
                    $startTime = (string)$b->start_time;
                }
            }

            $endTime = '09:45:00';
            if (!empty($b->end_time)) {
                try {
                    $endTime = \Carbon\Carbon::parse($b->end_time)->format('H:i:s');
                } catch (\Exception $e) {
                    $endTime = (string)$b->end_time;
                }
            }

            $events[] = [
                'id' => $b->id,
                'title' => $patientName . ' (' . $statusLabel . ') - ' . $serviceTitle,
                'start' => $dateStr . 'T' . $startTime,
                'end' => $dateStr . 'T' . $endTime,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'booking_id' => $b->id,
                    'patient_id' => $b->patient_id,
                    'patient_name' => $patientName,
                    'patient_phone' => $patientPhone,
                    'service_title' => $serviceTitle,
                    'status' => $status,
                    'status_label' => $statusLabel,
                    'price' => $servicePrice,
                    'reference' => $b->booking_reference,
                    'booking_type' => $b->booking_type ?? 'online',
                    'notes' => $b->notes ?? '',
                ]
            ];
        }

        return response()->json($events);
    }

    /**
     * Comprehensive Reports & Analytics Hub
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', 'month');
        $serviceId = $request->get('service_id');
        $bookingType = $request->get('booking_type');
        $status = $request->get('status');

        $now = Carbon::now();
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($period === 'today') {
            $startDate = $now->copy()->startOfDay()->format('Y-m-d');
            $endDate = $now->copy()->endOfDay()->format('Y-m-d');
        } elseif ($period === 'week') {
            $startDate = $now->copy()->startOfWeek()->format('Y-m-d');
            $endDate = $now->copy()->endOfWeek()->format('Y-m-d');
        } elseif ($period === 'month') {
            $startDate = $now->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $now->copy()->endOfMonth()->format('Y-m-d');
        } elseif ($period === 'year') {
            $startDate = $now->copy()->startOfYear()->format('Y-m-d');
            $endDate = $now->copy()->endOfYear()->format('Y-m-d');
        } elseif ($period === 'all') {
            $startDate = '2024-01-01';
            $endDate = $now->copy()->addYear()->format('Y-m-d');
        } else { // custom
            if (empty($startDate)) $startDate = $now->copy()->startOfMonth()->format('Y-m-d');
            if (empty($endDate)) $endDate = $now->copy()->endOfMonth()->format('Y-m-d');
        }

        // Base Query
        $query = Booking::with(['patient', 'service', 'payment'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

        if (!empty($serviceId)) {
            $query->where('service_id', $serviceId);
        }
        if (!empty($bookingType)) {
            $query->where('booking_type', $bookingType);
        }
        if (!empty($status)) {
            $query->where('status', $status);
        }

        $allPeriodBookings = (clone $query)->get();

        // 1. Financial Metrics
        $totalBookings = $allPeriodBookings->count();
        $paidBookings = $allPeriodBookings->whereIn('status', ['Confirmed', 'Completed']);
        $grossRevenue = $allPeriodBookings->sum('price');
        $paidRevenue = $paidBookings->sum('price');
        $pendingRevenue = $allPeriodBookings->where('status', 'AwaitingPayment')->sum('price');
        $completedCount = $allPeriodBookings->where('status', 'Completed')->count();
        $cancelledCount = $allPeriodBookings->whereIn('status', ['CancelledByPatient', 'CancelledByDoctor'])->count();
        $noShowCount = $allPeriodBookings->where('status', 'NoShow')->count();
        $avgBookingValue = $totalBookings > 0 ? round($paidRevenue / max(1, $paidBookings->count()), 2) : 0;

        // 2. Services Performance Breakdown
        $allServices = Service::all();
        $serviceStats = [];
        foreach ($allServices as $srv) {
            $srvBookings = $allPeriodBookings->where('service_id', $srv->id);
            $srvCount = $srvBookings->count();
            $srvRevenue = $srvBookings->whereIn('status', ['Confirmed', 'Completed'])->sum('price');
            $srvPercentage = $totalBookings > 0 ? round(($srvCount / $totalBookings) * 100, 1) : 0;

            $serviceStats[] = [
                'id' => $srv->id,
                'title' => $srv->title,
                'duration' => $srv->duration,
                'type' => $srv->type,
                'count' => $srvCount,
                'revenue' => $srvRevenue,
                'percentage' => $srvPercentage,
            ];
        }
        // Sort services by revenue descending
        usort($serviceStats, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        // 3. Channels Breakdown
        $channelStats = [
            'clinic' => [
                'title' => 'كشوفات العيادة (In-Clinic)',
                'count' => $allPeriodBookings->where('booking_type', 'clinic')->count(),
                'revenue' => $allPeriodBookings->where('booking_type', 'clinic')->whereIn('status', ['Confirmed', 'Completed'])->sum('price'),
            ],
            'video' => [
                'title' => 'استشارة مكالمة فيديو',
                'count' => $allPeriodBookings->where('booking_type', 'online')->where('consultation_type', 'video')->count(),
                'revenue' => $allPeriodBookings->where('booking_type', 'online')->where('consultation_type', 'video')->whereIn('status', ['Confirmed', 'Completed'])->sum('price'),
            ],
            'voice' => [
                'title' => 'استشارة مكالمة صوتية',
                'count' => $allPeriodBookings->where('booking_type', 'online')->where('consultation_type', 'voice')->count(),
                'revenue' => $allPeriodBookings->where('booking_type', 'online')->where('consultation_type', 'voice')->whereIn('status', ['Confirmed', 'Completed'])->sum('price'),
            ],
            'chat' => [
                'title' => 'استشارة محادثة نصية (شات)',
                'count' => $allPeriodBookings->where('booking_type', 'online')->where('consultation_type', 'chat')->count(),
                'revenue' => $allPeriodBookings->where('booking_type', 'online')->where('consultation_type', 'chat')->whereIn('status', ['Confirmed', 'Completed'])->sum('price'),
            ],
        ];

        // 4. Patients Analysis
        $uniquePatientIds = $allPeriodBookings->pluck('patient_id')->filter()->unique();
        $totalPatientsInPeriod = $uniquePatientIds->count();
        $newPatientsInPeriod = User::where('role', 'patient')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();

        // 5. Daily Trend Graph Data for the period
        $trendLabels = [];
        $trendBookings = [];
        $trendRevenue = [];

        $periodDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        if ($periodDays <= 31) {
            $current = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            while ($current <= $end) {
                $dStr = $current->format('Y-m-d');
                $dLabel = $current->format('d/m');
                $trendLabels[] = $dLabel;
                $dayB = $allPeriodBookings->filter(fn($b) => substr((string)$b->date, 0, 10) === $dStr);
                $trendBookings[] = $dayB->count();
                $trendRevenue[] = (float)$dayB->whereIn('status', ['Confirmed', 'Completed'])->sum('price');
                $current->addDay();
            }
        } else {
            // Group by month
            $current = Carbon::parse($startDate)->startOfMonth();
            $end = Carbon::parse($endDate)->endOfMonth();
            while ($current <= $end) {
                $mLabel = $current->format('m/Y');
                $mStart = $current->copy()->startOfMonth()->format('Y-m-d');
                $mEnd = $current->copy()->endOfMonth()->format('Y-m-d');
                $trendLabels[] = $mLabel;
                $mB = $allPeriodBookings->filter(fn($b) => substr((string)$b->date, 0, 10) >= $mStart && substr((string)$b->date, 0, 10) <= $mEnd);
                $trendBookings[] = $mB->count();
                $trendRevenue[] = (float)$mB->whereIn('status', ['Confirmed', 'Completed'])->sum('price');
                $current->addMonth();
            }
        }

        // Detailed Bookings Table (Paginated)
        $detailedBookings = $query->orderBy('date', 'desc')->orderBy('start_time', 'desc')->paginate(15)->withQueryString();

        $metrics = [
            'total_bookings' => $totalBookings,
            'gross_revenue' => $grossRevenue,
            'paid_revenue' => $paidRevenue,
            'pending_revenue' => $pendingRevenue,
            'completed_count' => $completedCount,
            'cancelled_count' => $cancelledCount,
            'noshow_count' => $noShowCount,
            'avg_booking_value' => $avgBookingValue,
            'total_patients' => $totalPatientsInPeriod,
            'new_patients' => $newPatientsInPeriod,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'period' => $period,
        ];

        return view('admin.reports', compact(
            'metrics',
            'serviceStats',
            'channelStats',
            'trendLabels',
            'trendBookings',
            'trendRevenue',
            'detailedBookings',
            'allServices',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export Detailed Reports CSV
     */
    public function exportReportsCsv(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));

        $query = Booking::with(['patient', 'service', 'payment'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date', 'desc');

        if ($request->filled('service_id')) $query->where('service_id', $request->service_id);
        if ($request->filled('booking_type')) $query->where('booking_type', $request->booking_type);
        if ($request->filled('status')) $query->where('status', $request->status);

        $bookings = $query->get();

        $filename = "Yonis_Medical_Report_{$startDate}_to_{$endDate}.csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel Arabic support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header Row
            fputcsv($file, [
                'المرجع',
                'اسم المريض',
                'الهاتف',
                'الخدمة',
                'نوع الحجز',
                'قناة الاستشارة',
                'التاريخ',
                'الوقت',
                'المبلغ (' . Setting::currencySymbol() . ')',
                'حالة الحجز',
                'حالة الدفع'
            ]);

            foreach ($bookings as $b) {
                $patientName = $b->patient?->name ?? ($b->temp_user_data['name'] ?? 'زائر');
                $patientPhone = $b->patient?->phone ?? ($b->temp_user_data['phone'] ?? '-');
                $statusAr = match($b->status) {
                    'Confirmed'             => 'مؤكد',
                    'AwaitingPayment'       => 'بانتظار الدفع',
                    'PendingPaymentReview'  => 'بانتظار مراجعة الدفع',
                    'Completed'             => 'مكتمل',
                    'CancelledByPatient'    => 'ملغي بواسطة المريض',
                    'CancelledByDoctor'     => 'ملغي بواسطة الطبيب',
                    'NoShow'                => 'لم يحضر',
                    default                 => $b->status
                };

                fputcsv($file, [
                    $b->booking_reference,
                    $patientName,
                    $patientPhone,
                    $b->service?->title ?? '-',
                    $b->booking_type === 'clinic' ? 'عيادة' : 'أونلاين',
                    $b->consultation_type_label ?? $b->consultation_type,
                    $b->date instanceof \DateTimeInterface ? $b->date->format('Y-m-d') : substr((string)$b->date, 0, 10),
                    Carbon::parse($b->start_time)->format('H:i') . ' - ' . Carbon::parse($b->end_time)->format('H:i'),
                    number_format($b->price ?? $b->service?->price ?? 0, 2),
                    $statusAr,
                    $b->payment?->status === 'Paid' ? 'مدفوع' : ($b->status === 'Confirmed' ? 'مدفوع' : 'معلق')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Store an uploaded file safely in public storage without relying on PHP ext-fileinfo.
     */
    protected function storePublicUpload($file, string $directory = 'uploads'): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $filename = Str::random(40) . '.' . strtolower($extension);
        $cleanDir = trim($directory, '/\\');

        $storageDir = storage_path('app/public/' . $cleanDir);
        if (!file_exists($storageDir)) {
            @mkdir($storageDir, 0755, true);
        }

        $targetPath = $storageDir . DIRECTORY_SEPARATOR . $filename;
        $file->move($storageDir, $filename);

        // If public/storage is a real folder (common on cPanel without symlinks), mirror file there
        $publicDir = public_path('storage/' . $cleanDir);
        if (file_exists(public_path('storage')) && !is_link(public_path('storage'))) {
            if (!file_exists($publicDir)) {
                @mkdir($publicDir, 0755, true);
            }
            @copy($targetPath, $publicDir . DIRECTORY_SEPARATOR . $filename);
        }

        return $cleanDir . '/' . $filename;
    }
}
