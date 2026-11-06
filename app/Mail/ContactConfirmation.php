<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent to the visitor after the contact form, in the language they used the site in. */
class ContactConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $senderName,
        public string $senderMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('site.name')),
            replyTo: [new Address(config('site.email'), config('site.name'))],
            subject: __('messages.confirmation.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.contact-confirmation');
    }
}
