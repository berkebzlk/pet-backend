<?php

namespace App\Modules\Pet\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Core\Payload\Resources\PaginatedResource;
use App\Modules\Pet\Payload\Requests\StorePetRequest;
use App\Modules\Pet\Payload\Requests\UpdatePetRequest;
use App\Modules\Pet\Payload\Resources\PetResource;
use App\Modules\Pet\Services\PetServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PetController extends Controller
{
    public function __construct(
        private PetServiceInterface $petService
    ) {
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $pets = $this->petService->index($data);

        return ResponseHelper::success(new PaginatedResource($pets, PetResource::class));
    }

    public function myPets(Request $request)
    {
        $data = $request->all();
        $data['filters']['user_id'] = $request->user()->id;

        $pets = $this->petService->index($data);

        return ResponseHelper::success(new PaginatedResource($pets, PetResource::class));
    }

    public function store(StorePetRequest $request)
    {
        $pet = $this->petService->store($request->validated());
        return ResponseHelper::success(new PetResource($pet), HttpStatusEnum::CREATED->value, __('crud.created', ['attribute' => $this->petService->getModelName()]));
    }

    public function show($id)
    {
        $pet = $this->petService->show($id);
        return ResponseHelper::success(new PetResource($pet), HttpStatusEnum::OK->value);
    }

    public function update(UpdatePetRequest $request, $id)
    {
        $updatedPet = $this->petService->update($id, $request->validated());
        return ResponseHelper::success(new PetResource($updatedPet), HttpStatusEnum::OK->value, __('crud.updated', ['attribute' => $this->petService->getModelName()]));
    }

    public function destroy($id)
    {
        $this->petService->delete($id);

        return ResponseHelper::success(null, HttpStatusEnum::OK->value, __('crud.deleted', ['attribute' => $this->petService->getModelName()]));
    }
}
