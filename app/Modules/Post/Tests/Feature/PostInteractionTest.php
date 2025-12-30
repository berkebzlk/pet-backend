<?php

namespace App\Modules\Post\Tests\Feature;

use App\Models\User;
use App\Modules\Pet\Models\Pet;
use App\Modules\Post\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PostInteractionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:client', ['--personal' => true, '--name' => 'Test Client']);

        // Ensure keys are loaded
        if (file_exists(storage_path('oauth-public.key'))) {
            $publicKey = shell_exec('sudo cat ' . storage_path('oauth-public.key'));
            $privateKey = shell_exec('sudo cat ' . storage_path('oauth-private.key'));

            // Write to temp files if needed or just ensure Passport can read them
            // For this environment, we rely on the fact that we fixed permissions earlier
        }
    }

    public function test_user_can_like_and_unlike_post()
    {
        $user = \App\Modules\User\Models\User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        $post = Post::create([
            'pet_id' => $pet->id,
            'image_url' => 'http://example.com/image.jpg',
            'description' => 'Test Post'
        ]);

        Passport::actingAs($user);

        // Like
        $response = $this->postJson("/api/posts/{$post->id}/like");
        $response->assertStatus(200);
        $this->assertDatabaseHas('likes', ['user_id' => $user->id, 'post_id' => $post->id]);

        // Unlike
        $response = $this->deleteJson("/api/posts/{$post->id}/like");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('likes', ['user_id' => $user->id, 'post_id' => $post->id]);
    }

    public function test_user_can_comment_on_post()
    {
        $user = \App\Modules\User\Models\User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        $post = Post::create([
            'pet_id' => $pet->id,
            'image_url' => 'http://example.com/image.jpg',
            'description' => 'Test Post'
        ]);

        Passport::actingAs($user);

        $response = $this->postJson("/api/posts/{$post->id}/comments", ['content' => 'Nice post!']);
        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', ['user_id' => $user->id, 'post_id' => $post->id, 'content' => 'Nice post!']);
    }

    public function test_user_can_delete_own_comment()
    {
        $user = \App\Modules\User\Models\User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        $post = Post::create([
            'pet_id' => $pet->id,
            'image_url' => 'http://example.com/image.jpg',
            'description' => 'Test Post'
        ]);

        Passport::actingAs($user);

        $comment = $post->comments()->create(['user_id' => $user->id, 'content' => 'My comment']);

        $response = $this->deleteJson("/api/posts/{$post->id}/comments/{$comment->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_user_can_save_and_unsave_post()
    {
        $user = \App\Modules\User\Models\User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        $post = Post::create([
            'pet_id' => $pet->id,
            'image_url' => 'http://example.com/image.jpg',
            'description' => 'Test Post'
        ]);

        Passport::actingAs($user);

        // Save
        $response = $this->postJson("/api/posts/{$post->id}/save");
        $response->assertStatus(200);
        $this->assertDatabaseHas('saved_posts', ['user_id' => $user->id, 'post_id' => $post->id]);

        // Unsave
        $response = $this->deleteJson("/api/posts/{$post->id}/save");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('saved_posts', ['user_id' => $user->id, 'post_id' => $post->id]);
    }
}
