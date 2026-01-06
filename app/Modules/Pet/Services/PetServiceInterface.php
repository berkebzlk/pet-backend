<?php

namespace App\Modules\Pet\Services;

use App\Modules\Core\Services\BaseServiceInterface;

interface PetServiceInterface extends BaseServiceInterface
{
    public function getByUsername(string $username);
    public function search(string $query, int $limit = 10);
}
