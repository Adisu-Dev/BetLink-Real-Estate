<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    protected $fillable = ['sub_city_id', 'name', 'slug', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function subCity()
    {
        return $this->belongsTo(SubCity::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
