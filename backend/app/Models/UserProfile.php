<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 'bio', 'date_of_birth', 'gender',
        'national_id', 'national_id_image', 'address',
        'city_id', 'is_verified', 'verified_at', 'social_links',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_verified'   => 'boolean',
        'verified_at'   => 'datetime',
        'social_links'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
