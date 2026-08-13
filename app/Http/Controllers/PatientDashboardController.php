<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Refund;

class PatientDashboardController extends Controller
{
    /**
     * Show patient dashboard
     */
    public function index()
    {
        $patient = Auth::user();
        $bookings = Booking::where('patient_id', $patient->id)
            ->with(['service', 'payment'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('patient.dashboard', compact('bookings'));
    }

    /**
     * Cancel booking and request refund if eligible
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        if (!in_array($booking->status, ['AwaitingPayment', 'Confirmed'])) {
            return redirect()->back()->with('error', 'لا يمكن إلغاء هذا الحجز في حالته الحالية.');
        }

        // Calculate time difference
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
                        // Mock refund for local dev
                        $payment->update([
                            'status' => 'Refunded',
                            'refunded_amount' => $payment->amount
                        ]);
                    }
                    $booking->update(['status' => 'CancelledByPatient']);
                    return redirect()->back()->with('success', 'تم إلغاء الحجز بنجاح وتم طلب استرداد المبلغ المدفوع (سيستغرق عدة أيام للظهور في حسابك).');
                } catch (\Exception $e) {
                    Log::error('Stripe Refund Error: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'حدث خطأ أثناء محاولة استرداد الأموال تلقائياً: ' . $e->getMessage());
                }
            } else {
                // Late cancellation - no refund
                $booking->update(['status' => 'CancelledByPatient']);
                return redirect()->back()->with('warning', 'تم إلغاء الحجز، ولكن نظراً لأن الإلغاء تم قبل أقل من 24 ساعة من الموعد، فلن يتم استرداد القيمة المدفوعة وفقاً لسياسة الإلغاء.');
            }
        }

        // If it was awaiting payment or unpaid
        $booking->update(['status' => 'CancelledByPatient']);
        if ($payment) {
            $payment->update(['status' => 'Failed']);
        }

        return redirect()->back()->with('success', 'تم إلغاء الحجز بنجاح.');
    }
}
