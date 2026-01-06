<?php

namespace App\Modules\Pet\Repositories;

use App\Modules\Core\Repositories\BaseRepositoryInterface;

interface PetRepositoryInterface extends BaseRepositoryInterface
{
    public function getByUsername(string $username);
    public function search(string $query, int $limit = 10);
}
