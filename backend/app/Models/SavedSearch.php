<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    protected $fillable = [
        'user_id', 'name', 'filters', 'alert_enabled', 'last_alert_at',
    ];

    protected $casts = [
        'filters'       => 'array',
        'alert_enabled' => 'boolean',
        'last_alert_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
