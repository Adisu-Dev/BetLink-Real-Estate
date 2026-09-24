<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $property = $this->appointment->property?->title ?? 'a property';
        $date     = $this->appointment->scheduled_at?->format('D, M j Y \a\t g:i A');

        return (new MailMessage)
            ->subject('Appointment Reminder — BetLink')
            ->greeting("Hello {$notifiable->name},")
            ->line("Reminder: You have an appointment for **{$property}** tomorrow.")
            ->line("Scheduled: {$date}")
            ->action('View Details', config('app.frontend_url') . '/appointments')
            ->line('Thank you for using BetLink.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'appointment_reminder',
            'appointment_id' => $this->appointment->id,
            'property_title' => $this->appointment->property?->title,
            'scheduled_at'   => $this->appointment->scheduled_at,
            'message'        => 'Reminder: You have an appointment tomorrow.',
        ];
    }
}
