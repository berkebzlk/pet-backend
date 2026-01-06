<?php

namespace App\Modules\Post\Services\Impl;

use App\Modules\Core\Services\Impl\BaseService;
use App\Modules\Post\Repositories\PostRepositoryInterface;
use App\Modules\Post\Services\PostServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostService extends BaseService implements PostServiceInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {
        parent::__construct($postRepository);
    }

    public function createPost(int $petId, UploadedFile $image, ?string $description)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($petId, $image, $description) {
            // 1. Create post with temporary image path or null
            $post = $this->postRepository->create([
                'pet_id' => $petId,
                'image_url' => '', // Temporary
                'description' => $description,
            ]);

            // 2. Get User ID from Pet (assuming we have access or can fetch it)
            // Since we are in PostService, we might need to fetch the pet to get the user_id
            // But usually the controller checks ownership, so we can assume auth()->id() is the owner
            // However, to be safe and strictly follow the path structure: users > userId > pets > petId
            $userId = auth()->id();

            // 3. Store image in structured path
            try {
                $path = $image->store("users/{$userId}/pets/{$petId}/posts/{$post->id}", 'public');

                if (!$path) {
                    throw new \Exception("Failed to store image file.");
                }
            } catch (\Exception $e) {
                // Throwing exception will trigger rollback
                throw new \Exception("Image upload failed: " . $e->getMessage());
            }

            // 4. Update post with the path (not full URL, as Resource handles URL)
            // Wait, previous fix in PostResource uses Storage::url($this->image_url).
            // So here we should store the relative path.
            $this->postRepository->update($post->id, ['image_url' => $path]);

            return $post->refresh();
        });
    }

    public function getFeed(?int $viewingPetId = null)
    {
        return $this->postRepository->getFeed($viewingPetId);
    }

    public function getPetPosts(int $petId, ?int $viewingPetId = null)
    {
        return $this->postRepository->getByPetId($petId, $viewingPetId);
    }

    public function getRandomPosts(int $limit = 20, ?int $viewingPetId = null)
    {
        return $this->postRepository->getRandom($limit, $viewingPetId);
    }

    public function getBatch(array $ids, ?int $viewingPetId = null)
    {
        return $this->postRepository->getByIds($ids, $viewingPetId);
    }
}
