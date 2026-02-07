<?php

namespace App\Modules\Notification\Services;

use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Notification\Models\Notification;

class NotificationService extends BaseEloquentService
{
    public function __construct(
        protected Notification $notification
    ) {
        parent::__construct($notification);
    }

    public function getUserNotifications(int $userId, int $limit = 20)
    {
        return $this->notification->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\Modules\User\Models\User')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function markAsRead(string $id)
    {
        $notification = $this->notification->findOrFail($id);
        $notification->update(['read_at' => now()]);
        return $notification;
    }

    public function markAllAsRead(int $userId)
    {
        return $this->notification->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\Modules\User\Models\User')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function getUnreadCount(int $userId)
    {
        return $this->notification->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\Modules\User\Models\User')
            ->whereNull('read_at')
            ->count();
    }
}
