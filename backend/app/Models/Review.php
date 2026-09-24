<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reviewable_type', 'reviewable_id', 'reviewer_id',
        'rating', 'title', 'body', 'pros', 'cons',
        'status', 'is_verified_stay',
    ];

    protected $casts = [
        'rating'           => 'integer',
        'is_verified_stay' => 'boolean',
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function response()
    {
        return $this->hasOne(ReviewResponse::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
