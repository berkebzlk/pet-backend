<?php

namespace App\Modules\Breeding\Controllers;

use App\Modules\Breeding\Services\BreedingService;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Core\Enums\HttpStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BreedingController extends Controller
{
    public function __construct(
        private BreedingService $breedingService
    ) {
    }

    public function discover(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id'
        ]);

        $filters = $request->only(['perPage', 'gender', 'exclude_connected']);
        $pets = $this->breedingService->discover($request->input('pet_id'), $filters);

        return ResponseHelper::success(
            new \App\Modules\Core\Payload\Resources\PaginatedResource($pets, \App\Modules\Pet\Payload\Resources\PetResource::class)
        );
    }

    public function pending(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id'
        ]);

        $requests = $this->breedingService->getPendingRequests($request->input('pet_id'));
        return ResponseHelper::success(\App\Modules\Breeding\Payload\Resources\BreedingConnectionResource::collection($requests), HttpStatusEnum::OK->value, 'Pending breeding requests retrieved successfully.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'initiator_pet_id' => 'required|exists:pets,id',
            'target_pet_id' => 'required|exists:pets,id|different:initiator_pet_id',
        ]);

        $connection = $this->breedingService->store($data);
        return ResponseHelper::success(new \App\Modules\Breeding\Payload\Resources\BreedingConnectionResource($connection), HttpStatusEnum::CREATED->value, 'Breeding request sent successfully.');
    }

    public function accept(int $id)
    {
        $connection = $this->breedingService->accept($id);
        return ResponseHelper::success(new \App\Modules\Breeding\Payload\Resources\BreedingConnectionResource($connection), HttpStatusEnum::OK->value, 'Breeding request accepted.');
    }

    public function reject(int $id)
    {
        $this->breedingService->reject($id);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Breeding request rejected.');
    }

    public function cancel(int $id)
    {
        $this->breedingService->cancel($id);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Breeding request cancelled.');
    }

    public function index(int $petId)
    {
        $connections = $this->breedingService->getConnections($petId);
        return ResponseHelper::success(\App\Modules\Pet\Payload\Resources\PetResource::collection($connections), HttpStatusEnum::OK->value, 'Breeding connections retrieved successfully.');
    }

    public function disconnect(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'target_pet_id' => 'required|exists:pets,id',
        ]);

        $this->breedingService->disconnect($request->input('pet_id'), $request->input('target_pet_id'));
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Breeding connection removed successfully.');
    }
}
