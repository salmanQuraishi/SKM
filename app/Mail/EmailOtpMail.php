<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $action;
    public string $recipientName;
    public int $expiryMinutes;

    public function __construct(string $otp, string $action = 'register', string $recipientName = 'Valued Customer', int $expiryMinutes = 10)
    {
        $this->otp = $otp;
        $this->action = $action;
        $this->recipientName = $recipientName;
        $this->expiryMinutes = $expiryMinutes;
    }

    public function envelope(): Envelope
    {
        $storeName = \App\Models\Setting::get('store_name', 'SKM Saller');
        $subject = match ($this->action) {
            'login' => "Your {$storeName} Login OTP: {$this->otp}",
            default => "Verify Your Email - {$storeName} OTP: {$this->otp}",
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otp' => $this->otp,
                'action' => $this->action,
                'recipientName' => $this->recipientName,
                'expiryMinutes' => $this->expiryMinutes,
            ],
        );
    }
}
