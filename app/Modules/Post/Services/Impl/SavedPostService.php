<?php

namespace App\Modules\Post\Services\Impl;

use App\Modules\Core\Services\Impl\BaseService;
use App\Modules\Post\Repositories\SavedPostRepositoryInterface;
use App\Modules\Post\Services\SavedPostServiceInterface;

class SavedPostService extends BaseService implements SavedPostServiceInterface
{
    public function __construct(
        private SavedPostRepositoryInterface $savedPostRepository
    ) {
        parent::__construct($savedPostRepository);
    }

    public function save(int $postId, int $userId)
    {
        return $this->savedPostRepository->save($postId, $userId);
    }

    public function unsave(int $postId, int $userId)
    {
        return $this->savedPostRepository->unsave($postId, $userId);
    }
}
