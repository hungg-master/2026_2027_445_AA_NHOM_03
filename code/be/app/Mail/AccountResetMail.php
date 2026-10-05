<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AccountResetMail extends Mailable
{
    public function __construct(public string $role, public string $email, public string $token) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SmartTrial - Đặt lại mật khẩu');
    }

    public function content(): Content
    {
        $url = config('supporting.reset_url').'?'.http_build_query(['role' => $this->role, 'email' => $this->email, 'token' => $this->token]);

        return new Content(htmlString: '<p>Yêu cầu đặt lại mật khẩu SmartTrial có hiệu lực trong 60 phút.</p><p><a href="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'">Đặt lại mật khẩu</a></p><p>Nếu bạn không yêu cầu, hãy bỏ qua thư này.</p>');
    }
}
