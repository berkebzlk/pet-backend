<?php

namespace App\Modules\Post\Services;

use App\Modules\Core\Services\BaseServiceInterface;
use Illuminate\Http\UploadedFile;

interface PostServiceInterface extends BaseServiceInterface
{
    public function createPost(int $petId, UploadedFile $image, ?string $description);
    public function getFeed(?int $viewingPetId = null);
    public function getPetPosts(int $petId, ?int $viewingPetId = null);
    public function getRandomPosts(int $limit = 20, ?int $viewingPetId = null);
    public function getBatch(array $ids, ?int $viewingPetId = null);
}
