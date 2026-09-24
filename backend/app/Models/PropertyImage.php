<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    protected $fillable = [
        'property_id', 'url', 'thumbnail_url', 'cloudinary_id',
        'is_primary', 'caption', 'sort_order',
    ];

    protected $casts = ['is_primary' => 'boolean'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getUrlAttribute($value): string
    {
        if (!$value) return 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&q=80';
        if (str_starts_with($value, 'http')) return $value;
        return asset('storage/' . $value);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->url;
    }
}
