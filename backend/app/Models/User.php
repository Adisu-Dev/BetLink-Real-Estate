<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $guard_name = 'sanctum';

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'status', 'avatar', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'avatar',
        'avatar_url',
    ];

    public function getAvatarAttribute($value)
    {
        if ($value) {
            if (str_starts_with($value, 'http') || str_starts_with($value, 'data:')) {
                return $value;
            }
            $clean = ltrim($value, '/');
            if (str_starts_with($clean, 'storage/')) {
                $clean = substr($clean, 8);
            }
            return url('storage/' . $clean);
        }
        if ($this->profile?->avatar) {
            $pav = $this->profile->avatar;
            if (str_starts_with($pav, 'http') || str_starts_with($pav, 'data:')) {
                return $pav;
            }
            $clean = ltrim($pav, '/');
            if (str_starts_with($clean, 'storage/')) {
                $clean = substr($clean, 8);
            }
            return url('storage/' . $clean);
        }
        return null;
    }

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'phone_verified_at'  => 'datetime',
        'deleted_at'         => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function savedSearches()
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function appointmentsAsVisitor()
    {
        return $this->hasMany(Appointment::class, 'visitor_id');
    }

    public function appointmentsAsOwner()
    {
        return $this->hasMany(Appointment::class, 'owner_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(
            Conversation::class,
            'conversation_participants'
        )->withPivot('last_read_at', 'created_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function verificationRequests()
    {
        return $this->hasMany(VerificationRequest::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isBuyer(): bool
    {
        return $this->hasRole('buyer');
    }

    public function isAgent(): bool
    {
        return $this->hasRole('agent');
    }

    public function getAvatarUrlAttribute(): string
    {
        $av = $this->attributes['avatar'] ?? null;
        if (!$av) {
            $av = $this->profile?->avatar;
        }
        if ($av) {
            if (str_starts_with($av, 'http') || str_starts_with($av, 'data:')) {
                return $av;
            }
            $clean = ltrim($av, '/');
            if (str_starts_with($clean, 'storage/')) {
                $clean = substr($clean, 8);
            }
            return url('storage/' . $clean);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?: 'User') . '&background=1e293b&color=fff';
    }
}
