<?php

namespace App\Modules\User\Tests\Feature;

use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSearchTest extends TestCase
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

    public function test_can_search_users_by_username()
    {
        User::factory()->create(['username' => 'john_doe', 'name' => 'John Doe']);
        User::factory()->create(['username' => 'jane_doe', 'name' => 'Jane Doe']);
        User::factory()->create(['username' => 'bob_smith', 'name' => 'Bob Smith']);

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/user/search?q=doe');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['username' => 'john_doe']);
        $response->assertJsonFragment(['username' => 'jane_doe']);
        $response->assertJsonMissing(['username' => 'bob_smith']);
    }

    public function test_can_search_users_by_name()
    {
        User::factory()->create(['username' => 'u1', 'name' => 'Michael Scott']);
        User::factory()->create(['username' => 'u2', 'name' => 'Michael Jordan']);
        User::factory()->create(['username' => 'u3', 'name' => 'Dwight Schrute']);

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/user/search?q=Michael');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['name' => 'Michael Scott']);
        $response->assertJsonFragment(['name' => 'Michael Jordan']);
    }

    public function test_search_returns_empty_array_if_no_query()
    {
        User::factory()->count(5)->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/user/search');

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}
