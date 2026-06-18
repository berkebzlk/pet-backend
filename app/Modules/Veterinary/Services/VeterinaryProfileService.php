<?php

namespace App\Modules\Veterinary\Services;

use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Veterinary\Models\VeterinaryProfile;
use App\Modules\Post\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class VeterinaryProfileService extends BaseEloquentService
{
    public function __construct(
        protected VeterinaryProfile $veterinaryProfile
    ) {
        parent::__construct($veterinaryProfile);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $userId = auth()->id();

            // Check if user already has a veterinary profile
            if ($this->model->newQuery()->where('user_id', $userId)->exists()) {
                throw new Exception('A user can only have one veterinary profile.', 400);
            }

            $data['user_id'] = $userId;

            // Extract files
            $profilePhoto = null;
            if (isset($data['profile_photo']) && $data['profile_photo'] instanceof UploadedFile) {
                $profilePhoto = $data['profile_photo'];
                unset($data['profile_photo']);
            }

            $coverPhoto = null;
            if (isset($data['cover_photo']) && $data['cover_photo'] instanceof UploadedFile) {
                $coverPhoto = $data['cover_photo'];
                unset($data['cover_photo']);
            }

            $profile = $this->model->create($data);

            // Save photos if present
            if ($profilePhoto) {
                $profilePhotoPath = $profilePhoto->store("users/{$userId}/veterinary/profilePhoto", 'public');
                $profile->update(['profile_photo' => $profilePhotoPath]);
            }

            if ($coverPhoto) {
                $coverPhotoPath = $coverPhoto->store("users/{$userId}/veterinary/coverPhoto", 'public');
                $profile->update(['cover_photo' => $coverPhotoPath]);
            }

            return $profile->fresh();
        });
    }

    public function getPosts(int $profileId)
    {
        $profile = $this->show($profileId);

        // Fetch posts belonging directly to this veterinary profile
        return Post::where('veterinary_profile_id', $profileId)
            ->with(['pet', 'veterinaryProfile'])
            ->latest()
            ->get();
    }
}
