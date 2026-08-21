<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class BookingCheckoutService
{
    /**
     * Confirm booking payment and create client account if not already created.
     */
    public function confirmBookingPayment(Booking $booking, Payment $payment): User
    {
        $patient = $booking->patient;

        // If booking does not have an associated patient yet, process temp_user_data
        if (!$patient && !empty($booking->temp_user_data)) {
            $tempData = $booking->temp_user_data;
            $phone = $tempData['phone'] ?? null;
            $email = $tempData['email'] ?? null;
            $name = $tempData['name'] ?? 'عميل جديد';
            $plainPassword = $tempData['password'] ?? '12345678';

            // Check if user already exists by phone or email
            $existingUser = null;
            if (!empty($phone)) {
                $existingUser = User::where('phone', $phone)->first();
            }
            if (!$existingUser && !empty($email)) {
                $existingUser = User::where('email', $email)->first();
            }

            if ($existingUser) {
                $patient = $existingUser;
                Log::info("Existing user found (ID {$patient->id}) for booking {$booking->booking_reference}");
            } else {
                // Generate email if not provided (using phone)
                $userEmail = !empty($email) ? $email : ('patient_' . preg_replace('/[^0-9]/', '', $phone) . '@yonis-app.com');

                // Create new client user account
                $patient = User::create([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $userEmail,
                    'password' => Hash::needsRehash($plainPassword) ? $plainPassword : Hash::make($plainPassword),
                    'role' => 'patient',
                ]);

                Log::info("Created new patient user account (ID {$patient->id}) for booking {$booking->booking_reference}");
            }

            // Assign patient to booking and clear temporary data
            $booking->patient_id = $patient->id;
            $booking->temp_user_data = null;
        }

        // Update status
        $booking->status = 'Confirmed';
        $booking->save();

        $payment->status = 'Paid';
        $payment->save();

        return $patient;
    }
}
