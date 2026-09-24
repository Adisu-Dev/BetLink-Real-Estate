<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['property_type_id', 'name', 'slug', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
