<?php

namespace App\Modules\Pet\Database\Seeders;

use App\Modules\Pet\Models\Pet;
use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure storage directory exists
        if (!Storage::disk('public')->exists('pets')) {
            Storage::disk('public')->makeDirectory('pets');
        }

        // Desktop path for images
        $desktopPath = '/home/berke/Desktop';

        for ($i = 1; $i <= 10; $i++) {
            // Create User
            $user = User::create([
                'name' => "User $i",
                'username' => "user$i",
                'email' => "user$i@example.com",
                'password' => Hash::make('123123123'),
            ]);

            // Determine image source
            $imageName = "dog$i";
            $extensions = ['jpg', 'jpeg', 'png', 'webp'];
            $sourcePath = null;
            $extension = null;

            foreach ($extensions as $ext) {
                $path = "$desktopPath/$imageName.$ext";
                if (File::exists($path)) {
                    $sourcePath = $path;
                    $extension = $ext;
                    break;
                }
            }

            $petUsername = "dog{$i}_username";

            // Create Pet without image first
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
                'image' => null, // Will update after upload
                'posts_count' => rand(0, 50),
                'match_count' => rand(0, 50),
            ]);

            if ($sourcePath) {
                // Create directory structure: pets/{user_id}/{pet_id}/
                $targetDir = "pets/{$user->id}/{$pet->id}";

                // Create directory in public disk
                Storage::disk('public')->makeDirectory($targetDir);

                // Generate filename
                $fileName = "profile.$extension";
                $targetPath = "$targetDir/$fileName";

                // Copy file
                Storage::disk('public')->put($targetPath, File::get($sourcePath));

                // Update pet with image path
                $pet->update(['image' => $targetPath]);
            }
        }
    }
}
