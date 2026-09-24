<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\SubCity;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    use ApiResponse;

    public function cities(): JsonResponse
    {
        $cities = City::active()->withCount([
            'subCities',
            'addresses as properties_count' => fn($q) => $q->where('addressable_type', \App\Models\Property::class)
                ->whereHasMorph('addressable', [\App\Models\Property::class], fn($pq) => $pq->where('status', 'active')),
        ])->get();

        return $this->success($cities);
    }

    public function subCities(int $cityId): JsonResponse
    {
        $subCities = SubCity::where('city_id', $cityId)->active()->get();
        return $this->success($subCities);
    }

    public function neighborhoods(int $subCityId): JsonResponse
    {
        $neighborhoods = Neighborhood::where('sub_city_id', $subCityId)->active()->get();
        return $this->success($neighborhoods);
    }

    public function allLocations(): JsonResponse
    {
        $subCities = SubCity::with('city:id,name')->active()->get()->map(fn($sc) => [
            'id'       => $sc->id,
            'city_id'  => $sc->city_id,
            'cityName' => $sc->city?->name ?? 'Ethiopia',
            'name'     => $sc->name,
            'type'     => 'sub_city',
        ]);

        $neighborhoods = Neighborhood::with('subCity.city')->active()->get()->map(fn($n) => [
            'id'          => $n->id,
            'city_id'     => $n->subCity?->city_id ?? 1,
            'cityName'    => $n->subCity?->city?->name ?? 'Addis Ababa',
            'sub_city_id' => $n->sub_city_id,
            'subCityName' => $n->subCity?->name ?? '',
            'name'        => $n->name,
            'type'        => 'neighborhood',
        ]);

        return $this->success($subCities->concat($neighborhoods)->values());
    }
}
