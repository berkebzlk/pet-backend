<?php

namespace App\Modules\Veterinary\Services;

use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Veterinary\Models\VeterinaryProfile;
use App\Modules\Post\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Modules\Veterinary\Models\VeterinaryReview;
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

    public function index(array $requestData = [], ?Builder $query = null)
    {
        $query = $query ?? $this->model->newQuery();

        if (isset($requestData['filters'])) {
            $filters = $requestData['filters'];
            if (is_string($filters)) {
                $filters = json_decode($filters, true) ?? [];
            }

            if (isset($filters['specialty']) && !empty($filters['specialty'])) {
                $specialty = $filters['specialty'];
                $query->whereJsonContains('specialties', $specialty);
                unset($filters['specialty']);
            }

            if (isset($filters['city']) && !empty($filters['city'])) {
                $city = $filters['city'];
                $query->where('city', $city);
                unset($filters['city']);
            }

            $requestData['filters'] = $filters;
        }

        if (isset($requestData['search']) && !empty($requestData['search'])) {
            $search = $requestData['search'];
            $query->where('clinic_name', 'LIKE', '%' . $search . '%');
            unset($requestData['search']);
        }

        return parent::index($requestData, $query);
    }

    public function addOrUpdateReview(int $profileId, array $data): VeterinaryReview
    {
        return DB::transaction(function () use ($profileId, $data) {
            $profile = $this->show($profileId);

            if ($profile->user_id === auth()->id()) {
                throw new Exception('You cannot rate or review your own clinic.', 400);
            }

            $review = VeterinaryReview::updateOrCreate(
                [
                    'veterinary_profile_id' => $profileId,
                    'pet_id' => $data['pet_id'],
                ],
                [
                    'rating' => $data['rating'],
                    'comment' => $data['comment'] ?? null,
                ]
            );

            $stats = VeterinaryReview::where('veterinary_profile_id', $profileId)
                ->selectRaw('COALESCE(AVG(rating), 0) as avg_rating, COUNT(*) as count')
                ->first();

            $profile->update([
                'average_rating' => $stats->avg_rating,
                'reviews_count' => $stats->count,
            ]);

            return $review->load('pet');
        });
    }

    public function getReviews(int $profileId)
    {
        $this->show($profileId);

        return VeterinaryReview::where('veterinary_profile_id', $profileId)
            ->with('pet')
            ->latest()
            ->get();
    }

    public function getCities(): array
    {
        return $this->model->newQuery()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city', 'asc')
            ->pluck('city')
            ->toArray();
    }
}
