<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Property;
use App\Models\PropertyFeature;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Support\Str;

class PropertyService
{
    public function __construct(private ImageService $imageService) {}

    public function create(array $data, User $owner): Property
    {
        $data['user_id'] = $owner->id;
        $data['slug']    = self::generateUniqueSlug($data['title'] ?? 'property-' . Str::random(6));
        $data['status']  = 'draft';

        $property = Property::create($data);

        $this->syncAddress($property, $data);
        $this->syncAmenities($property, $data['amenity_ids'] ?? []);
        $this->syncFeatures($property, $data['features'] ?? []);

        return $property->load(['address', 'amenities', 'features', 'images']);
    }

    public function update(Property $property, array $data): Property
    {
        if (isset($data['title'])) {
            $data['slug'] = self::generateUniqueSlug($data['title'], $property->id);
        }

        $property->update($data);

        if (isset($data['city_id'])) {
            $this->syncAddress($property, $data);
        }
        if (isset($data['amenity_ids'])) {
            $this->syncAmenities($property, $data['amenity_ids']);
        }
        if (isset($data['features'])) {
            $this->syncFeatures($property, $data['features']);
        }

        return $property->fresh(['address', 'amenities', 'features', 'images']);
    }

    public function publish(Property $property): Property
    {
        $property->update(['status' => 'pending']);
        return $property;
    }

    public function uploadImages(Property $property, array $files, bool $firstAsPrimary = false): void
    {
        foreach ($files as $index => $file) {
            $path = $this->imageService->upload($file, 'properties');
            PropertyImage::create([
                'property_id' => $property->id,
                'url'         => $path,
                'is_primary'  => $firstAsPrimary && $index === 0 && !$property->images()->where('is_primary', true)->exists(),
                'sort_order'  => $property->images()->max('sort_order') + $index + 1,
            ]);
        }
    }

    private function syncAddress(Property $property, array $data): void
    {
        $addressData = array_filter([
            'city_id'         => $data['city_id'] ?? null,
            'sub_city_id'     => $data['sub_city_id'] ?? null,
            'neighborhood_id' => $data['neighborhood_id'] ?? null,
            'street'          => $data['street'] ?? null,
            'kebele'          => $data['kebele'] ?? null,
            'latitude'        => $data['latitude'] ?? null,
            'longitude'       => $data['longitude'] ?? null,
        ], fn($v) => $v !== null);

        if (!empty($addressData)) {
            $property->address()->updateOrCreate(
                ['addressable_id' => $property->id, 'addressable_type' => Property::class],
                $addressData
            );
        }
    }

    private function syncAmenities(Property $property, array $ids): void
    {
        $property->amenities()->sync($ids);
    }

    private function syncFeatures(Property $property, array $features): void
    {
        $property->features()->delete();
        foreach ($features as $feature) {
            PropertyFeature::create([
                'property_id' => $property->id,
                'feature'     => $feature['feature'],
                'value'       => $feature['value'] ?? null,
            ]);
        }
    }

    public static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $base = $slug;
        $i    = 1;
        while (Property::withTrashed()->where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
