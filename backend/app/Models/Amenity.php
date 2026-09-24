<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'category', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_amenities');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
