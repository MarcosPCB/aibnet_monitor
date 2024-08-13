<?php

namespace Database\Factories;

use App\Models\Delta;
use App\Models\MainBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeltaFactory extends Factory
{
    protected $model = Delta::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'week' => $this->faker->numberBetween(1, 52),
            'year' => $this->faker->year,
            'main_brand_id' => MainBrand::factory(),
            'primary_posts' => $this->faker->randomElements(['post1', 'post2', 'post3', 'post4'], 2),
            'opponents_posts' => $this->faker->randomElements(['post5', 'post6', 'post7', 'post8'], 2),
        ];
    }
}
