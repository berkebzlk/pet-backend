<?php

namespace App\Modules\Match\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Match\Payload\Requests\StoreMatchRequest;
use App\Modules\Match\Payload\Resources\MatchResource;
use App\Modules\Match\Services\MatchServiceInterface;
use Illuminate\Routing\Controller;

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
}
