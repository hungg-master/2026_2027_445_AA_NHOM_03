<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BusinessEventMail extends Mailable
{
    public function __construct(public string $eventType, public string $body) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SmartTrial - Thông báo học tập');
    }

    public function content(): Content
    {
        return new Content(htmlString: '<p>'.nl2br(htmlspecialchars($this->body, ENT_QUOTES, 'UTF-8')).'</p>');
    }
}
