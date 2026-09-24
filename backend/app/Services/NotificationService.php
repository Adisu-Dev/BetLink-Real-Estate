<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NotificationService
{
    public function getForUser(?User $user, int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if (!$user) {
            return new LengthAwarePaginator([], 0, $perPage);
        }

        try {
            return $user->notifications()->paginate($perPage);
        } catch (\Throwable $e) {
            return new LengthAwarePaginator([], 0, $perPage);
        }
    }

    public function markAsRead(?User $user, string $notificationId): void
    {
        if (!$user) return;
        try {
            $user->notifications()->where('id', $notificationId)->first()?->markAsRead();
        } catch (\Throwable $e) {}
    }

    public function markAllAsRead(?User $user): void
    {
        if (!$user) return;
        try {
            $user->unreadNotifications->markAsRead();
        } catch (\Throwable $e) {}
    }

    public function getUnreadCount(?User $user): int
    {
        if (!$user) return 0;
        try {
            return $user->unreadNotifications()->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function deleteOldNotifications(?User $user): void
    {
        if (!$user) return;
        try {
            $user->notifications()
                ->where('created_at', '<', now()->subDays(30))
                ->delete();
        } catch (\Throwable $e) {}
    }
}
