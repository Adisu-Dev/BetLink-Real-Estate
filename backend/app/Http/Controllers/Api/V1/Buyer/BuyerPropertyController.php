<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuyerPropertyController extends Controller
{
    use ApiResponse;

    /**
     * Get filtered and paginated list of properties for Buyer Marketplace / Property Discovery.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Property::with(['primaryImage', 'images', 'propertyType', 'category', 'address.city', 'address.subCity', 'owner'])
            ->where(function ($q) {
                $q->whereIn('status', ['active', 'published', 'available'])
                  ->orWhereNull('status');
            });

        // 1. Search Query Filter (Title, Description, City, Sub-City, Street, Full Address)
        if ($search = trim((string)($request->input('search') ?? $request->input('q') ?? ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('address', function ($addrQ) use ($search) {
                      $addrQ->where('street', 'like', "%{$search}%")
                            ->orWhere('kebele', 'like', "%{$search}%")
                            ->orWhere('house_number', 'like', "%{$search}%")
                            ->orWhere('full_address', 'like', "%{$search}%")
                            ->orWhereHas('city', function ($cityQ) use ($search) {
                                $cityQ->where('name', 'like', "%{$search}%")
                                      ->orWhere('region', 'like', "%{$search}%");
                            })
                            ->orWhereHas('subCity', function ($subCityQ) use ($search) {
                                $subCityQ->where('name', 'like', "%{$search}%");
                            });
                  });
            });
        }

        // 2. Transaction / Listing Type Filter
        $type = strtolower((string)$request->input('type', $request->input('listing_type', 'all')));
        if ($type && $type !== 'all') {
            if ($type === 'sale' || $type === 'for sale' || $type === 'buy') {
                $query->where('listing_type', 'sale');
            } elseif ($type === 'rent' || $type === 'for rent') {
                $query->where('listing_type', 'rent');
            } elseif ($type === 'short_rent' || $type === 'short stay' || $type === 'shortstay') {
                $query->where('listing_type', 'short_rent');
            } else {
                $query->where('listing_type', $type);
            }
        }

        // 3. Category / Property Type Filter
        $category = strtolower((string)$request->input('category', $request->input('property_type', 'all')));
        if ($category && $category !== 'all') {
            $query->where(function ($q) use ($category) {
                $q->whereHas('propertyType', function ($typeQ) use ($category) {
                    $typeQ->where('name', 'like', "%{$category}%")
                          ->orWhere('slug', 'like', "%{$category}%");
                })->orWhereHas('category', function ($catQ) use ($category) {
                    $catQ->where('name', 'like', "%{$category}%")
                         ->orWhere('slug', 'like', "%{$category}%");
                });
            });
        }

        // 4. Price Filter
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', (float)$minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', (float)$maxPrice);
        }

        // 5. Bedrooms Filter
        if ($bedrooms = $request->input('bedrooms')) {
            if (is_numeric($bedrooms) && (int)$bedrooms > 0) {
                $bedCount = (int)$bedrooms;
                if ($bedCount >= 4) {
                    $query->where('bedrooms', '>=', 4);
                } else {
                    $query->where('bedrooms', $bedCount);
                }
            }
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'latest');
        match ($sortBy) {
            'price_asc', 'price_low'   => $query->orderBy('price', 'asc'),
            'price_desc', 'price_high' => $query->orderBy('price', 'desc'),
            'oldest'                   => $query->oldest(),
            'name_asc', 'title_asc'    => $query->orderBy('title', 'asc'),
            'name_desc', 'title_desc'  => $query->orderBy('title', 'desc'),
            'popular'                  => $query->orderBy('views_count', 'desc'),
            default                    => $query->latest()
        };

        // 6. Paginate Results (Default 12 per page)
        $perPage = min((int)$request->input('per_page', 12), 50);
        $paginated = $query->paginate($perPage);

        // Map favorite status for current authenticated buyer
        $favoriteIds = [];
        if ($user) {
            try {
                $favoriteIds = Favorite::where('user_id', $user->id)
                    ->whereIn('property_id', $paginated->pluck('id'))
                    ->pluck('property_id')
                    ->toArray();
            } catch (\Throwable $e) {
                $favoriteIds = [];
            }
        }

        $formattedItems = $paginated->getCollection()->map(function ($prop) use ($favoriteIds) {
            $primaryImg = $prop->primary_image_url;
            $locationStr = '';
            if ($prop->address) {
                $cityName = $prop->address->city?->name ?? (is_string($prop->address->city) ? $prop->address->city : '');
                $subCityName = $prop->address->subCity?->name ?? (is_string($prop->address->sub_city) ? $prop->address->sub_city : '');
                $street = is_string($prop->address->street ?? null) ? $prop->address->street : '';
                $district = is_string($prop->address->district ?? null) ? $prop->address->district : '';

                $parts = array_filter([$street, $district, $subCityName, $cityName]);
                $locationStr = implode(', ', $parts);
            }

            return [
                'id'            => $prop->id,
                'title'         => $prop->title,
                'slug'          => $prop->slug,
                'price'         => (float)$prop->price,
                'currency'      => $prop->currency ?? 'ETB',
                'listing_type'  => $prop->listing_type ?? 'sale',
                'property_type' => $prop->propertyType ? $prop->propertyType->name : 'Property',
                'category'      => $prop->category ? $prop->category->name : 'General',
                'bedrooms'      => (int)$prop->bedrooms,
                'bathrooms'     => (int)$prop->bathrooms,
                'area'          => (float)$prop->area,
                'area_unit'     => $prop->area_unit ?? 'sqm',
                'location'      => $locationStr ?: 'Addis Ababa',
                'image'         => $primaryImg,
                'is_featured'   => (bool)$prop->is_featured,
                'is_verified'   => (bool)$prop->is_verified,
                'is_favorite'   => in_array($prop->id, $favoriteIds),
                'status'        => $prop->status ?? 'active',
                'owner'         => [
                    'id'          => $prop->owner ? $prop->owner->id : ($prop->user_id ?? 1),
                    'name'        => $prop->owner ? $prop->owner->name : 'Abebe Kebede',
                    'email'       => $prop->owner ? $prop->owner->email : 'owner@betlink.et',
                    'phone'       => $prop->owner ? $prop->owner->phone : '+251 91 123 4567',
                    'is_verified' => $prop->owner ? (bool)($prop->owner->is_verified ?? true) : true,
                    'role'        => 'Owner / Landlord',
                ],
                'created_at'    => $prop->created_at ? $prop->created_at->toIso8601String() : null,
            ];
        });

        return $this->success([
            'data'         => $formattedItems,
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'per_page'     => $paginated->perPage(),
        ], 'Properties retrieved successfully');
    }
}
