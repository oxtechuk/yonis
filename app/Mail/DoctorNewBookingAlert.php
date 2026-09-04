<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DoctorNewBookingAlert extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public string $eventTitle;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, string $eventTitle = 'حجز جديد بانتظار المراجعة')
    {
        $this->booking = $booking;
        $this->eventTitle = $eventTitle;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $ref = $this->booking->booking_reference;
        $patientName = $this->booking->patient?->name ?? ($this->booking->temp_user_data['name'] ?? 'مريض');
        
        return new Envelope(
            subject: "🔔 {$this->eventTitle} - {$ref} ({$patientName})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.doctor_new_booking',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
