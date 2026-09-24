<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'property_type_id', 'category_id',
        'title', 'slug', 'description', 'listing_type',
        'price', 'price_type', 'currency', 'negotiable',
        'bedrooms', 'bathrooms', 'area', 'area_unit',
        'floor_number', 'total_floors', 'year_built',
        'furnished', 'parking_spaces', 'status',
        'is_featured', 'is_verified', 'verified_at', 'verified_by',
        'featured_from', 'featured_until', 'featured_priority', 'featured_reason',
        'rejection_reason', 'views_count', 'favorites_count',
        'contact_count', 'meta_title', 'meta_description',
        'published_at', 'expires_at',
    ];

    protected $casts = [
        'price'             => 'decimal:2',
        'area'              => 'decimal:2',
        'negotiable'        => 'boolean',
        'is_featured'       => 'boolean',
        'is_verified'       => 'boolean',
        'featured_priority' => 'integer',
        'featured_from'     => 'datetime',
        'featured_until'    => 'datetime',
        'verified_at'       => 'datetime',
        'published_at'      => 'datetime',
        'expires_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    public function videos()
    {
        return $this->hasMany(PropertyVideo::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'property_amenities');
    }

    public function features()
    {
        return $this->hasMany(PropertyFeature::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function views()
    {
        return $this->hasMany(PropertyView::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function availability()
    {
        return $this->hasMany(PropertyAvailability::class);
    }

    public function shortRentalBookings()
    {
        return $this->hasMany(ShortRentalBooking::class);
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getPrimaryImageUrlAttribute(): string
    {
        return $this->primaryImage?->url ?? $this->images?->first()?->url ?? 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80';
    }

    public function getImageAttribute(): string
    {
        return $this->primary_image_url;
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForSale($query)
    {
        return $query->where('listing_type', 'sale');
    }

    public function scopeForRent($query)
    {
        return $query->where('listing_type', 'rent');
    }

    public function scopeShortRent($query)
    {
        return $query->where('listing_type', 'short_rent');
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function isFavoritedBy(User $user): bool
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->where('status', 'approved')->avg('rating') ?? 0, 1);
    }
}
