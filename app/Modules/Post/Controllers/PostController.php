<?php

namespace App\Modules\Post\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Post\Payload\Requests\StorePostRequest;
use App\Modules\Post\Payload\Resources\PostResource;
use App\Modules\Post\Services\PostServiceInterface;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct(
        private PostServiceInterface $postService
    ) {
    }

    public function store(StorePostRequest $request)
    {
        // Verify pet ownership
        $pet = Auth::user()->pets()->findOrFail($request->pet_id);

        $post = $this->postService->createPost(
            $pet->id,
            $request->file('image'),
            $request->description
        );

        return ResponseHelper::success(new PostResource($post), HttpStatusEnum::CREATED->value, trans('post::post.created'));
    }

    public function index()
    {
        $viewingPetId = request()->query('pet_id');
        $posts = $this->postService->getFeed($viewingPetId);
        $posts->loadCount(['likes', 'comments']);
        return ResponseHelper::success(PostResource::collection($posts));
    }

    public function show($id)
    {
        $post = $this->postService->show($id);

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
        $post->load('pet');

        if ($post->pet->user_id !== Auth::id()) {
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
