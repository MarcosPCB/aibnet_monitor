<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'active' => $this->faker->boolean,
            'main_brand_id' => \App\Models\MainBrand::factory(), // Opcional
        ];
    }
}
