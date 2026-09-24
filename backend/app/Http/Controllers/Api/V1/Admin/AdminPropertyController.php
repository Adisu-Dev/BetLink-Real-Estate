<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPropertyController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $properties = Property::with(['owner', 'propertyType', 'address.city', 'primaryImage'])
            ->when($request->q, function ($query, $v) {
                $trimmed = trim($v);
                $query->where(function ($sub) use ($trimmed) {
                    if (strlen($trimmed) <= 2) {
                        // For short queries/letters, match word starts or prefix for crystal-clear matching
                        $sub->where('title', 'like', "{$trimmed}%")
                            ->orWhere('title', 'like', "% {$trimmed}%")
                            ->orWhereHas('address.city', fn($cq) => $cq->where('name', 'like', "{$trimmed}%")->orWhere('name', 'like', "% {$trimmed}%"))
                            ->orWhereHas('owner', fn($oq) => $oq->where('name', 'like', "{$trimmed}%")->orWhere('name', 'like', "% {$trimmed}%"));
                    } else {
                        $sub->where('title', 'like', "%{$trimmed}%")
                            ->orWhereHas('address.city', fn($cq) => $cq->where('name', 'like', "%{$trimmed}%"))
                            ->orWhereHas('owner', fn($oq) => $oq->where('name', 'like', "%{$trimmed}%"));
                    }
                });
            })
            ->when($request->owner_id, fn($q, $v) => $q->where('user_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->listing_type, function ($q, $v) {
                if (in_array(strtolower($v), ['short_stay', 'short_rent', 'short-stay'])) {
                    $q->whereIn('listing_type', ['short_rent', 'short_stay']);
                } else {
                    $q->where('listing_type', $v);
                }
            })
            ->withTrashed()
            ->when($request->q, function ($query, $v) {
                $trimmed = trim($v);
                $query->orderByRaw("CASE 
                    WHEN title LIKE ? THEN 1 
                    WHEN title LIKE ? THEN 2 
                    ELSE 3 
                END", ["{$trimmed}%", "% {$trimmed}%"]);
            })
            ->latest()
            ->paginate($request->per_page ?? 20);

        return $this->paginated($properties);
    }

    public function show(int $id): JsonResponse
    {
        $property = Property::withTrashed()->with([
            'owner', 'propertyType', 'category', 'address',
            'images', 'amenities', 'features',
        ])->findOrFail($id);

        return $this->success($property);
    }

    public function approve(Request $request, Property $property): JsonResponse
    {
        if ($property->status !== 'pending') {
            return $this->error('Only pending properties can be approved');
        }

        $property->update([
            'status'       => 'active',
            'is_verified'  => true,
            'verified_at'  => now(),
            'verified_by'  => $request->user()->id,
            'published_at' => now(),
        ]);

        $property->owner->notify(new \App\Notifications\PropertyApproved($property));

        return $this->success($property, 'Property approved and published');
    }

    public function reject(Request $request, Property $property): JsonResponse
    {
        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        if ($property->status !== 'pending') {
            return $this->error('Only pending properties can be rejected');
        }

        $property->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        $property->owner->notify(new \App\Notifications\PropertyRejected($property));

        return $this->success($property, 'Property rejected');
    }

    public function feature(Request $request, Property $property): JsonResponse
    {
        $validated = $request->validate([
            'is_featured'       => ['nullable', 'boolean'],
            'featured_priority' => ['nullable', 'integer', 'min:1', 'max:10'],
            'featured_from'     => ['nullable', 'date'],
            'featured_until'    => ['nullable', 'date'],
            'featured_reason'   => ['nullable', 'string', 'in:promoted,verified_badge,high_rating,admin_spotlight'],
        ]);

        $newStatus = isset($validated['is_featured']) ? (bool) $validated['is_featured'] : !$property->is_featured;

        $property->update([
            'is_featured'       => $newStatus,
            'featured_priority' => $newStatus ? ($validated['featured_priority'] ?? $property->featured_priority ?? 1) : null,
            'featured_from'     => $newStatus ? ($validated['featured_from'] ?? now()) : null,
            'featured_until'    => $newStatus ? ($validated['featured_until'] ?? now()->addDays(30)) : null,
            'featured_reason'   => $newStatus ? ($validated['featured_reason'] ?? 'admin_spotlight') : null,
        ]);

        $msg = $property->is_featured ? 'Property marked as featured with active criteria' : 'Property unfeatured';
        return $this->success($property->fresh(), $msg);
    }

    public function updateStatus(Request $request, Property $property): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,draft,pending,rejected,sold,rented']
        ]);

        $property->update([
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'active' ? ($property->published_at ?? now()) : $property->published_at,
        ]);

        return $this->success($property, "Property status updated to {$validated['status']}");
    }

    public function destroy(Property $property): JsonResponse
    {
        $property->forceDelete();
        return $this->noContent('Property permanently deleted');
    }
}
