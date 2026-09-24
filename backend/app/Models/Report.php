<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reportable_type', 'reportable_id', 'reporter_id',
        'reason', 'description', 'status',
        'reviewed_by', 'reviewed_at', 'admin_notes',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function reportable()
    {
        return $this->morphTo();
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
