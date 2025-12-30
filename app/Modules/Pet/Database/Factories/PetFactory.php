<?php

namespace App\Modules\Pet\Database\Factories;

use App\Modules\Pet\Models\Pet;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->firstName,
            'type' => $this->faker->randomElement(['dog', 'cat']),
            'breed' => $this->faker->word,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'weight' => $this->faker->randomFloat(2, 1, 50),
            'birthdate' => $this->faker->date(),
            'is_neutered' => $this->faker->boolean,
            'image' => null, // We can't easily generate structured files in factory without complex setup
            'username' => $this->faker->unique()->userName,
            'bio' => $this->faker->sentence,
            'posts_count' => $this->faker->numberBetween(0, 100),
            'match_count' => $this->faker->numberBetween(0, 50),
        ];
    }
}
