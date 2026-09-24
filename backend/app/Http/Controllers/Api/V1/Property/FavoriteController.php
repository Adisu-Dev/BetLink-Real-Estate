<?php

namespace App\Http\Controllers\Api\V1\Property;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponse;

    /**
     * Get user favorites with full property details
     */
    public function index(Request $request): JsonResponse
    {
        $favorites = $request->user()
            ->favorites()
            ->with(['property.primaryImage', 'property.images', 'property.address.city', 'property.address.subCity', 'property.propertyType', 'property.owner'])
            ->latest()
            ->get()
            ->map(function ($fav) {
                $prop = $fav->property;
                if (!$prop) return null;
                return [
                    'id'            => $prop->id,
                    'favorite_id'   => $fav->id,
                    'title'         => $prop->title,
                    'slug'          => $prop->slug,
                    'description'   => $prop->description,
                    'listing_type'  => $prop->listing_type,
                    'price'         => (float) $prop->price,
                    'price_type'    => $prop->price_type,
                    'currency'      => $prop->currency ?? 'ETB',
                    'bedrooms'      => $prop->bedrooms,
                    'bathrooms'     => $prop->bathrooms,
                    'area'          => $prop->area,
                    'is_verified'   => (bool) $prop->is_verified,
                    'is_featured'   => (bool) $prop->is_featured,
                    'image'         => $prop->primaryImage?->url ?? $prop->images?->first()?->url ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
                    'location'      => ($prop->address?->subCity?->name ? $prop->address->subCity->name . ', ' : '') . ($prop->address?->city?->name ?? 'Addis Ababa, Ethiopia'),
                    'property_type' => $prop->propertyType?->name ?? 'Residential',
                    'owner'         => [
                        'id'    => $prop->owner?->id ?? 1,
                        'name'  => $prop->owner?->name ?? 'Verified Landlord',
                        'role'  => 'Landlord',
                        'phone' => $prop->owner?->phone ?? '+251 91 123 4567',
                    ],
                    'created_at'    => $fav->created_at,
                ];
            })
            ->filter()
            ->values();

        return $this->success($favorites, 'Favorites retrieved successfully');
    }

    /**
     * Add a property to favorites
     */
    public function store(Request $request, int $propertyId): JsonResponse
    {
        $property = Property::findOrFail($propertyId);

        if ($property->user_id === $request->user()->id) {
            return $this->error('You cannot favorite your own property listing.', 422);
        }

        $favorite = Favorite::firstOrCreate([
            'user_id'     => $request->user()->id,
            'property_id' => $property->id,
        ]);

        $property->increment('favorites_count');
        if ($property->views_count < $property->favorites_count) {
            $property->views_count = $property->favorites_count + 1;
            $property->save();
        }
        \Illuminate\Support\Facades\Cache::forget("buyer_dashboard_{$request->user()->id}");

        return $this->created($favorite, 'Added to favorites');
    }

    /**
     * Remove a property from favorites
     */
    public function destroy(Request $request, int $propertyId): JsonResponse
    {
        $deleted = Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $propertyId)
            ->delete();

        if ($deleted) {
            Property::where('id', $propertyId)->decrement('favorites_count');
        }

        \Illuminate\Support\Facades\Cache::forget("buyer_dashboard_{$request->user()->id}");

        return $this->success(null, 'Removed from favorites');
    }

    /**
     * Check if a property is favorited
     */
    public function check(Request $request, int $propertyId): JsonResponse
    {
        $isFavorited = Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $propertyId)
            ->exists();

        return $this->success(['is_favorited' => $isFavorited]);
    }
}
