<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyVideo extends Model
{
    protected $fillable = [
        'property_id', 'url', 'platform', 'thumbnail_url', 'title',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
