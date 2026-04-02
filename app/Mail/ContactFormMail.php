<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $text;
    public $name;
    public $email;

    public function __construct($text, $name, $email)
    {
        $this->text = nl2br($text);
        $this->name = $name;
        $this->email = $email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Neue Kontaktanfrage',
            replyTo: [$this->email],
        );
    }



    public function content(): Content
    {
        return new Content(
            view: 'mails.contactMailView',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
