<?php

namespace App\Modules\Match\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Match\Payload\Requests\StoreMatchRequest;
use App\Modules\Match\Payload\Resources\MatchResource;
use App\Modules\Match\Services\MatchServiceInterface;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function __construct(
        private MatchServiceInterface $matchService
    ) {
    }

    public function store(StoreMatchRequest $request)
    {
        $match = $this->matchService->store($request->validated());
        return ResponseHelper::success(new MatchResource($match), HttpStatusEnum::CREATED->value, 'Match request sent successfully.');
    }

    public function pending(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
        ]);

        $matches = $this->matchService->getPendingMatches($request->pet_id);
        return ResponseHelper::success(MatchResource::collection($matches));
    }

    public function accept(int $id)
    {
        $this->matchService->respondToMatch($id, StatusEnum::ACCEPTED);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Match accepted.');
    }

    public function reject(int $id)
    {
        $this->matchService->respondToMatch($id, StatusEnum::REJECTED);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Match rejected.');
    }

    public function check(Request $request)
    {
        $request->validate([
            'initiator_pet_id' => 'required|exists:pets,id',
            'target_pet_id' => 'required|exists:pets,id',
        ]);

        $match = $this->matchService->checkMatchStatus($request->initiator_pet_id, $request->target_pet_id);

        return ResponseHelper::success($match ? new MatchResource($match) : null);
    }
}
