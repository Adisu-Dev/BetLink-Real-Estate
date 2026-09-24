<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewReviewReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'new_review',
            'review_id'   => $this->review->id,
            'reviewer'    => $this->review->reviewer?->name,
            'rating'      => $this->review->rating,
            'message'     => "{$this->review->reviewer?->name} left a {$this->review->rating}-star review.",
        ];
    }
}
