<?php

namespace App\Modules\Post\Tests\Feature;

use App\Modules\Pet\Models\Pet;
use App\Modules\Post\Models\Post;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RandomPostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Generate mock keys for testing
        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privateKey);
        $publicKey = openssl_pkey_get_details($res)['key'];

        config(['passport.private_key' => $privateKey]);
        config(['passport.public_key' => $publicKey]);
    }

    public function test_can_fetch_random_posts()
    {
        // Create 30 posts
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        Post::factory()->count(30)->create(['pet_id' => $pet->id]);

        $response = $this->actingAs($user, 'api')->getJson('/api/post/random?limit=10');

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'image_url',
                ]
            ]
        ]);
    }

    public function test_random_posts_are_actually_random()
    {
        // Create enough posts to make randomness likely
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        Post::factory()->count(50)->create(['pet_id' => $pet->id]);

        $response1 = $this->actingAs($user, 'api')->getJson('/api/post/random?limit=5');
        $response2 = $this->actingAs($user, 'api')->getJson('/api/post/random?limit=5');

        $ids1 = collect($response1->json('data'))->pluck('id')->sort()->values();
        $ids2 = collect($response2->json('data'))->pluck('id')->sort()->values();

        // There's a tiny chance they match exactly, but highly unlikely with 50 posts and picking 5
        $this->assertNotEquals($ids1, $ids2);
    }
}
