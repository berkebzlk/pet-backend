<?php

namespace App\Modules\Notification\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Repositories\NotificationRepositoryInterface;

class NotificationRepository extends BaseRepositoryEloquent implements NotificationRepositoryInterface
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    public function getUserNotifications(int $userId, int $limit = 20)
    {
        return $this->model->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\Modules\User\Models\User')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function markAsRead(string $id)
    {
        $notification = $this->model->findOrFail($id);
        $notification->update(['read_at' => now()]);
        return $notification;
    }

    public function markAllAsRead(int $userId)
    {
        return $this->model->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\Modules\User\Models\User')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function getUnreadCount(int $userId)
    {
        return $this->model->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\Modules\User\Models\User')
            ->whereNull('read_at')
            ->count();
    }
}
