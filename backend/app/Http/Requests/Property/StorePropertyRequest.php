<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'property_type_id'  => ['required', 'exists:property_types,id'],
            'category_id'       => ['nullable', 'exists:categories,id'],
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string'],
            'listing_type'      => ['required', 'in:sale,rent,short_rent'],
            'price'             => ['required', 'numeric', 'min:0'],
            'price_type'        => ['nullable', 'in:total,per_month,per_night,per_sqm'],
            'currency'          => ['nullable', 'string', 'max:10'],
            'negotiable'        => ['boolean'],
            'bedrooms'          => ['nullable', 'integer', 'min:0'],
            'bathrooms'         => ['nullable', 'integer', 'min:0'],
            'area'              => ['nullable', 'numeric', 'min:0'],
            'area_unit'         => ['nullable', 'in:sqm,sqft,hectare'],
            'floor_number'      => ['nullable', 'integer'],
            'total_floors'      => ['nullable', 'integer'],
            'year_built'        => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'furnished'         => ['nullable', 'in:furnished,semi_furnished,unfurnished'],
            'parking_spaces'    => ['nullable', 'integer', 'min:0'],
            'meta_title'        => ['nullable', 'string', 'max:255'],
            'meta_description'  => ['nullable', 'string'],
            // Address fields
            'city_id'           => ['required', 'exists:cities,id'],
            'sub_city_id'       => ['nullable', 'exists:sub_cities,id'],
            'neighborhood_id'   => ['nullable', 'exists:neighborhoods,id'],
            'street'            => ['nullable', 'string'],
            'kebele'            => ['nullable', 'string'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
            // Amenities & features
            'amenity_ids'       => ['nullable', 'array'],
            'amenity_ids.*'     => ['exists:amenities,id'],
            'features'          => ['nullable', 'array'],
            'features.*.feature'=> ['required', 'string'],
            'features.*.value'  => ['nullable', 'string'],
            // Images
            'images'            => ['nullable', 'array', 'max:20'],
            'images.*'          => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
