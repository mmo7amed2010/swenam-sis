<?php

namespace App\Mail;

use App\Models\StudentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoaRequestedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public StudentApplication $application
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notice of Assessment Required - '.$this->application->reference_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.noa-requested',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
