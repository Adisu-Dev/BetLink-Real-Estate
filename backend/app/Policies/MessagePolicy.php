<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function view(User $user, Message $message): bool
    {
        return $message->conversation->participants()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->id === $message->sender_id || $user->isAdmin();
    }
}
