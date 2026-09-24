<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->reviewer_id || $user->isAdmin();
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->reviewer_id || $user->isAdmin();
    }
}
