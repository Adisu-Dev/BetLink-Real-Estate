<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'property_id', 'owner_id', 'visitor_id',
        'scheduled_at', 'duration_minutes', 'status', 'type',
        'message', 'owner_notes', 'cancellation_reason',
        'cancelled_by', 'reminder_sent',
    ];

    protected $casts = [
        'scheduled_at'  => 'datetime',
        'reminder_sent' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function visitor()
    {
        return $this->belongsTo(User::class, 'visitor_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed'])
                     ->where('scheduled_at', '>=', now());
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }
}
