<?php

namespace App\Http\Controllers\Api\V1\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\Property;
use App\Models\Review;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponse;

    public function index(Request $request, int $propertyId): JsonResponse
    {
        $property = Property::findOrFail($propertyId);

        $reviews = $property->reviews()
            ->approved()
            ->with(['reviewer:id,name,avatar', 'response'])
            ->latest()
            ->paginate($request->per_page ?? 10);

        $avgRating = $property->reviews()->approved()->avg('rating');

        return $this->paginated($reviews);
    }

    public function store(StoreReviewRequest $request, int $propertyId): JsonResponse
    {
        $property = Property::where('status', 'active')->findOrFail($propertyId);

        // One review per user per property
        $exists = Review::where('reviewable_type', Property::class)
            ->where('reviewable_id', $property->id)
            ->where('reviewer_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return $this->error('You have already reviewed this property', 409);
        }

        $review = Review::create([
            'reviewable_type' => Property::class,
            'reviewable_id'   => $property->id,
            'reviewer_id'     => $request->user()->id,
            'rating'          => $request->rating,
            'title'           => $request->title,
            'body'            => $request->body,
            'pros'            => $request->pros,
            'cons'            => $request->cons,
            'status'          => 'approved', // auto-approve or 'pending' based on config
        ]);

        return $this->created($review->load('reviewer'), 'Review submitted');
    }

    public function update(StoreReviewRequest $request, Review $review): JsonResponse
    {
        $this->authorize('update', $review);
        $review->update($request->validated());
        return $this->success($review, 'Review updated');
    }

    public function destroy(Review $review): JsonResponse
    {
        $this->authorize('delete', $review);
        $review->delete();
        return $this->noContent('Review deleted');
    }

    public function respond(Request $request, Review $review): JsonResponse
    {
        $request->validate(['body' => ['required', 'string', 'max:1000']]);

        $property = Property::find($review->reviewable_id);
        if (!$property || $property->user_id !== $request->user()->id) {
            return $this->forbidden('Only the property owner can respond to reviews');
        }

        $response = $review->response()->updateOrCreate(
            ['review_id' => $review->id],
            ['user_id' => $request->user()->id, 'body' => $request->body]
        );

        return $this->success($response, 'Response added');
    }
}
