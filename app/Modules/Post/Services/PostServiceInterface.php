<?php

namespace App\Modules\Post\Services;

use App\Modules\Core\Services\BaseServiceInterface;
use Illuminate\Http\UploadedFile;

interface PostServiceInterface extends BaseServiceInterface
{
    public function createPost(int $petId, UploadedFile $image, ?string $description);
    public function getFeed();
    public function getPetPosts(int $petId);
}
