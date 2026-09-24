<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerAvailability extends Model
{
    use HasFactory;

    protected $table = 'seller_availabilities';

    protected $fillable = [
        'user_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'day_of_week'           => 'integer',
        'slot_duration_minutes' => 'integer',
        'is_active'             => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
