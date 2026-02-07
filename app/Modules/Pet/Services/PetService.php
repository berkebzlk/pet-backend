<?php

namespace App\Modules\Pet\Services;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Pet\Models\Pet;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PetService extends BaseEloquentService
{
    public function __construct(
        protected Pet $pet
    ) {
        parent::__construct($pet);
    }

    private function getQueryWithCounts(): Builder
    {
        return $this->pet->newQuery()->withCount([
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

    public function index(array $requestData = [], ?Builder $query = null)
    {
        $query = $query ?? $this->getQueryWithCounts();
        return parent::index($requestData, $query);
    }

    public function show(int $id)
    {
        $pet = $this->getQueryWithCounts()->find($id);

        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }

        return $pet;
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Ensure user_id is set if not present
            if (!isset($data['user_id'])) {
                $data['user_id'] = auth()->id();
            }

            $image = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $image = $data['image'];
                unset($data['image']);
            }

            $pet = $this->pet->create($data);

            if ($image) {
                try {
                    $path = $image->store("users/{$pet->user_id}/pets/{$pet->id}/profilePhoto", 'public');

                    if (!$path) {
                        throw new Exception("Failed to store image file.");
                    }

                    $pet->update(['image' => $path]);
                } catch (Exception $e) {
                    throw new Exception("Image upload failed: " . $e->getMessage());
                }
            }

            return $pet;
        });
    }

    public function update(int $id, array $data)
    {
        $pet = $this->show($id);

        if ($pet->user_id !== auth()->id()) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image
            if ($pet->image) {
                Storage::disk('public')->delete($pet->image);
            }
            $path = $data['image']->store("users/{$pet->user_id}/pets/{$pet->id}/profilePhoto", 'public');
            $data['image'] = $path;
        }

        $pet->update($data);
        return $pet;
    }

    public function delete(int $id)
    {
        $pet = $this->show($id);

        if ($pet->user_id !== auth()->id()) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        if ($pet->image) {
            Storage::disk('public')->delete($pet->image);
        }

        return $pet->delete();
    }

    public function getByUsername(string $username)
    {
        return $this->getQueryWithCounts()->where('username', $username)->firstOrFail();
    }

    public function search(string $query, int $limit = 10)
    {
        return $this->pet->where('username', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->limit($limit)
            ->get();
    }
}
