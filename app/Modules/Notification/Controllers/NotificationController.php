<?php

namespace App\Modules\Notification\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Notification\Payload\Resources\NotificationResource;
use App\Modules\Notification\Services\NotificationServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationServiceInterface $notificationService
    ) {
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 20);
        $notifications = $this->notificationService->getUserNotifications(Auth::id(), $limit);
        return ResponseHelper::success(NotificationResource::collection($notifications));
    }

    public function markAsRead($id)
    {
        $this->notificationService->markAsRead($id);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::id());
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'All notifications marked as read.');
    }

    public function unreadCount()
    {
        $count = $this->notificationService->getUnreadCount(Auth::id());
        return ResponseHelper::success(['count' => $count]);
    }
}
