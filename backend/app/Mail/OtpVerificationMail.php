<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $userName = 'Valued User',
        public int $expiryMinutes = 10
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->otp} is your BetLink email verification code",
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtmlTemplate(),
        );
    }

    private function buildHtmlTemplate(): string
    {
        $safeName = htmlspecialchars($this->userName);
        $safeOtp = htmlspecialchars($this->otp);
        $minutes = (int)$this->expiryMinutes;

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BetLink Email Verification</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px; color: #1e293b; }
        .container { max-width: 520px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background-color: #0f172a; padding: 28px 24px; text-align: center; color: #ffffff; }
        .logo { font-size: 24px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff; }
        .content { padding: 32px 28px; line-height: 1.6; text-align: center; }
        .otp-box { margin: 24px auto; padding: 18px 28px; background-color: #f1f5f9; border-radius: 12px; display: inline-block; letter-spacing: 8px; font-size: 32px; font-weight: 900; color: #0f172a; border: 2px dashed #94a3b8; }
        .badge { display: inline-block; background-color: #fef3c7; color: #92400e; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; margin-top: 8px; }
        .footer { background-color: #f8fafc; padding: 20px 24px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">BetLink</div>
            <p style="margin: 6px 0 0; font-size: 12px; color: #94a3b8; letter-spacing: 0.5px;">ETHIOPIAN REAL ESTATE PLATFORM</p>
        </div>
        <div class="content">
            <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 0;">Verify Your Email Address</h2>
            <p style="color: #475569; font-size: 14px; margin-bottom: 8px;">Hello {$safeName},</p>
            <p style="color: #475569; font-size: 14px; line-height: 1.5;">
                Thank you for joining BetLink. Use the 6-digit verification code below to complete your registration and activate your account:
            </p>
            
            <div>
                <div class="otp-box">{$safeOtp}</div>
            </div>

            <div class="badge">⏱ Valid for {$minutes} minutes only</div>

            <p style="color: #64748b; font-size: 12px; margin-top: 24px;">
                If you did not request this verification code, please disregard this email.
            </p>
        </div>
        <div class="footer">
            &copy; 2026 BetLink Real Estate Platform. All rights reserved.<br>
            Addis Ababa, Ethiopia.
        </div>
    </div>
</body>
</html>
HTML;
    }
}
