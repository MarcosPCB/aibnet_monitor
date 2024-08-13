<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // Senha padrão
            'permission' => $this->faker->word,
            'account_id' => \App\Models\Account::factory(), // Cria uma conta associada
            'remember_token' => Str::random(10),
        ];
    }
}
