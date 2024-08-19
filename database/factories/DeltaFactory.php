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
            'brand_id' => MainBrand::factory(),
            'json' => $this->faker->randomElements(['post1', 'post2', 'post3', 'post4'], 2),
        ];
    }
}
