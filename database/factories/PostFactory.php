<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'platform_id' => \App\Models\Platform::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'tags' => $this->faker->words(3, true),
            'likes' => $this->faker->numberBetween(0, 1000),
            'shares' => $this->faker->numberBetween(0, 1000),
            'reactions_positive' => $this->faker->numberBetween(0, 1000),
            'reactions_negative' => $this->faker->numberBetween(0, 1000),
            'reactions_neutral' => $this->faker->numberBetween(0, 1000),
            'item_url' => $this->faker->url,
            'is_video' => $this->faker->boolean,
            'is_image' => $this->faker->boolean,
            'is_external' => $this->faker->boolean,
            'mentions' => $this->faker->words(3, true),
            'internal_platform_id' => \App\Models\Platform::factory(), // Corrigir nome se necessário
        ];
    }
}
