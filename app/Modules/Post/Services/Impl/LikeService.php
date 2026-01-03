<?php

namespace App\Modules\Post\Services\Impl;

use App\Modules\Core\Services\Impl\BaseService;
use App\Modules\Post\Repositories\LikeRepositoryInterface;
use App\Modules\Post\Services\LikeServiceInterface;

class LikeService extends BaseService implements LikeServiceInterface
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository
    ) {
        parent::__construct($likeRepository);
    }

    public function like(int $postId, int $petId)
    {
        auth()->user()->pets()->findOrFail($petId);
        return $this->likeRepository->like($postId, $petId);
    }

    public function unlike(int $postId, int $petId)
    {
        auth()->user()->pets()->findOrFail($petId);
        return $this->likeRepository->unlike($postId, $petId);
    }
}
