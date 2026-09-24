<?php
namespace App\Services;

use App\Mail\OtpVerificationMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public const EXPIRATION_MINUTES = 2;

    public function __construct(private SmsService $smsService) {}

    public function generateAndSend(string $email, array $registrationPayload, string $channel = 'both'): array
    {
        $email = strtolower(trim($email));
        $otp = sprintf('%06d', random_int(100000, 999999));
        $expiresAt = now()->addMinutes(self::EXPIRATION_MINUTES);

        // 1. Cache for fast lookup
        Cache::put("reg_otp_{$email}", [
            'otp'        => $otp,
            'payload'    => $registrationPayload,
            'expires_at' => $expiresAt->timestamp,
        ], self::EXPIRATION_MINUTES * 60);

        // 2. Persist to DB (with graceful cache fallback if DB is temporarily unreachable)
        try {
            DB::table('email_otps')->insert([
                'email'      => $email,
                'otp_code'   => $otp,
                'payload'    => json_encode($registrationPayload),
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("Could not persist OTP to email_otps table (Cache remains active): " . $e->getMessage());
        }

        $userName = $registrationPayload['name'] ?? 'Valued User';
        $phoneNumber = $registrationPayload['phone'] ?? null;

        $emailDispatched = false;
        $smsDispatched = false;

        // 3. Dispatch Email if requested
        if (in_array($channel, ['email', 'both'])) {
            try {
                Mail::to($email)->send(new OtpVerificationMail($otp, $userName, self::EXPIRATION_MINUTES));
                $emailDispatched = true;
            } catch (\Throwable $e) {
                Log::warning("Failed sending OTP email to {$email}: " . $e->getMessage());
            }
        }

        // 4. Dispatch SMS if requested or as backup if phone is provided
        if ((in_array($channel, ['sms', 'both']) || !$emailDispatched) && !empty($phoneNumber)) {
            try {
                $domain = parse_url(config('app.frontend_url', 'http://localhost:5173'), PHP_URL_HOST) ?: 'localhost';
                $smsText = "BetLink: Your verification code is {$otp}. Valid for 2 minutes. Do not share this code.\n\n@{$domain} #{$otp}";
                $smsDispatched = $this->smsService->send($phoneNumber, $smsText);
            } catch (\Throwable $e) {
                Log::warning("Failed sending OTP SMS to {$phoneNumber}: " . $e->getMessage());
            }
        }

        // Always log OTP for audit and developer visibility
        Log::info("BetLink OTP generated for {$email} (Phone: {$phoneNumber}): [{$otp}] via channel [{$channel}]");

        $isDevOrLog = config('app.debug') || config('mail.default') === 'log' || config('app.env') === 'local';

        return [
            'email'           => $email,
            'phone'           => $phoneNumber,
            'channel'         => $channel,
            'email_sent'      => $emailDispatched,
            'sms_sent'        => $smsDispatched,
            'expires_in_secs' => self::EXPIRATION_MINUTES * 60,
            'expires_at'      => $expiresAt->toIso8601String(),
            'dev_otp'         => $isDevOrLog ? $otp : null,
        ];
    }

    /**
     * Verify the entered 6-digit OTP.
     */
    public function verify(string $email, string $otp): ?array
    {
        $email = strtolower(trim($email));
        $otp = trim($otp);

        // 1. Check cache first
        $cached = Cache::get("reg_otp_{$email}");
        if ($cached && isset($cached['otp']) && $cached['otp'] === $otp) {
            Cache::forget("reg_otp_{$email}");

            try {
                DB::table('email_otps')
                    ->where('email', $email)
                    ->where('otp_code', $otp)
                    ->whereNull('verified_at')
                    ->update(['verified_at' => now(), 'updated_at' => now()]);
            } catch (\Throwable $e) {
                Log::warning("Could not mark OTP as verified in DB: " . $e->getMessage());
            }

            return $cached['payload'] ?? [];
        }

        // 2. Fallback to database record if cache missed
        try {
            $record = DB::table('email_otps')
                ->where('email', $email)
                ->where('otp_code', $otp)
                ->where('expires_at', '>=', now())
                ->whereNull('verified_at')
                ->orderByDesc('id')
                ->first();

            if ($record) {
                DB::table('email_otps')
                    ->where('id', $record->id)
                    ->update(['verified_at' => now(), 'updated_at' => now()]);

                Cache::forget("reg_otp_{$email}");
                return json_decode($record->payload, true) ?: [];
            }
        } catch (\Throwable $e) {
            Log::warning("Database OTP fallback check failed: " . $e->getMessage());
        }

        return null;
    }
}
