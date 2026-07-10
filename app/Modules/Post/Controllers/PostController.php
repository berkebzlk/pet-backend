<?php

namespace App\Modules\Post\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Post\Payload\Requests\StorePostRequest;
use App\Modules\Post\Payload\Resources\PostResource;
use App\Modules\Post\Services\PostService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {
    }

    public function store(StorePostRequest $request)
    {
        $petId = $request->pet_id;
        $vetId = $request->veterinary_profile_id;

        if ($petId) {
            // Verify pet ownership
            $pet = Auth::user()->pets()->findOrFail($petId);
            $post = $this->postService->createPost(
                $pet->id,
                $request->file('image'),
                $request->description
            );
        } else {
            // Verify veterinary profile ownership
            $vet = Auth::user()->veterinaryProfile;
            if (!$vet || $vet->id !== (int)$vetId) {
                abort(403, 'Unauthorized');
            }
            $post = $this->postService->createPost(
                null,
                $request->file('image'),
                $request->description,
                $vet->id
            );
        }

        return ResponseHelper::success(new PostResource($post), HttpStatusEnum::CREATED->value, trans('post::post.created'));
    }

    public function index()
    {
        $viewingPetId = request()->query('pet_id');
        $page = (int)request()->query('page', 1);
        $limit = (int)request()->query('limit', 10);

        $posts = $this->postService->getFeed($viewingPetId, $page, $limit);
        $posts = new \Illuminate\Database\Eloquent\Collection($posts);
        $posts->loadCount(['likes', 'comments']);

        // Calculate total count for pagination meta
        $total = 0;
        if ($viewingPetId) {
            $matchedIds = \Illuminate\Support\Facades\DB::table('matches')
                ->where(function($q) use ($viewingPetId) {
                    $q->where('initiator_pet_id', $viewingPetId)
                      ->orWhere('target_pet_id', $viewingPetId);
                })
                ->where('status', 5)
                ->get()
                ->map(fn($row) => $row->initiator_pet_id == $viewingPetId ? $row->target_pet_id : $row->initiator_pet_id)
                ->toArray();

            $breedingIds = \Illuminate\Support\Facades\DB::table('breeding_connections')
                ->where(function($q) use ($viewingPetId) {
                    $q->where('initiator_pet_id', $viewingPetId)
                      ->orWhere('target_pet_id', $viewingPetId);
                })
                ->where('status', 'accepted')
                ->get()
                ->map(fn($row) => $row->initiator_pet_id == $viewingPetId ? $row->target_pet_id : $row->initiator_pet_id)
                ->toArray();

            $connectedPetIds = array_values(array_unique(array_merge($matchedIds, $breedingIds)));

            $connTotal = \App\Modules\Post\Models\Post::whereIn('pet_id', $connectedPetIds)->count();
            
            $strangerTotal = \App\Modules\Post\Models\Post::where(function($q) use ($viewingPetId, $connectedPetIds) {
                $q->whereNull('pet_id')
                  ->orWhere(function($sub) use ($viewingPetId, $connectedPetIds) {
                      $sub->whereNotIn('pet_id', array_merge($connectedPetIds, [$viewingPetId]));
                  });
            })->count();

            $total = $connTotal + $strangerTotal;
        } else {
            $total = \App\Modules\Post\Models\Post::count();
        }

        return ResponseHelper::success([
            'items' => PostResource::collection($posts),
            'pagination' => [
                'current_page' => $page,
                'last_page' => (int)ceil($total / $limit),
                'per_page' => $limit,
                'total' => $total,
            ]
        ]);
    }

    public function show($id)
    {
        $viewingPetId = request()->query('pet_id');
        
        $query = \App\Modules\Post\Models\Post::where('id', $id);
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
        
        $post = $query->firstOrFail();

        $post->load(['pet', 'likes', 'comments']);
        $post->loadCount(['likes', 'comments']);
        return ResponseHelper::success(new PostResource($post));
    }

    public function getPetPosts($petId)
    {
        $viewingPetId = request()->query('pet_id');
        $posts = $this->postService->getPetPosts($petId, $viewingPetId);
        $posts->loadCount(['likes', 'comments']);
        return ResponseHelper::success(PostResource::collection($posts));
    }

    public function delete($id)
    {
        $post = $this->postService->show($id);
        $post->load(['pet', 'veterinaryProfile']);

        if ($post->pet) {
            if ($post->pet->user_id !== Auth::id()) {
                return ResponseHelper::error('Unauthorized', HttpStatusEnum::FORBIDDEN->value);
            }
        } elseif ($post->veterinaryProfile) {
            if ($post->veterinaryProfile->user_id !== Auth::id()) {
                return ResponseHelper::error('Unauthorized', HttpStatusEnum::FORBIDDEN->value);
            }
        } else {
            return ResponseHelper::error('Unauthorized', HttpStatusEnum::FORBIDDEN->value);
        }

        $post->delete();

        return ResponseHelper::success(null, HttpStatusEnum::OK->value, trans('post::post.deleted'));
    }

    public function random()
    {
        $viewingPetId = request()->query('pet_id');
        $limit = request()->query('limit', 20);
        $posts = $this->postService->getRandomPosts($limit, $viewingPetId);
        // No need to load counts for minimal resource
        return ResponseHelper::success(\App\Modules\Post\Payload\Resources\PostMinimalResource::collection($posts));
    }

    public function batch(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:posts,id',
        ]);

        $viewingPetId = $request->query('pet_id'); // Or from body if preferred, but query is standard for context
        // Actually for POST, body is better for ids, query for context
        $viewingPetId = $request->input('pet_id');

        $posts = $this->postService->getBatch($request->input('ids'), $viewingPetId);
        $posts->loadCount(['likes', 'comments']);

        return ResponseHelper::success(PostResource::collection($posts));
    }
}
