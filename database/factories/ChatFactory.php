<?php

namespace Database\Factories;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text' => $this->faker->randomElements(['post1', 'post2', 'post3', 'post4'], 2),
            'main_brand_id' => \App\Models\MainBrand::factory(),
        ];
    }
}
