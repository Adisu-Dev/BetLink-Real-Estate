<?php

namespace App\Http\Controllers\Api\V1\Property;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertySearchController extends Controller
{
    use ApiResponse;

    public function __construct(private SearchService $searchService) {}

    public function search(Request $request): JsonResponse
    {
        $filters    = $request->only([
            'q', 'type', 'listing_type', 'city_id', 'sub_city_id',
            'town', 'sub_city', 'neighborhood_id', 'neighborhood',
            'min_price', 'max_price', 'bedrooms', 'bathrooms',
            'min_area', 'max_area', 'furnished', 'parking',
            'amenities', 'sort', 'user_id', 'agent_id', 'owner_id',
        ]);
        $properties = $this->searchService->search($filters, $request->per_page ?? 15);
        return $this->paginated($properties);
    }

    public function mapSearch(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => ['required', 'numeric'],
            'lng'    => ['required', 'numeric'],
            'radius' => ['nullable', 'numeric', 'min:1', 'max:100'],
        ]);

        $properties = $this->searchService->mapSearch($request->all());
        return $this->success($properties);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $request->validate(['q' => ['required', 'string', 'min:1']]);
        $q = $request->q;

        $properties = \App\Models\Property::where('status', 'active')
            ->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%")
                    ->orWhereHas('address', function ($addr) use ($q) {
                        $addr->whereHas('city', fn($c) => $c->where('name', 'like', "%{$q}%"))
                             ->orWhereHas('subCity', fn($s) => $s->where('name', 'like', "%{$q}%"));
                    });
            })
            ->select('id', 'title', 'slug', 'listing_type', 'price', 'type')
            ->with('primaryImage')
            ->limit(8)
            ->get();

        return $this->success($properties);
    }
}
