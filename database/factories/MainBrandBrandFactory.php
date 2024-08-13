<?php

namespace Database\Factories;

use App\Models\MainBrandBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

class MainBrandBrandFactory extends Factory
{
    protected $model = MainBrandBrand::class;

    public function definition()
    {
        return [
            'main_brand_id' => \App\Models\MainBrand::factory(),
            'brand_id' => \App\Models\Brand::factory(),
            'is_opponent' => $this->faker->boolean,
        ];
    }
}
