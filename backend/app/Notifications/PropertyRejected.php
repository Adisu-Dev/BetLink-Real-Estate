<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Property $property) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Property Listing Update — BetLink')
            ->greeting("Hello {$notifiable->name},")
            ->line("Unfortunately, your property **{$this->property->title}** was not approved.")
            ->line("Reason: {$this->property->rejection_reason}")
            ->line("Please update your listing and resubmit.")
            ->action('Edit Property', config('app.frontend_url') . "/owner/properties/{$this->property->id}/edit")
            ->line('Thank you for using BetLink.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => 'property_rejected',
            'property_id'      => $this->property->id,
            'property_title'   => $this->property->title,
            'rejection_reason' => $this->property->rejection_reason,
            'message'          => "Your property \"{$this->property->title}\" was rejected. Please review and resubmit.",
        ];
    }
}
