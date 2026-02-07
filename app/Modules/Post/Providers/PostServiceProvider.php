<?php

namespace App\Modules\Post\Providers;

use App\Modules\Post\Models\Comment;
use App\Modules\Post\Policies\CommentPolicy;
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
        //
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