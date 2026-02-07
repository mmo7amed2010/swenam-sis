<?php

namespace App\Mail;

use App\Models\StudentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public StudentApplication $application
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Approved - '.$this->application->reference_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
