<?php

namespace App\Modules\Pet\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Pet\Models\Pet;
use App\Modules\Pet\Repositories\PetRepositoryInterface;

use App\Modules\Core\Enums\StatusEnum;

class PetRepository extends BaseRepositoryEloquent implements PetRepositoryInterface
{
    public function __construct(Pet $model)
    {
        parent::__construct($model);
    }

    private function getQueryWithCounts(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model->withCount([
            'posts',
            'initiatedMatches' => function ($query) {
                $query->where('status', StatusEnum::ACCEPTED->value);
            },
            'receivedMatches' => function ($query) {
                $query->where('status', StatusEnum::ACCEPTED->value);
            },
            'receivedLikes'
        ]);
    }

    public function findById(int $id)
    {
        return $this->getQueryWithCounts()->find($id);
    }

    public function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->getQueryWithCounts();
    }

    public function getByUsername(string $username)
    {
        return $this->getQueryWithCounts()->where('username', $username)->firstOrFail();
    }

    public function search(string $query, int $limit = 10)
    {
        return $this->model->where('username', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->limit($limit)
            ->get();
    }
}
