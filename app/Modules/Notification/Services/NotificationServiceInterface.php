<?php

namespace App\Modules\Notification\Services;

use App\Modules\Core\Services\BaseServiceInterface;

interface NotificationServiceInterface extends BaseServiceInterface
{
    public function getUserNotifications(int $userId, int $limit = 20);
    public function markAsRead(string $id);
    public function markAllAsRead(int $userId);
    public function getUnreadCount(int $userId);
}
