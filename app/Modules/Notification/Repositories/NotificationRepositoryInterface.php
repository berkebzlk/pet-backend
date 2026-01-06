<?php

namespace App\Modules\Notification\Repositories;

use App\Modules\Core\Repositories\BaseRepositoryInterface;

interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserNotifications(int $userId, int $limit = 20);
    public function markAsRead(string $id);
    public function markAllAsRead(int $userId);
    public function getUnreadCount(int $userId);
}
