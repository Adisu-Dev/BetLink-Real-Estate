<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'slug', 'region', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function subCities()
    {
        return $this->hasMany(SubCity::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
