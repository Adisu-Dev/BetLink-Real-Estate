<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyApproved extends Notification implements ShouldQueue
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
            ->subject('Your Property is Live — BetLink')
            ->greeting("Congratulations {$notifiable->name}!")
            ->line("Your property **{$this->property->title}** has been approved and is now live.")
            ->action('View Property', config('app.frontend_url') . "/properties/{$this->property->slug}")
            ->line('Thank you for listing with BetLink.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'property_approved',
            'property_id'    => $this->property->id,
            'property_title' => $this->property->title,
            'property_slug'  => $this->property->slug,
            'message'        => "Your property \"{$this->property->title}\" has been approved and is now live.",
        ];
    }
}
