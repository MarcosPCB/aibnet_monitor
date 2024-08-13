<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'token' => $this->faker->uuid,
            'payment_method' => $this->faker->word,
            'installments' => $this->faker->numberBetween(1, 12),
            'contract_time' => $this->faker->numberBetween(1, 24),
            'paid' => $this->faker->boolean,
            'contract_type' => $this->faker->word,
            'contract_description' => $this->faker->paragraph,
            'contract_brands' => $this->faker->numberBetween(1, 100),
            'contract_brand_opponents' => $this->faker->numberBetween(1, 100),
            'contract_users' => $this->faker->numberBetween(1, 100),
            'contract_build_brand_time' => $this->faker->numberBetween(1, 100),
            'contract_monitored' => $this->faker->boolean,
            'cancel_time' => $this->faker->numberBetween(1, 100),
            'active' => $this->faker->boolean,
        ];
    }
}
