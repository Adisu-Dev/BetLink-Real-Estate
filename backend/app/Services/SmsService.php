<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS message to a recipient.
     * Supports:
     * - AfroMessage (Ethiopian SMS gateway)
     * - Twilio
     * - Generic Webhook / Log Driver
     *
     * @param string $toPhoneNumber Phone number in international or local format (e.g. +251911234567)
     * @param string $message The text message to dispatch
     * @return bool True if sent or successfully logged
     */
    public function send(string $toPhoneNumber, string $message): bool
    {
        $toPhoneNumber = trim($toPhoneNumber);
        $provider = env('SMS_PROVIDER', 'log'); // 'afromessage', 'twilio', 'webhook', 'log'

        // Log SMS dispatch in development and audit logs
        Log::info("BetLink SMS Dispatch [To: {$toPhoneNumber}] via [{$provider}]: {$message}");

        try {
            if ($provider === 'afromessage') {
                return $this->sendViaAfroMessage($toPhoneNumber, $message);
            }

            if ($provider === 'twilio') {
                return $this->sendViaTwilio($toPhoneNumber, $message);
            }

            if ($provider === 'webhook') {
                $webhookUrl = env('SMS_WEBHOOK_URL');
                if ($webhookUrl) {
                    $response = Http::timeout(5)->post($webhookUrl, [
                        'to'      => $toPhoneNumber,
                        'message' => $message,
                        'time'    => now()->toIso8601String(),
                    ]);
                    return $response->successful();
                }
            }

            // Default 'log' driver: successfully queued for local development
            return true;
        } catch (\Throwable $e) {
            Log::warning("Failed to send SMS to {$toPhoneNumber}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS via AfroMessage (Ethiopia)
     */
    private function sendViaAfroMessage(string $to, string $message): bool
    {
        $token = env('AFROMESSAGE_TOKEN');
        $sender = env('AFROMESSAGE_SENDER');

        if (!$token) {
            Log::warning("AfroMessage token not configured. SMS logged instead.");
            return true;
        }

        $cleanPhone = preg_replace('/[^\d+]/', '', $to);

        $payload = [
            'to'      => $cleanPhone,
            'message' => $message,
        ];

        if (!empty($sender)) {
            $payload['from'] = $sender;
        }

        $response = Http::withToken($token)
            ->timeout(6)
            ->post('https://api.afromessage.com/api/send', $payload);

        Log::info("AfroMessage response [Status: {$response->status()}]: " . $response->body());

        if (!$response->successful()) {
            return false;
        }

        $body = $response->json();
        if (isset($body['acknowledge']) && $body['acknowledge'] === 'error') {
            Log::warning("AfroMessage error: " . json_encode($body['response']['errors'] ?? $body['response']));
            return false;
        }

        return true;
    }

    /**
     * Send SMS via Twilio
     */
    private function sendViaTwilio(string $to, string $message): bool
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_FROM');

        if (!$sid || !$token || !$from) {
            Log::warning("Twilio credentials not configured. SMS logged instead.");
            return true;
        }

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->timeout(5)
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'To'   => $to,
                'From' => $from,
                'Body' => $message,
            ]);

        return $response->successful();
    }
}
