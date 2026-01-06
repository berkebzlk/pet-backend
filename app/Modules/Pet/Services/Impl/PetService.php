<?php

namespace App\Modules\Pet\Services\Impl;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Services\Impl\BaseService;
use App\Modules\Pet\Repositories\PetRepositoryInterface;
use App\Modules\Pet\Services\PetServiceInterface;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PetService extends BaseService implements PetServiceInterface
{
    public function __construct(
        private PetRepositoryInterface $petRepository
    ) {
        parent::__construct($petRepository);
    }

    public function show(int $id)
    {
        $pet = $this->petRepository->findById($id);

        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }

        // Ownership check removed to allow viewing other users' pets

        return $pet;
    }

    public function store(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            // Ensure user_id is set if not present
            if (!isset($data['user_id'])) {
                $data['user_id'] = auth()->id();
            }

            // Create pet first to get ID
            // We need to handle image separately after creation if we want ID in path
            // Or we can use a temporary path and move it, but creating first is safer for ID consistency

            $image = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $image = $data['image'];
                unset($data['image']);
            }

            $pet = $this->petRepository->create($data);

            if ($image) {
                try {
                    $path = $image->store("users/{$pet->user_id}/pets/{$pet->id}/profilePhoto", 'public');

                    if (!$path) {
                        throw new Exception("Failed to store image file.");
                    }

                    $this->petRepository->update($pet->id, ['image' => $path]);
                    $pet->refresh();
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

        return $this->petRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        $pet = $this->show($id);

        if ($pet->user_id !== auth()->id()) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        if ($pet?->image) {
            Storage::disk('public')->delete($pet->image);
        }

        return $this->petRepository->delete($id);
    }

    public function getByUsername(string $username)
    {
        return $this->petRepository->getByUsername($username);
    }

    public function search(string $query, int $limit = 10)
    {
        return $this->petRepository->search($query, $limit);
    }
}
