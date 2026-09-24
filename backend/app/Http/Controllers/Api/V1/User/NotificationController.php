<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function __construct(private NotificationService $notificationService) {}

    public function index(Request $request): JsonResponse
    {
        $notifications = $this->notificationService->getForUser($request->user(), $request->per_page ?? 20);
        return $this->paginated($notifications);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $this->notificationService->markAsRead($request->user(), $id);
        return $this->success(null, 'Notification marked as read');
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $this->notificationService->markAllAsRead($request->user());
        return $this->success(null, 'All notifications marked as read');
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount($request->user());
        return $this->success(['unread_count' => $count]);
    }
}
