<?php

namespace App\Http\Controllers\Api\V1\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Category;
use App\Models\Amenity;
use App\Services\PropertyService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    public function __construct(private PropertyService $propertyService) {}

    public function index(Request $request): JsonResponse
    {
        $properties = Property::with(['propertyType', 'category', 'address.city', 'primaryImage', 'owner'])
            ->where('status', 'active')
            ->when($request->listing_type, fn($q, $v) => $q->where('listing_type', $v))
            ->when($request->featured, fn($q) => $q->where('is_featured', true))
            ->when($request->user_id, fn($q, $v) => $q->where('user_id', $v))
            ->when($request->agent_id, fn($q, $v) => $q->where('user_id', $v))
            ->when($request->owner_id, fn($q, $v) => $q->where('user_id', $v))
            ->latest('published_at')
            ->paginate($request->per_page ?? 15);

        return $this->paginated($properties);
    }

    public function show(string $slug, Request $request): JsonResponse
    {
        $query = Property::with([
            'propertyType', 'category', 'address.city', 'address.subCity', 'address.neighborhood',
            'images', 'videos', 'amenities', 'features',
            'owner.profile', 'reviews' => fn($q) => $q->approved()->with('reviewer'),
        ]);

        if (is_numeric($slug)) {
            $property = $query->findOrFail((int)$slug);
        } else {
            $property = $query->where('slug', $slug)->firstOrFail();
        }

        $user = $request->user('sanctum');
        $isOwnerOrAdmin = $user && ($user->id === $property->user_id || $user->isAdmin());

        if (!$isOwnerOrAdmin && $property->status !== 'active') {
            abort(404, 'Property not found');
        }

        // track unique view per person/browser on public active listings
        if ($property->status === 'active' && !$isOwnerOrAdmin) {
            $ip = $request->ip();
            $userAgent = substr((string)$request->userAgent(), 0, 255);
            $userId = $user?->id;

            // Check if this user or IP has already viewed this property
            $alreadyViewed = \App\Models\PropertyView::where('property_id', $property->id)
                ->where(function ($q) use ($userId, $ip) {
                    if ($userId) {
                        $q->where('user_id', $userId);
                    } else {
                        $q->where('ip_address', $ip);
                    }
                })
                ->exists();

            if (!$alreadyViewed) {
                \App\Models\PropertyView::create([
                    'property_id' => $property->id,
                    'user_id'     => $userId,
                    'ip_address'  => $ip,
                    'user_agent'  => $userAgent,
                    'viewed_at'   => now(),
                ]);
                $property->increment('views_count');
                $property->views_count = (int)$property->views_count + 1;
            }
        }

        return $this->success($property);
    }

    public function store(StorePropertyRequest $request): JsonResponse
    {
        $property = $this->propertyService->create($request->validated(), $request->user());

        if ($request->hasFile('images')) {
            $this->propertyService->uploadImages($property, $request->file('images'), true);
        }

        return $this->created($property, 'Property created successfully');
    }

    public function update(UpdatePropertyRequest $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);
        $property = $this->propertyService->update($property, $request->validated());
        return $this->success($property, 'Property updated successfully');
    }

    public function destroy(Property $property): JsonResponse
    {
        $this->authorize('delete', $property);
        $property->delete();
        return $this->noContent('Property deleted successfully');
    }

    public function publish(Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        if (!in_array($property->status, ['draft', 'rejected'])) {
            return $this->error('Only draft or rejected properties can be submitted for approval');
        }

        $property = $this->propertyService->publish($property);
        return $this->success($property, 'Property submitted for approval');
    }

    public function myProperties(Request $request): JsonResponse
    {
        $properties = Property::with(['propertyType', 'primaryImage', 'address.city'])
            ->where('user_id', $request->user()->id)
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return $this->paginated($properties);
    }

    public function featured(): JsonResponse
    {
        $query = Property::with([
            'propertyType', 'category', 'address.city', 'address.subCity',
            'primaryImage', 'images', 'owner.profile'
        ])->where('status', 'active');

        $properties = (clone $query)->where('is_featured', true)
            ->latest('published_at')
            ->take(12)
            ->get();

        if ($properties->count() < 4) {
            $existingIds = $properties->pluck('id')->toArray();
            $fallback = (clone $query)
                ->whereNotIn('id', $existingIds)
                ->latest()
                ->take(12 - $properties->count())
                ->get();
            $properties = $properties->concat($fallback);
        }

        return $this->success($properties);
    }

    public function types(): JsonResponse
    {
        $types = PropertyType::active()->withCount('properties')->with(['categories' => fn($q) => $q->active()])->get();
        return $this->success($types);
    }

    public function categories(Request $request): JsonResponse
    {
        $categories = Category::active()
            ->when($request->property_type_id, fn($q, $v) => $q->where('property_type_id', $v))
            ->get();
        return $this->success($categories);
    }

    public function amenities(): JsonResponse
    {
        $amenities = Amenity::active()->get();
        return $this->success($amenities);
    }
}
