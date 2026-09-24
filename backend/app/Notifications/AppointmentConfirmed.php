<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AppointmentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        // Dispatch SMS alert directly to phone if available
        $this->dispatchSmsNotification($notifiable);

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status      = $this->appointment->status;
        $property    = $this->appointment->property?->title ?? 'a property';
        $date        = $this->appointment->scheduled_at ? $this->appointment->scheduled_at->format('D, M j Y \a\t g:i A') : 'Scheduled time';
        $visitorName = $this->appointment->visitor?->name ?? 'A client';
        $ownerName   = $this->appointment->owner?->name ?? 'Property Owner';
        $isOwner     = $notifiable->id === $this->appointment->owner_id;

        $mail = (new MailMessage);

        if ($status === 'pending') {
            $mail->subject("New Property Tour Request: {$property} — BetLink")
                ->greeting("Hello {$notifiable->name},")
                ->line("**{$visitorName}** has requested a tour appointment for your property **{$property}**.")
                ->line("Requested Date & Time: **{$date}**")
                ->action('Review & Confirm Appointment', rtrim(config('app.frontend_url', 'http://localhost:5173'), '/') . '/appointments')
                ->line('Please review and confirm or reschedule as soon as possible.');
        } elseif ($status === 'confirmed') {
            $mail->subject("Appointment Confirmed: {$property} — BetLink")
                ->greeting("Hello {$notifiable->name},")
                ->line("Your viewing appointment for **{$property}** is **Confirmed**.")
                ->line("Confirmed Date & Time: **{$date}**")
                ->action('View Appointment Details', rtrim(config('app.frontend_url', 'http://localhost:5173'), '/') . '/appointments')
                ->line('Thank you for choosing BetLink Real Estate.');
        } else {
            $mail->subject("Appointment Update ({$status}): {$property} — BetLink")
                ->greeting("Hello {$notifiable->name},")
                ->line("Your appointment for **{$property}** has been updated to **{$status}**.")
                ->line("Date: **{$date}**")
                ->action('View Details', rtrim(config('app.frontend_url', 'http://localhost:5173'), '/') . '/appointments');
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'appointment_confirmed',
            'appointment_id' => $this->appointment->id,
            'property_id'    => $this->appointment->property_id,
            'property_title' => $this->appointment->property?->title,
            'status'         => $this->appointment->status,
            'scheduled_at'   => $this->appointment->scheduled_at,
            'message'        => "Appointment for {$this->appointment->property?->title} is {$this->appointment->status}.",
        ];
    }

    /**
     * Send instant SMS alert to the recipient's phone number
     */
    protected function dispatchSmsNotification(object $notifiable): void
    {
        if (empty($notifiable->phone)) {
            return;
        }

        try {
            $status      = $this->appointment->status;
            $property    = substr($this->appointment->property?->title ?? 'Property', 0, 30);
            $date        = $this->appointment->scheduled_at ? $this->appointment->scheduled_at->format('M d, h:i A') : '';
            $visitorName = $this->appointment->visitor?->name ?? 'A client';
            $isOwner     = $notifiable->id === $this->appointment->owner_id;
            $url         = rtrim(config('app.frontend_url', 'http://localhost:5173'), '/') . '/appointments';

            if ($status === 'pending' && $isOwner) {
                $smsText = "BetLink: New tour request from {$visitorName} for '{$property}' on {$date}. Review: {$url}";
            } elseif ($status === 'confirmed') {
                $smsText = "BetLink: Tour appointment confirmed for '{$property}' on {$date}. Details: {$url}";
            } else {
                $smsText = "BetLink: Appointment for '{$property}' is now {$status}. Details: {$url}";
            }

            app(SmsService::class)->send($notifiable->phone, $smsText);
        } catch (\Throwable $e) {
            Log::warning("Failed to dispatch SMS for appointment to {$notifiable->phone}: " . $e->getMessage());
        }
    }
}
