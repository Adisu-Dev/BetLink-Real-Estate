<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'addressable_type', 'addressable_id',
        'city_id', 'sub_city_id', 'neighborhood_id',
        'street', 'kebele', 'house_number',
        'latitude', 'longitude', 'full_address',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function subCity()
    {
        return $this->belongsTo(SubCity::class);
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }
}
