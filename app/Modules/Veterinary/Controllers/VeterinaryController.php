<?php

namespace App\Modules\Veterinary\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Core\Payload\Resources\PaginatedResource;
use App\Modules\Veterinary\Payload\Requests\StoreVeterinaryProfileRequest;
use App\Modules\Veterinary\Payload\Resources\VeterinaryProfileResource;
use App\Modules\Veterinary\Services\VeterinaryProfileService;
use App\Modules\Post\Payload\Resources\PostResource;
use App\Modules\Veterinary\Payload\Requests\StoreVeterinaryReviewRequest;
use App\Modules\Veterinary\Payload\Resources\VeterinaryReviewResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VeterinaryController extends Controller
{
    public function __construct(
        private VeterinaryProfileService $veterinaryProfileService
    ) {
    }

    public function index(Request $request)
    {
        $data = $request->all();

        // Support ?city=Bursa query direct filter
        if ($request->has('city') && !empty($request->input('city'))) {
            if (!isset($data['filters']) || !is_array($data['filters'])) {
                $data['filters'] = [];
            }
            $data['filters']['city'] = $request->input('city');
        }

        $profiles = $this->veterinaryProfileService->index($data);

        return ResponseHelper::success(new PaginatedResource($profiles, VeterinaryProfileResource::class));
    }

    public function store(StoreVeterinaryProfileRequest $request)
    {
        try {
            $profile = $this->veterinaryProfileService->store($request->validated());
            
            // Reload user with veterinaryProfile relation for frontend sync
            $user = auth()->user();
            $user->load('veterinaryProfile');

            return ResponseHelper::success(
                new VeterinaryProfileResource($profile),
                HttpStatusEnum::CREATED->value,
                __('crud.created', ['attribute' => $this->veterinaryProfileService->getModelName()])
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e->getCode() ?: HttpStatusEnum::BAD_REQUEST->value
            );
        }
    }

    public function show($id)
    {
        try {
            $profile = $this->veterinaryProfileService->show($id);
            return ResponseHelper::success(new VeterinaryProfileResource($profile), HttpStatusEnum::OK->value);
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                HttpStatusEnum::NOT_FOUND->value
            );
        }
    }

    public function getPosts($id)
    {
        try {
            $posts = $this->veterinaryProfileService->getPosts($id);
            return ResponseHelper::success(PostResource::collection($posts), HttpStatusEnum::OK->value);
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                HttpStatusEnum::BAD_REQUEST->value
            );
        }
    }

    public function getReviews($id)
    {
        try {
            $reviews = $this->veterinaryProfileService->getReviews($id);
            return ResponseHelper::success(VeterinaryReviewResource::collection($reviews), HttpStatusEnum::OK->value);
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                HttpStatusEnum::BAD_REQUEST->value
            );
        }
    }

    public function addReview(StoreVeterinaryReviewRequest $request, $id)
    {
        try {
            $review = $this->veterinaryProfileService->addOrUpdateReview($id, $request->validated());
            return ResponseHelper::success(
                new VeterinaryReviewResource($review),
                HttpStatusEnum::CREATED->value,
                __('crud.created', ['attribute' => 'Review'])
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e->getCode() ?: HttpStatusEnum::BAD_REQUEST->value
            );
        }
    }

    public function getCities()
    {
        try {
            $cities = $this->veterinaryProfileService->getCities();
            return ResponseHelper::success($cities, HttpStatusEnum::OK->value);
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                HttpStatusEnum::BAD_REQUEST->value
            );
        }
    }
}
