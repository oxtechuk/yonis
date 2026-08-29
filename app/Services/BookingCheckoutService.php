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
     * Returns an array containing the patient User and a boolean indicating if a new account was created.
     *
     * @param Booking $booking
     * @param Payment $payment
     * @param string|null $overridePassword
     * @return array{patient: User, is_new_user: bool}
     */
    public function confirmBookingPayment(Booking $booking, Payment $payment, ?string $overridePassword = null): array
    {
        $patient = $booking->patient;
        $isNewUser = false;

        // If booking does not have an associated patient yet, process temp_user_data
        if (!$patient && !empty($booking->temp_user_data)) {
            $tempData = $booking->temp_user_data;
            $phone = $tempData['phone'] ?? null;
            $email = $tempData['email'] ?? null;
            $name = $tempData['name'] ?? 'عميل جديد';
            $plainPassword = $overridePassword ?? ($tempData['password'] ?? '12345678');

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
                $isNewUser = false;
                Log::info("Existing user found (ID {$patient->id}) for booking {$booking->booking_reference}");
            } else {
                // Generate email if not provided (using phone)
                $userEmail = !empty($email) ? $email : ('patient_' . preg_replace('/[^0-9]/', '', (string)$phone) . '@yonis-app.com');

                // Create new client user account
                $patient = User::create([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $userEmail,
                    'password' => Hash::needsRehash($plainPassword) ? $plainPassword : Hash::make($plainPassword),
                    'role' => 'patient',
                ]);

                $isNewUser = true;
                Log::info("Created new patient user account (ID {$patient->id}) for booking {$booking->booking_reference}");
            }

            // Assign patient to booking and clear temporary data
            $booking->patient_id = $patient->id;
            $booking->temp_user_data = null;
        } elseif ($patient) {
            $isNewUser = false;
        }

        // Update status
        $booking->status = 'Confirmed';
        $booking->save();

        $payment->status = 'Paid';
        $payment->save();

        return [
            'patient' => $patient,
            'is_new_user' => $isNewUser,
        ];
    }
}
