<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'platform_id' => \App\Models\Platform::factory(),
            'message' => $this->faker->paragraph,
            'likes' => $this->faker->numberBetween(0, 1000),
            'shares' => $this->faker->numberBetween(0, 1000),
            'mentions' => $this->faker->words(3, true),
            'reactions_positive' => $this->faker->numberBetween(0, 1000),
            'reactions_negative' => $this->faker->numberBetween(0, 1000),
            'reactions_neutral' => $this->faker->numberBetween(0, 1000),
            'item_url' => $this->faker->url,
            'has_video' => $this->faker->boolean,
            'has_image' => $this->faker->boolean,
            'has_external' => $this->faker->boolean,
            'user_gender' => $this->faker->randomElement(['Male', 'Female']),
            'user_age' => $this->faker->numberBetween(18, 99),
            'num_user_followers' => $this->faker->numberBetween(0, 10000),
            'post_id' => \App\Models\Post::factory(),
        ];
    }
}
