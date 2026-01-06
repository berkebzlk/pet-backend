<?php

namespace App\Modules\Post\Database\Factories;

use App\Modules\Pet\Models\Pet;
use App\Modules\Post\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'image_url' => $this->faker->imageUrl(),
            'description' => $this->faker->sentence(),
        ];
    }
}
