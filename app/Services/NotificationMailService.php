<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Setting;
use App\Mail\DoctorNewBookingAlert;
use App\Mail\PatientBookingReceived;
use App\Mail\PatientBookingConfirmed;
use App\Mail\AdminTestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationMailService
{
    /**
     * Send email notification to Doctor about a new booking or payment confirmation.
     */
    public static function notifyDoctorNewBooking(Booking $booking, string $eventTitle = 'حجز جديد بانتظار المراجعة'): void
    {
        if (!self::isEmailEnabled()) {
            return;
        }

        $doctorEmail = self::getDoctorEmail();
        if (empty($doctorEmail)) {
            Log::warning("NotificationMailService: Doctor notification email is empty.");
            return;
        }

        try {
            Mail::to($doctorEmail)->send(new DoctorNewBookingAlert($booking, $eventTitle));
            Log::info("NotificationMailService: Doctor alert sent to {$doctorEmail} for booking {$booking->booking_reference}");
        } catch (\Throwable $e) {
            Log::error("NotificationMailService Error [Doctor Alert]: " . $e->getMessage());
        }
    }

    /**
     * Send receipt/under-review confirmation email to Patient.
     */
    public static function notifyPatientBookingReceived(Booking $booking): void
    {
        if (!self::isEmailEnabled()) {
            return;
        }

        $patientEmail = $booking->patient?->email ?? ($booking->temp_user_data['email'] ?? null);
        if (empty($patientEmail) || !filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($patientEmail)->send(new PatientBookingReceived($booking));
            Log::info("NotificationMailService: Patient review notice sent to {$patientEmail} for booking {$booking->booking_reference}");
        } catch (\Throwable $e) {
            Log::error("NotificationMailService Error [Patient Review Notice]: " . $e->getMessage());
        }
    }

    /**
     * Send final confirmation email to Patient when Doctor confirms the appointment.
     */
    public static function notifyPatientBookingConfirmed(Booking $booking): void
    {
        if (!self::isEmailEnabled()) {
            return;
        }

        $patientEmail = $booking->patient?->email ?? ($booking->temp_user_data['email'] ?? null);
        if (empty($patientEmail) || !filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($patientEmail)->send(new PatientBookingConfirmed($booking));
            Log::info("NotificationMailService: Patient confirmation sent to {$patientEmail} for booking {$booking->booking_reference}");
        } catch (\Throwable $e) {
            Log::error("NotificationMailService Error [Patient Confirmed Notice]: " . $e->getMessage());
        }
    }

    /**
     * Send a test email to verify SMTP credentials.
     */
    public static function sendTestEmail(string $recipientEmail): bool
    {
        $doctorName = Setting::get('doctor_name', 'د. يونس المرشد');
        Mail::to($recipientEmail)->send(new AdminTestEmail($doctorName));
        return true;
    }

    /**
     * Check if email notifications are enabled globally.
     */
    public static function isEmailEnabled(): bool
    {
        return Setting::get('email_notifications_enabled', '1') === '1';
    }

    /**
     * Get doctor/admin recipient email.
     */
    public static function getDoctorEmail(): string
    {
        return Setting::get('notification_email') 
            ?: config('mail.from.address', env('ADMIN_NOTIFICATION_EMAIL', 'dr.yonis@example.com'));
    }
}
