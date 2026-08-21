<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Event;

use App\Services\BookingCheckoutService;

class StripeWebhookController extends Controller
{
    protected BookingCheckoutService $checkoutService;

    public function __construct(BookingCheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Handle incoming Stripe webhooks.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        $event = null;

        // If Webhook Secret is not set (e.g. local dev mock tests), process payload directly
        if (empty($webhookSecret) || str_contains($webhookSecret, 'placeholder')) {
            $data = json_decode($payload, true);
            if ($data && isset($data['type'])) {
                Log::info('Stripe Webhook (Mock Mode): ' . $data['type']);
                $this->processEvent($data['type'], $data['data']['object']);
                return response('Webhook Processed (Mock)', 200);
            }
            return response('Invalid Payload', 400);
        }

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Stripe Webhook Error: Invalid Payload - ' . $e->getMessage());
            return response('Invalid Payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Stripe Webhook Error: Invalid Signature - ' . $e->getMessage());
            return response('Invalid Signature', 400);
        }

        Log::info('Stripe Webhook Received: ' . $event->type);
        $this->processEvent($event->type, $event->data->object);

        return response('Webhook Handled', 200);
    }

    /**
     * Process Stripe event.
     */
    protected function processEvent(string $type, $stripeObject)
    {
        // 1. Payment succeeded
        if ($type === 'payment_intent.succeeded') {
            $paymentIntentId = $stripeObject['id'] ?? null;
            if ($paymentIntentId) {
                $payment = Payment::where('payment_intent_id', $paymentIntentId)->first();
                if ($payment && $payment->booking) {
                    $this->checkoutService->confirmBookingPayment($payment->booking, $payment);
                    Log::info("Booking Ref {$payment->booking->booking_reference} confirmed and user account processed via Stripe Webhook.");
                }
            }
        }

        // 2. Refund succeeded
        if ($type === 'charge.refunded') {
            $paymentIntentId = $stripeObject['payment_intent'] ?? null;
            if ($paymentIntentId) {
                $payment = Payment::where('payment_intent_id', $paymentIntentId)->first();
                if ($payment) {
                    $refundedAmount = ($stripeObject['amount_refunded'] ?? 0) / 100;
                    $payment->update([
                        'status' => 'Refunded',
                        'refunded_amount' => $refundedAmount
                    ]);
                    $payment->booking->update(['status' => 'CancelledByPatient']);
                    Log::info("Booking Ref {$payment->booking->booking_reference} refunded and cancelled via Stripe Webhook.");
                }
            }
        }
    }
}
