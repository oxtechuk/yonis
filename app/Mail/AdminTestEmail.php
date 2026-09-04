<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminTestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $doctorName;
    public string $testTime;

    /**
     * Create a new message instance.
     */
    public function __construct(string $doctorName = 'د. يونس المرشد')
    {
        $this->doctorName = $doctorName;
        $this->testTime = now()->format('Y-m-d H:i:s');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "✅ اختبار اتصال البريد بنجاح - عيادة د. يونس المرشدي",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_test_email',
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
