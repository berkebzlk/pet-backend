<?php

namespace App\Modules\Post\Repositories;

use App\Modules\Core\Repositories\BaseRepositoryInterface;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function getFeed(?int $viewingPetId = null);
    public function getByPetId(int $petId, ?int $viewingPetId = null);
}
