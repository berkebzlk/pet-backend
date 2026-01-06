<?php

namespace App\Modules\Notification\Services\Impl;

use App\Modules\Core\Services\Impl\BaseService;
use App\Modules\Notification\Repositories\NotificationRepositoryInterface;
use App\Modules\Notification\Services\NotificationServiceInterface;

class NotificationService extends BaseService implements NotificationServiceInterface
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository
    ) {
        parent::__construct($notificationRepository);
    }

    public function getUserNotifications(int $userId, int $limit = 20)
    {
        return $this->notificationRepository->getUserNotifications($userId, $limit);
    }

    public function markAsRead(string $id)
    {
        return $this->notificationRepository->markAsRead($id);
    }

    public function markAllAsRead(int $userId)
    {
        return $this->notificationRepository->markAllAsRead($userId);
    }

    public function getUnreadCount(int $userId)
    {
        return $this->notificationRepository->getUnreadCount($userId);
    }
}
