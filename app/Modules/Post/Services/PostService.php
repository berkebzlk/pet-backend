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

    public function createPost(?int $petId, UploadedFile $image, ?string $description, ?int $veterinaryProfileId = null)
    {
        return DB::transaction(function () use ($petId, $image, $description, $veterinaryProfileId) {
            $post = $this->post->create([
                'pet_id' => $petId,
                'veterinary_profile_id' => $veterinaryProfileId,
                'image_url' => '', // Temporary
                'description' => $description,
            ]);

            $userId = auth()->id();

            try {
                $subFolder = $petId ? "pets/{$petId}" : "veterinary/{$veterinaryProfileId}";
                $path = $image->store("users/{$userId}/{$subFolder}/posts/{$post->id}", 'public');

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

    public function getFeed(?int $viewingPetId = null, int $page = 1, int $limit = 10)
    {
        if (!$viewingPetId) {
            $query = $this->post->with(['pet', 'veterinaryProfile'])->latest()->offset(($page - 1) * $limit)->limit($limit);
            $this->applyViewingPet($query, $viewingPetId);
            return $query->get();
        }

        // 1. Get connection pet IDs (matches and breeding connections)
        $matchedIds = DB::table('matches')
            ->where(function($q) use ($viewingPetId) {
                $q->where('initiator_pet_id', $viewingPetId)
                  ->orWhere('target_pet_id', $viewingPetId);
            })
            ->where('status', 5) // StatusEnum::ACCEPTED
            ->get()
            ->map(fn($row) => $row->initiator_pet_id == $viewingPetId ? $row->target_pet_id : $row->initiator_pet_id)
            ->toArray();

        $breedingIds = DB::table('breeding_connections')
            ->where(function($q) use ($viewingPetId) {
                $q->where('initiator_pet_id', $viewingPetId)
                  ->orWhere('target_pet_id', $viewingPetId);
            })
            ->where('status', 'accepted')
            ->get()
            ->map(fn($row) => $row->initiator_pet_id == $viewingPetId ? $row->target_pet_id : $row->initiator_pet_id)
            ->toArray();

        $connectedPetIds = array_values(array_unique(array_merge($matchedIds, $breedingIds)));

        // 2. Determine target limits and offsets (70% connections, 30% strangers)
        $connLimit = (int)ceil($limit * 0.7);
        $strangerLimit = $limit - $connLimit;
        
        $connOffset = ($page - 1) * $connLimit;
        $strangerOffset = ($page - 1) * ($limit - $connLimit);

        // 3. Fetch connection posts
        $connPosts = collect();
        if (!empty($connectedPetIds)) {
            $connQuery = $this->post->whereIn('pet_id', $connectedPetIds)
                ->with(['pet', 'veterinaryProfile'])
                ->latest();
            $this->applyViewingPet($connQuery, $viewingPetId);
            $connPosts = $connQuery->offset($connOffset)->limit($connLimit)->get();
        }

        // 4. Backfill logic (if connections have fewer posts, fetch more from strangers)
        $connCount = $connPosts->count();
        if ($connCount < $connLimit) {
            $strangerLimit += ($connLimit - $connCount);
        }

        // 5. Fetch stranger posts
        $strangerQuery = $this->post->where(function($q) use ($viewingPetId, $connectedPetIds) {
            $q->whereNull('pet_id') // Veterinary/clinic posts
              ->orWhere(function($sub) use ($viewingPetId, $connectedPetIds) {
                  $sub->whereNotIn('pet_id', array_merge($connectedPetIds, [$viewingPetId]));
              });
        })
        ->with(['pet', 'veterinaryProfile'])
        ->latest();

        $this->applyViewingPet($strangerQuery, $viewingPetId);
        $strangerPosts = $strangerQuery->offset($strangerOffset)->limit($strangerLimit)->get();

        // 6. Combine and sort chronologically
        return $connPosts->concat($strangerPosts)->sortByDesc('created_at')->values();
    }

    public function getPetPosts(int $petId, ?int $viewingPetId = null)
    {
        $query = $this->post->where('pet_id', $petId)->with(['pet', 'veterinaryProfile'])->latest();
        $this->applyViewingPet($query, $viewingPetId);
        return $query->get();
    }

    public function getRandomPosts(int $limit = 20, ?int $viewingPetId = null)
    {
        $query = $this->post->inRandomOrder()->limit($limit)->with(['pet', 'veterinaryProfile']);
        $this->applyViewingPet($query, $viewingPetId);
        return $query->get();
    }

    public function getBatch(array $ids, ?int $viewingPetId = null)
    {
        $query = $this->post->whereIn('id', $ids)->with(['pet', 'veterinaryProfile']);
        $this->applyViewingPet($query, $viewingPetId);

        // Maintain the order of IDs (MySQL specific, skip for SQLite testing)
        if (!empty($ids) && DB::getDriverName() !== 'sqlite') {
            $idsString = implode(',', $ids);
            $query->orderByRaw("FIELD(id, $idsString)");
        }

        return $query->get();
    }
}
