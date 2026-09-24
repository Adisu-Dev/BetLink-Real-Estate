<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortRentalBooking extends Model
{
    protected $fillable = [
        'property_id', 'guest_id', 'check_in_date', 'check_out_date',
        'nights', 'guests_count', 'total_price', 'service_fee',
        'status', 'special_requests',
    ];

    protected $casts = [
        'check_in_date'  => 'date',
        'check_out_date' => 'date',
        'total_price'    => 'decimal:2',
        'service_fee'    => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }
}
