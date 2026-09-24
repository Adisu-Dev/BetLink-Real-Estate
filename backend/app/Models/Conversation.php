<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['property_id', 'type'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
                    ->withPivot('last_read_at', 'created_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function getUnreadCountForUser(int $userId): int
    {
        $participant = $this->participants()
            ->where('user_id', $userId)->first();

        if (!$participant) return 0;

        $lastReadAt = $participant->pivot->last_read_at;

        $query = $this->messages()->where('sender_id', '!=', $userId);

        if ($lastReadAt) {
            $query->where('created_at', '>', $lastReadAt);
        }

        return $query->count();
    }
}
