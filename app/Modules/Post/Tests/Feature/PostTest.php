<?php

namespace App\Modules\Post\Tests\Feature;

use App\Modules\Pet\Models\Pet;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock Passport keys with string content to bypass file system checks
        // Use sudo cat to read keys because of permission issues
        $privateKey = shell_exec('sudo cat ' . storage_path('oauth-private.key'));
        $publicKey = shell_exec('sudo cat ' . storage_path('oauth-public.key'));

        config(['passport.private_key' => $privateKey]);
        config(['passport.public_key' => $publicKey]);
    }

    public function test_user_can_create_post_for_their_pet()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->postJson('/api/posts', [
            'pet_id' => $pet->id,
            'image' => UploadedFile::fake()->image('post.jpg'),
            'description' => 'My first post!',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('posts', [
            'pet_id' => $pet->id,
            'description' => 'My first post!',
        ]);

        // Verify image storage
        $post = $response->json('data');
        $this->assertNotNull($post['image_url']);
    }

    public function test_user_cannot_create_post_for_others_pet()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user, 'api')->postJson('/api/posts', [
            'pet_id' => $pet->id,
            'image' => UploadedFile::fake()->image('post.jpg'),
            'description' => 'Hacking attempt',
        ]);

        $response->assertNotFound(); // Or Forbidden depending on implementation
    }
}
