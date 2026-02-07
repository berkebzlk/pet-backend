<?php

namespace App\Modules\Post\Services;

use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Post\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostService extends BaseEloquentService
{
    public function __construct(
        protected Post $post
    ) {
        parent::__construct($post);
    }

    private function applyViewingPet(Builder $query, ?int $viewingPetId = null): void
    {
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
    }

    public function createPost(int $petId, UploadedFile $image, ?string $description)
    {
        return DB::transaction(function () use ($petId, $image, $description) {
            $post = $this->post->create([
                'pet_id' => $petId,
                'image_url' => '', // Temporary
                'description' => $description,
            ]);

            $userId = auth()->id();

            try {
                $path = $image->store("users/{$userId}/pets/{$petId}/posts/{$post->id}", 'public');

                if (!$path) {
                    throw new \Exception("Failed to store image file.");
                }

                $post->update(['image_url' => $path]);
            } catch (\Exception $e) {
                throw new \Exception("Image upload failed: " . $e->getMessage());
            }

            return $post->refresh();
        });
    }

    public function getFeed(?int $viewingPetId = null)
    {
        $query = $this->post->with('pet')->latest();
        $this->applyViewingPet($query, $viewingPetId);
        return $query->get();
    }

    public function getPetPosts(int $petId, ?int $viewingPetId = null)
    {
        $query = $this->post->where('pet_id', $petId)->with('pet')->latest();
        $this->applyViewingPet($query, $viewingPetId);
        return $query->get();
    }

    public function getRandomPosts(int $limit = 20, ?int $viewingPetId = null)
    {
        $query = $this->post->inRandomOrder()->limit($limit)->with('pet');
        $this->applyViewingPet($query, $viewingPetId);
        return $query->get();
    }

    public function getBatch(array $ids, ?int $viewingPetId = null)
    {
        $query = $this->post->whereIn('id', $ids)->with('pet');
        $this->applyViewingPet($query, $viewingPetId);

        // Maintain the order of IDs (MySQL specific, skip for SQLite testing)
        if (!empty($ids) && DB::getDriverName() !== 'sqlite') {
            $idsString = implode(',', $ids);
            $query->orderByRaw("FIELD(id, $idsString)");
        }

        return $query->get();
    }
}
