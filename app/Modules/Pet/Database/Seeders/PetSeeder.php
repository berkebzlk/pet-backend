<?php

namespace App\Modules\Pet\Database\Seeders;

use App\Modules\Pet\Models\Pet;
use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->warn('No users found. Skipping Pet seeding.');
            return;
        }

        $pets = [
            [
                'name' => 'Buddy',
                'type' => 'dog',
                'breed' => 'Golden Retriever',
                'gender' => 'male',
                'birthdate' => '2020-05-15',
                'weight' => 30.5,
                'is_neutered' => true,
                'bio' => 'A friendly and energetic golden retriever who loves fetch.',
                'image' => null,
            ],
            [
                'name' => 'Luna',
                'type' => 'cat',
                'breed' => 'Siamese',
                'gender' => 'female',
                'birthdate' => '2021-08-20',
                'weight' => 4.2,
                'is_neutered' => true,
                'bio' => 'A vocal and affectionate Siamese cat.',
                'image' => null,
            ],
            [
                'name' => 'Charlie',
                'type' => 'bird',
                'breed' => 'Parrot',
                'gender' => 'male',
                'birthdate' => '2022-01-10',
                'weight' => 0.5,
                'is_neutered' => false,
                'bio' => 'A colorful parrot who loves to mimic sounds.',
                'image' => null,
            ],
        ];

        foreach ($pets as $petData) {
            $user->pets()->create($petData);
        }

        $this->command->info(count($pets) . ' pets seeded successfully for user: ' . $user->email);
    }
}
