<?php

namespace App\Modules\Pet\Database\Seeders;

use App\Modules\Pet\Models\Pet;
use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            // Create User
            $user = User::create([
                'name' => "User $i",
                'username' => "user$i",
                'email' => "user$i@example.com",
                'password' => Hash::make('123123123'),
            ]);

            $petUsername = "dog{$i}_username";

            // Create Pet
            $pet = Pet::create([
                'user_id' => $user->id,
                'name' => "Dog $i",
                'type' => 'dog',
                'breed' => 'Golden Retriever', // Default breed
                'gender' => $i % 2 == 0 ? 'female' : 'male',
                'weight' => rand(10, 30),
                'birthdate' => now()->subYears(rand(1, 10)),
                'is_neutered' => (bool) rand(0, 1),
                'bio' => "I am Dog $i. I love playing fetch!",
                'username' => $petUsername,
                'image' => null, // Will update after checking storage
                'posts_count' => rand(0, 50),
                'match_count' => rand(0, 50),
                'is_breeding_available' => (bool) rand(0, 1),
            ]);

            // Determine image source from backend storage
            $extensions = ['jpg', 'jpeg', 'png', 'webp'];

            foreach ($extensions as $ext) {
                $targetPath = "users/{$user->id}/pets/{$pet->id}/profilePhoto/profile.$ext";

                if (Storage::disk('public')->exists($targetPath)) {
                    // Update pet with image path
                    $pet->update(['image' => $targetPath]);
                    break;
                }
            }
        }
    }
}
