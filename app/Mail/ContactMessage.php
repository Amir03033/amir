<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $senderMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('site.name')),
            replyTo: [new Address($this->senderEmail, $this->senderName)],
            subject: 'Portfolio contact van '.$this->senderName,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact',
        );
    }
}
