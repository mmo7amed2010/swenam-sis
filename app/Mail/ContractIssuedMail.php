<?php

namespace App\Mail;

use App\Models\StudentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ContractIssuedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public StudentApplication $application,
        public string $pdfPath
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Contract - '.$this->application->reference_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contract-issued',
        );
    }

    public function attachments(): array
    {
        if (Storage::exists($this->pdfPath)) {
            return [
                Attachment::fromStorage($this->pdfPath)
                    ->as('enrollment-contract.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
