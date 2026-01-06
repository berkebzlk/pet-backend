<?php

namespace App\Modules\Post\Tests\Feature;

use App\Modules\Pet\Models\Pet;
use App\Modules\Post\Models\Post;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchPostTest extends TestCase
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

    public function test_can_fetch_batch_posts()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        $posts = Post::factory()->count(5)->create(['pet_id' => $pet->id]);

        $targetPosts = $posts->take(3);
        $ids = $targetPosts->pluck('id')->toArray();

        $response = $this->actingAs($user, 'api')->postJson('/api/post/batch', [
            'ids' => $ids,
            'pet_id' => $pet->id
        ]);

        $response->assertOk();
        $response->assertJsonCount(3, 'data');

        // Verify order matches input IDs (if implemented) or just existence
        $returnedIds = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertEquals($ids, $returnedIds);
    }

    public function test_validates_ids_array()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/post/batch', [
            'ids' => 'not-an-array'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ids']);
    }
}
