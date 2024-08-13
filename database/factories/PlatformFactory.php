<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformFactory extends Factory
{
    protected $model = Platform::class;

    public function definition()
    {
        return [
            'type' => $this->faker->word,
            'url' => $this->faker->url,
            'platform_id' => $this->faker->unique()->word,
            'name' => $this->faker->company,
            'avatar_url' => $this->faker->imageUrl,
            'description' => $this->faker->paragraph,
            'tags' => $this->faker->words(3, true),
            'num_followers' => $this->faker->numberBetween(0, 10000),
            'num_likes' => $this->faker->numberBetween(0, 10000),
            'capture_comments' => $this->faker->boolean,
            'capture_users_from_comments' => $this->faker->boolean,
            'active' => $this->faker->boolean,
            'brand_id' => \App\Models\Brand::factory(),
        ];
    }
}
