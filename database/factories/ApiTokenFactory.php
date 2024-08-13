<?php

namespace Database\Factories;

use App\Models\ApiToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiTokenFactory extends Factory
{
    protected $model = ApiToken::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'url' => $this->faker->url,
            'doc_url' => $this->faker->url,
            'token' => $this->faker->sha256,
            'email' => $this->faker->email,
            'limit' => $this->faker->numberBetween(1000, 10000),
            'limit_type' => $this->faker->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'last_used' => now(),
            'limit_used' => 0,
            'status' => true,
        ];
    }
}

