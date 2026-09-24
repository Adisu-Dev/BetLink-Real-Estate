<?php

namespace App\Http\Controllers\Api\V1\Property;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyImageController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    public function __construct(private ImageService $imageService) {}

    public function store(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'images'   => ['required', 'array', 'max:20'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $uploaded = [];
        foreach ($request->file('images') as $file) {
            $path  = $this->imageService->upload($file, 'properties');
            $image = $property->images()->create([
                'url'        => $path,
                'is_primary' => !$property->images()->where('is_primary', true)->exists(),
                'sort_order' => $property->images()->max('sort_order') + 1,
            ]);
            $uploaded[] = $image;
        }

        return $this->created($uploaded, 'Images uploaded successfully');
    }

    public function destroy(Property $property, PropertyImage $image): JsonResponse
    {
        $this->authorize('update', $property);

        if ($image->property_id !== $property->id) {
            return $this->forbidden();
        }

        $this->imageService->delete($image->getRawOriginal('url'));
        $image->delete();

        // reassign primary if needed
        if ($image->is_primary) {
            $property->images()->first()?->update(['is_primary' => true]);
        }

        return $this->noContent('Image deleted');
    }

    public function setPrimary(Property $property, PropertyImage $image): JsonResponse
{
    $this->authorize('update', $property);

    if ($image->property_id !== $property->id) {
        return $this->forbidden();
    }

    $property->images()->update(['is_primary' => false]);
    $image->update(['is_primary' => true]);

    return $this->success($image, 'Primary image set');
}

    public function reorder(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($request->order as $index => $imageId) {
            $property->images()->where('id', $imageId)->update(['sort_order' => $index]);
        }

        return $this->success(null, 'Images reordered');
    }
}
