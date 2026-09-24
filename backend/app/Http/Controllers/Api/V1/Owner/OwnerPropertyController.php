<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyType;
use App\Models\SubCity;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OwnerPropertyController extends Controller
{
    use ApiResponse;

    /**
     * Get paginated properties owned by authenticated owner
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Property::where('user_id', $user->id)
            ->with([
                'primaryImage',
                'images',
                'address.city',
                'address.subCity',
                'propertyType',
                'category',
                'favorites.user'
            ])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhereHas('address', function ($aq) use ($s) {
                      $aq->where('street', 'like', "%{$s}%")
                         ->orWhereHas('subCity', fn($sq) => $sq->where('name', 'like', "%{$s}%"));
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('listing_type') && $request->listing_type !== 'all') {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->filled('property_type') && $request->property_type !== 'all') {
            $type = $request->property_type;
            $query->whereHas('propertyType', function ($tq) use ($type) {
                $tq->where('name', $type);
            });
        }

        $perPage = $request->per_page ?? 12;
        $properties = $query->paginate($perPage);

        $transformed = $properties->through(function ($prop) {
            $favs = $prop->favorites
                ->where('user_id', '!=', $prop->user_id);

            $favoritedBy = $favs->map(fn($f) => [
                'id'        => $f->id,
                'user_id'   => $f->user_id,
                'user_name' => $f->user?->name ?? 'Interested Buyer',
                'email'     => $f->user?->email,
                'phone'     => $f->user?->phone ?? '—',
                'saved_at'  => $f->created_at ? \Illuminate\Support\Carbon::parse($f->created_at)->format('Y-m-d H:i') : '—',
            ])->values();

            return [
                'id'            => $prop->id,
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
                'status'        => $prop->status,
                'is_verified'   => (bool) $prop->is_verified,
                'is_featured'   => (bool) $prop->is_featured,
                'views_count'   => (int) ($prop->views_count ?? 0),
                'favorites_count' => $favoritedBy->count(),
                'favorited_by'  => $favoritedBy,
                'image'         => $prop->primaryImage?->url ?? $prop->images?->first()?->url ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
                'location'      => ($prop->address?->subCity?->name ? $prop->address->subCity->name . ', ' : '') . ($prop->address?->city?->name ?? 'Addis Ababa'),
                'sub_city'      => $prop->address?->subCity?->name ?? 'Bole',
                'street'        => $prop->address?->street ?? '',
                'property_type' => $prop->propertyType?->name ?? 'Residential',
                'created_at'    => $prop->created_at?->toIso8601String(),
            ];
        });

        return $this->paginated($transformed);
    }

    /**
     * Store a new property listing with address and images
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['required', 'string', 'max:5000'],
            'listing_type'     => ['required', 'string', 'in:sale,rent,short_rent'],
            'price'            => ['required', 'numeric', 'min:0'],
            'price_type'       => ['nullable', 'string', 'in:total,per_month,per_night,per_sqm'],
            'property_type_id' => ['nullable', 'integer'],
            'bedrooms'         => ['nullable', 'integer', 'min:0'],
            'bathrooms'        => ['nullable', 'integer', 'min:0'],
            'area'             => ['nullable', 'numeric', 'min:0'],
            'status'           => ['nullable', 'string', 'in:draft,active,pending'],
            'city_name'        => ['nullable', 'string'],
            'sub_city_name'    => ['nullable', 'string'],
            'images'           => ['nullable', 'array'],
            'images.*'         => ['nullable', 'string'],
        ]);

        // Check for duplicate property listing
        $normalizedTitle = trim(strtolower($validated['title']));
        $duplicateExists = Property::where(function ($q) use ($normalizedTitle, $user, $validated) {
            $q->whereRaw('LOWER(title) = ?', [$normalizedTitle])
              ->orWhere(function ($sub) use ($user, $validated) {
                  $sub->where('user_id', $user->id)
                      ->where('price', $validated['price'])
                      ->where('listing_type', $validated['listing_type']);
              });
        })->exists();

        if ($duplicateExists) {
            return $this->error('A property with this title or details already exists. Duplicate property postings are not allowed.', 422);
        }

        $defaultType = PropertyType::firstOrCreate(['slug' => 'apartment'], ['name' => 'Apartment']);

        $property = Property::create([
            'user_id'          => $user->id,
            'property_type_id' => $validated['property_type_id'] ?? $defaultType->id,
            'title'            => $validated['title'],
            'slug'             => Str::slug($validated['title']) . '-' . Str::random(6),
            'description'      => $validated['description'],
            'listing_type'     => $validated['listing_type'],
            'price'            => $validated['price'],
            'price_type'       => $validated['price_type'] ?? ($validated['listing_type'] === 'rent' ? 'per_month' : 'total'),
            'bedrooms'         => $validated['bedrooms'] ?? 2,
            'bathrooms'        => $validated['bathrooms'] ?? 1,
            'area'             => $validated['area'] ?? 120,
            'status'           => ($validated['status'] ?? 'pending') === 'draft' ? 'draft' : 'pending',
            'is_verified'      => false,
            'published_at'     => now(),
        ]);

        // Address resolution: Find or create the selected city and subcity dynamically
        $rawCityName = trim($validated['city_name'] ?? 'Addis Ababa');
        $citySlug = Str::slug($rawCityName);
        $city = City::where('slug', $citySlug)
            ->orWhere('name', 'like', "%{$rawCityName}%")
            ->first();

        if (!$city) {
            $city = City::create([
                'name' => $rawCityName,
                'slug' => $citySlug,
                'region' => $rawCityName . ' Region',
                'is_active' => true,
            ]);
        }

        $rawSubCityName = trim($validated['sub_city_name'] ?? 'Bole');
        $subCitySlug = Str::slug($rawSubCityName);
        $subCity = SubCity::where('city_id', $city->id)
            ->where(fn($q) => $q->where('slug', $subCitySlug)->orWhere('name', 'like', "%{$rawSubCityName}%"))
            ->first();

        if (!$subCity) {
            $subCity = SubCity::create([
                'city_id' => $city->id,
                'name' => $rawSubCityName,
                'slug' => $subCitySlug,
                'is_active' => true,
            ]);
        }

        $streetName = trim($validated['street'] ?? ($rawSubCityName . ' Main Road'));

        Address::create([
            'addressable_type' => Property::class,
            'addressable_id'   => $property->id,
            'city_id'          => $city->id,
            'sub_city_id'      => $subCity->id,
            'street'           => $streetName,
            'full_address'     => $streetName . ', ' . $subCity->name . ', ' . $city->name . ', Ethiopia',
        ]);

        // Images storing
        $images = $request->images ?? [];
        if (empty($images)) {
            $images = ['https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&q=80'];
        }

        foreach ($images as $index => $imageUrl) {
            if ($imageUrl) {
                PropertyImage::create([
                    'property_id' => $property->id,
                    'url'         => $imageUrl,
                    'is_primary'  => $index === 0,
                    'sort_order'  => $index + 1,
                ]);
            }
        }

        return $this->created($property->load(['primaryImage', 'images', 'address.subCity', 'address.city']), 'Property listing created successfully');
    }

    /**
     * Update an existing property
     */
    public function update(Request $request, Property $property): JsonResponse
    {
        if ($property->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return $this->error('Unauthorized to modify this listing', 403);
        }

        $validated = $request->validate([
            'title'         => ['nullable', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:5000'],
            'listing_type'  => ['nullable', 'string', 'in:sale,rent,short_rent'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'bedrooms'      => ['nullable', 'integer', 'min:0'],
            'bathrooms'     => ['nullable', 'integer', 'min:0'],
            'area'          => ['nullable', 'numeric', 'min:0'],
            'status'        => ['nullable', 'string', 'in:draft,active,pending,sold,rented,inactive'],
            'city_name'     => ['nullable', 'string', 'max:255'],
            'sub_city_name' => ['nullable', 'string', 'max:255'],
            'street'        => ['nullable', 'string', 'max:255'],
        ]);

        $property->update(array_filter($validated, fn($val) => $val !== null));

        if ($request->filled('city_name') || $request->filled('sub_city_name') || $request->filled('street')) {
            $address = $property->address ?? new Address(['addressable_type' => Property::class, 'addressable_id' => $property->id]);

            if ($request->filled('city_name')) {
                $rawCity = trim($request->city_name);
                $city = City::firstOrCreate(
                    ['slug' => Str::slug($rawCity)],
                    ['name' => $rawCity, 'region' => $rawCity . ' Region', 'is_active' => true]
                );
                $address->city_id = $city->id;
            }

            if ($request->filled('sub_city_name')) {
                $rawSubCity = trim($request->sub_city_name);
                $cityId = $address->city_id ?? 1;
                $subCity = SubCity::firstOrCreate(
                    ['city_id' => $cityId, 'slug' => Str::slug($rawSubCity)],
                    ['name' => $rawSubCity, 'is_active' => true]
                );
                $address->sub_city_id = $subCity->id;
            }

            if ($request->filled('street')) {
                $address->street = trim($request->street);
            }

            $cityName = $address->city?->name ?? 'Addis Ababa';
            $subCityName = $address->subCity?->name ?? '';
            $streetName = $address->street ?? '';
            $address->full_address = trim("{$streetName}, {$subCityName}, {$cityName}, Ethiopia", ', ');
            $address->save();
        }

        return $this->success($property->load(['primaryImage', 'images', 'address.city', 'address.subCity']), 'Property updated successfully');
    }

    /**
     * Toggle status between active and draft
     */
    public function toggleStatus(Request $request, Property $property): JsonResponse
    {
        if ($property->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return $this->error('Unauthorized', 403);
        }

        $property->status = $property->status === 'active' ? 'draft' : 'active';
        $property->save();

        return $this->success($property, "Property status updated to {$property->status}");
    }

    /**
     * Delete a property listing
     */
    public function destroy(Request $request, Property $property): JsonResponse
    {
        if ($property->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return $this->error('Unauthorized', 403);
        }

        $property->delete();

        return $this->success(null, 'Property listing deleted successfully');
    }

    /**
     * Upload an image file for property listing (laptop / mobile)
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $file = $request->file('image');
        $path = $file->store('properties', 'public');
        $url = asset('storage/' . $path);

        return $this->success([
            'url'  => $url,
            'path' => $path,
        ], 'Image uploaded successfully');
    }
}
