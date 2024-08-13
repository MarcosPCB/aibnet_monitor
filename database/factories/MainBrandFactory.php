<?php

namespace Database\Factories;

use App\Models\MainBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

class MainBrandFactory extends Factory
{
    protected $model = MainBrand::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'follow_tags' => $this->faker->words(3, true),
            'mentions' => $this->faker->words(3, true),
            'past_stamp' => $this->faker->dateTime,
            'account_id' => \App\Models\Account::factory(),
        ];
    }
}
