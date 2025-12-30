<?php

namespace App\Modules\Post\Providers;

use App\Modules\Post\Models\Comment;
use App\Modules\Post\Policies\CommentPolicy;
use App\Modules\Post\Repositories\Impl\PostRepository;
use App\Modules\Post\Repositories\PostRepositoryInterface;
use App\Modules\Post\Services\Impl\PostService;
use App\Modules\Post\Services\PostServiceInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PostServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            PostRepositoryInterface::class,
            PostRepository::class
        );
        $this->app->bind(
            PostServiceInterface::class,
            PostService::class
        );

        // Comments
        $this->app->bind(
            \App\Modules\Post\Repositories\CommentRepositoryInterface::class,
            \App\Modules\Post\Repositories\Impl\CommentRepository::class
        );
        $this->app->bind(
            \App\Modules\Post\Services\CommentServiceInterface::class,
            \App\Modules\Post\Services\Impl\CommentService::class
        );

        // Likes
        $this->app->bind(
            \App\Modules\Post\Repositories\LikeRepositoryInterface::class,
            \App\Modules\Post\Repositories\Impl\LikeRepository::class
        );
        $this->app->bind(
            \App\Modules\Post\Services\LikeServiceInterface::class,
            \App\Modules\Post\Services\Impl\LikeService::class
        );

        // Saved Posts
        $this->app->bind(
            \App\Modules\Post\Repositories\SavedPostRepositoryInterface::class,
            \App\Modules\Post\Repositories\Impl\SavedPostRepository::class
        );
        $this->app->bind(
            \App\Modules\Post\Services\SavedPostServiceInterface::class,
            \App\Modules\Post\Services\Impl\SavedPostService::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'post');
        Gate::policy(Comment::class, CommentPolicy::class);
    }
}