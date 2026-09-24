<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCity extends Model
{
    protected $fillable = ['city_id', 'name', 'slug', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
