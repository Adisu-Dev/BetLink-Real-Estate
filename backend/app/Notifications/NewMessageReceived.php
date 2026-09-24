<?php

namespace App\Notifications;

use App\Models\Message;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        // Also dispatch SMS alert directly to phone if available
        $this->dispatchSmsNotification($notifiable);

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $senderName = $this->message->sender?->name ?? 'A user';
        $preview = substr($this->message->body ?? 'New attachment received', 0, 150);
        $chatUrl = rtrim(config('app.frontend_url', 'http://localhost:5173'), '/') . '/messages';

        return (new MailMessage)
            ->subject("New message from {$senderName} — BetLink")
            ->greeting("Hello {$notifiable->name},")
            ->line("You received a new direct message from **{$senderName}** on BetLink:")
            ->line("\"{$preview}\"")
            ->action('Open & Reply to Message', $chatUrl)
            ->line('Replying quickly helps keep prospective buyers and clients engaged.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'new_message',
            'conversation_id' => $this->message->conversation_id,
            'sender_id'       => $this->message->sender_id,
            'sender_name'     => $this->message->sender?->name,
            'preview'         => substr($this->message->body, 0, 100),
            'message'         => "New message from {$this->message->sender?->name}",
        ];
    }

    /**
     * Send instant SMS notification to user's phone
     */
    protected function dispatchSmsNotification(object $notifiable): void
    {
        if (empty($notifiable->phone)) {
            return;
        }

        try {
            $senderName = $this->message->sender?->name ?? 'A user';
            $preview = substr(strip_tags($this->message->body ?? 'Attachment sent'), 0, 80);
            $appUrl = rtrim(config('app.frontend_url', 'http://localhost:5173'), '/') . '/messages';

            $smsText = "BetLink: New message from {$senderName}: \"{$preview}\". Log in to reply: {$appUrl}";

            app(SmsService::class)->send($notifiable->phone, $smsText);
        } catch (\Throwable $e) {
            Log::warning("Failed to dispatch SMS for new message to {$notifiable->phone}: " . $e->getMessage());
        }
    }
}
