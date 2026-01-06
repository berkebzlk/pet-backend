<?php

namespace App\Modules\Post\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Repositories\PostRepositoryInterface;

class PostRepository extends BaseRepositoryEloquent implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function getFeed(?int $viewingPetId = null)
    {
        $query = $this->model->with('pet')->latest();

        if ($viewingPetId) {
            $query->withExists([
                'likes as is_liked' => function ($q) use ($viewingPetId) {
                    $q->where('pet_id', $viewingPetId);
                },
                'savedBy as is_saved' => function ($q) use ($viewingPetId) {
                    $q->where('pet_id', $viewingPetId);
                }
            ]);
        }

        return $query->get();
    }

    public function getByPetId(int $petId, ?int $viewingPetId = null)
    {
        $query = $this->model->where('pet_id', $petId)->with('pet')->latest();

        if ($viewingPetId) {
            $query->withExists([
                'likes as is_liked' => function ($q) use ($viewingPetId) {
                    $q->where('pet_id', $viewingPetId);
                },
                'savedBy as is_saved' => function ($q) use ($viewingPetId) {
                    $q->where('pet_id', $viewingPetId);
                }
            ]);
        }

        return $query->get();
    }

    public function getRandom(int $limit = 20, ?int $viewingPetId = null)
    {
        $query = $this->model->inRandomOrder()->limit($limit)->with('pet');

        if ($viewingPetId) {
            $query->withExists([
                'likes as is_liked' => function ($q) use ($viewingPetId) {
                    $q->where('pet_id', $viewingPetId);
                },
                'savedBy as is_saved' => function ($q) use ($viewingPetId) {
                    $q->where('pet_id', $viewingPetId);
                }
            ]);
        }

        return $query->get();
    }

    public function getByIds(array $ids, ?int $viewingPetId = null)
    {
        $query = $this->model->whereIn('id', $ids)->with('pet');

        if ($viewingPetId) {
            $query->withExists([
                'likes as is_liked' => function ($q) use ($viewingPetId) {
                    $q->where('pet_id', $viewingPetId);
                },
                'savedBy as is_saved' => function ($q) use ($viewingPetId) {
                    $q->where('pet_id', $viewingPetId);
                }
            ]);
        }

        // Maintain the order of IDs (MySQL specific, skip for SQLite testing)
        if (!empty($ids) && \DB::getDriverName() !== 'sqlite') {
            $idsString = implode(',', $ids);
            $query->orderByRaw("FIELD(id, $idsString)");
        }

        return $query->get();
    }
}
