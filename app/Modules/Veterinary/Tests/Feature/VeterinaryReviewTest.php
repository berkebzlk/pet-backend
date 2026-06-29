<?php

namespace App\Modules\Veterinary\Tests\Feature;

use App\Modules\User\Models\User;
use App\Modules\Pet\Models\Pet;
use App\Modules\Veterinary\Models\VeterinaryProfile;
use App\Modules\Veterinary\Models\VeterinaryReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VeterinaryReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock keys for Laravel Passport
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

    public function test_owner_cannot_review_own_clinic()
    {
        $owner = User::factory()->create();
        $clinic = VeterinaryProfile::create([
            'user_id' => $owner->id,
            'clinic_name' => 'My Veterinary Clinic',
            'city' => 'Bursa',
            'specialties' => ['Cerrahi'],
        ]);

        $pet = Pet::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner, 'api')->postJson("/api/veterinarians/{$clinic->id}/reviews", [
            'pet_id' => $pet->id,
            'rating' => 5,
            'comment' => 'Great clinic!'
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['message' => 'You cannot rate or review your own clinic.']);
    }

    public function test_pet_owner_can_review_clinic()
    {
        $owner = User::factory()->create();
        $clinic = VeterinaryProfile::create([
            'user_id' => $owner->id,
            'clinic_name' => 'Some Clinic',
            'city' => 'Istanbul',
            'specialties' => ['Dahiliye'],
        ]);

        $reviewer = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $reviewer->id]);

        $response = $this->actingAs($reviewer, 'api')->postJson("/api/veterinarians/{$clinic->id}/reviews", [
            'pet_id' => $pet->id,
            'rating' => 4,
            'comment' => 'Very friendly staff!'
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.rating', 4);
        $response->assertJsonPath('data.comment', 'Very friendly staff!');

        // Check if average rating and count updated on profile
        $clinic->refresh();
        $this->assertEquals(4.00, $clinic->average_rating);
        $this->assertEquals(1, $clinic->reviews_count);
    }

    public function test_multiple_reviews_recalculate_average_correctly()
    {
        $owner = User::factory()->create();
        $clinic = VeterinaryProfile::create([
            'user_id' => $owner->id,
            'clinic_name' => 'Big Clinic',
            'city' => 'Izmir',
            'specialties' => ['Kedi', 'Köpek'],
        ]);

        // Reviewer 1
        $reviewer1 = User::factory()->create();
        $pet1 = Pet::factory()->create(['user_id' => $reviewer1->id]);
        $this->actingAs($reviewer1, 'api')->postJson("/api/veterinarians/{$clinic->id}/reviews", [
            'pet_id' => $pet1->id,
            'rating' => 5,
        ]);

        // Reviewer 2
        $reviewer2 = User::factory()->create();
        $pet2 = Pet::factory()->create(['user_id' => $reviewer2->id]);
        $this->actingAs($reviewer2, 'api')->postJson("/api/veterinarians/{$clinic->id}/reviews", [
            'pet_id' => $pet2->id,
            'rating' => 3,
        ]);

        $clinic->refresh();
        $this->assertEquals(4.00, $clinic->average_rating);
        $this->assertEquals(2, $clinic->reviews_count);
    }

    public function test_can_retrieve_reviews_for_clinic()
    {
        $owner = User::factory()->create();
        $clinic = VeterinaryProfile::create([
            'user_id' => $owner->id,
            'clinic_name' => 'Review Clinic',
            'city' => 'Bursa',
            'specialties' => ['Cerrahi'],
        ]);

        $reviewer = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $reviewer->id]);

        VeterinaryReview::create([
            'veterinary_profile_id' => $clinic->id,
            'pet_id' => $pet->id,
            'rating' => 5,
            'comment' => 'Excellent!'
        ]);

        $response = $this->actingAs($reviewer, 'api')->getJson("/api/veterinarians/{$clinic->id}/reviews");

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.comment', 'Excellent!');
    }

    public function test_can_retrieve_unique_cities()
    {
        $owner1 = User::factory()->create();
        VeterinaryProfile::create([
            'user_id' => $owner1->id,
            'clinic_name' => 'Clinic 1',
            'city' => 'Bursa',
            'specialties' => ['Cerrahi'],
        ]);

        $owner2 = User::factory()->create();
        VeterinaryProfile::create([
            'user_id' => $owner2->id,
            'clinic_name' => 'Clinic 2',
            'city' => 'Istanbul',
            'specialties' => ['Dahiliye'],
        ]);

        $owner3 = User::factory()->create();
        VeterinaryProfile::create([
            'user_id' => $owner3->id,
            'clinic_name' => 'Clinic 3',
            'city' => 'Bursa',
            'specialties' => ['Göz Hastalıkları'],
        ]);

        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->getJson('/api/veterinarians/cities');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $this->assertTrue(in_array('Bursa', $response->json('data')));
        $this->assertTrue(in_array('Istanbul', $response->json('data')));
    }
}
