<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'property_id', 'user_id', 'ip_address', 'user_agent', 'viewed_at',
    ];

    protected $casts = ['viewed_at' => 'datetime'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
