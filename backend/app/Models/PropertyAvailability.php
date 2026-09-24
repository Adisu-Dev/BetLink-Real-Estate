<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAvailability extends Model
{
    protected $table = 'property_availability';

    protected $fillable = ['property_id', 'date', 'is_available', 'price_override'];

    protected $casts = [
        'date'           => 'date',
        'is_available'   => 'boolean',
        'price_override' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
