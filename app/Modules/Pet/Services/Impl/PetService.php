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

        if ($pet->user_id !== auth()->id()) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        return $pet;
    }

    public function store(array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $path = $data['image']->store('pets', 'public');
            $data['image'] = $path;
        }

        // Ensure user_id is set if not present
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        return $this->petRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $pet = $this->show($id); // Re-use show for auth check

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image
            if ($pet->image) {
                Storage::disk('public')->delete($pet->image);
            }
            $path = $data['image']->store('pets', 'public');
            $data['image'] = $path;
        }

        return $this->petRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        $pet = $this->show($id); // Re-use show for auth check

        if ($pet?->image) {
            Storage::disk('public')->delete($pet->image);
        }

        return $this->petRepository->delete($id);
    }
}
