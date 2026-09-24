<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
        public string $loginUrl = 'http://localhost:5173/login'
    ) {
        $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
        $this->loginUrl = rtrim($frontendUrl, '/') . '/login';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to BetLink - Your Account Access Credentials',
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
        $userName = htmlspecialchars($this->user->name ?? 'Valued User');
        $userEmail = htmlspecialchars($this->user->email);
        $userRole = ucfirst(htmlspecialchars($this->user->getRoleNames()->first() ?? 'Buyer'));
        $password = htmlspecialchars($this->plainPassword);
        $loginUrl = htmlspecialchars($this->loginUrl);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to BetLink</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 580px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #0f172a; padding: 32px 24px; text-align: center; color: #ffffff; }
        .logo { font-size: 26px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff; }
        .logo span { color: #10b981; }
        .content { padding: 32px 28px; line-height: 1.6; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        .credentials-card { background-color: #f1f5f9; border-radius: 12px; border: 1px solid #cbd5e1; padding: 20px; margin: 24px 0; }
        .credential-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #cbd5e1; font-size: 14px; }
        .credential-row:last-child { border-bottom: none; }
        .label { font-weight: 600; color: #64748b; }
        .value { font-weight: 700; color: #0f172a; font-family: monospace; font-size: 15px; }
        .btn-container { text-align: center; margin: 32px 0 20px 0; }
        .btn { display: inline-block; background-color: #0f172a; color: #ffffff !important; padding: 14px 32px; border-radius: 12px; font-weight: 800; font-size: 14px; text-decoration: none; transition: background 0.2s ease; }
        .notice { font-size: 12px; color: #64748b; background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px 14px; border-radius: 4px; margin-top: 20px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Bet<span>Link</span></div>
            <p style="margin: 6px 0 0 0; font-size: 13px; color: #94a3b8; font-weight: 500;">Ethiopia's Premier Real Estate Marketplace</p>
        </div>
        <div class="content">
            <div class="greeting">Hello {$userName},</div>
            <p style="margin: 0 0 16px 0; font-size: 14px; color: #334155;">
                Welcome to <strong>BetLink</strong>! Your account as a <strong>{$userRole}</strong> has been successfully created.
            </p>
            <p style="margin: 0 0 20px 0; font-size: 14px; color: #334155;">
                Here are your initial login credentials generated securely for your account:
            </p>

            <div class="credentials-card">
                <div class="credential-row">
                    <span class="label">Login Email:</span>
                    <span class="value" style="font-family: inherit;">{$userEmail}</span>
                </div>
                <div class="credential-row">
                    <span class="label">Access Password:</span>
                    <span class="value" style="color: #059669; font-size: 16px;">{$password}</span>
                </div>
                <div class="credential-row">
                    <span class="label">Account Role:</span>
                    <span class="value" style="font-family: inherit;">{$userRole}</span>
                </div>
            </div>

            <div class="btn-container">
                <a href="{$loginUrl}" class="btn">Log In to Your Account</a>
            </div>

            <div class="notice">
                <strong>🔒 Security Tip:</strong> For your security, you can change this password at any time in your <strong>Account Settings</strong> after signing in.
            </div>
        </div>
        <div class="footer">
            &copy; 2026 BetLink Real Estate Platform. All rights reserved.<br>
            Addis Ababa, Ethiopia | Contact: support@betlink.et
        </div>
    </div>
</body>
</html>
HTML;
    }
}
