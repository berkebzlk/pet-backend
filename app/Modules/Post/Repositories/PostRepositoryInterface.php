<?php

namespace App\Modules\Post\Repositories;

use App\Modules\Core\Repositories\BaseRepositoryInterface;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function getFeed(?int $viewingPetId = null);
    public function getByPetId(int $petId, ?int $viewingPetId = null);
    public function getRandom(int $limit = 20, ?int $viewingPetId = null);
    public function getByIds(array $ids, ?int $viewingPetId = null);
}
