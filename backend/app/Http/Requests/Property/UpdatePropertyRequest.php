<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'property_type_id'  => ['sometimes', 'exists:property_types,id'],
            'category_id'       => ['nullable', 'exists:categories,id'],
            'title'             => ['sometimes', 'string', 'max:255'],
            'description'       => ['sometimes', 'string'],
            'listing_type'      => ['sometimes', 'in:sale,rent,short_rent'],
            'price'             => ['sometimes', 'numeric', 'min:0'],
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
            'city_id'           => ['sometimes', 'exists:cities,id'],
            'sub_city_id'       => ['nullable', 'exists:sub_cities,id'],
            'neighborhood_id'   => ['nullable', 'exists:neighborhoods,id'],
            'street'            => ['nullable', 'string'],
            'kebele'            => ['nullable', 'string'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
            'amenity_ids'       => ['nullable', 'array'],
            'amenity_ids.*'     => ['exists:amenities,id'],
            'features'          => ['nullable', 'array'],
            'features.*.feature'=> ['required', 'string'],
            'features.*.value'  => ['nullable', 'string'],
        ];
    }
}
