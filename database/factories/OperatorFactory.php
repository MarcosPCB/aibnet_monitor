<?php

namespace Database\Factories;

use App\Models\Operator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class OperatorFactory extends Factory
{
    /**
     * O nome do modelo associado a esta fábrica.
     *
     * @var string
     */
    protected $model = Operator::class;

    /**
     * Defina o estado padrão do modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // senha padrão (você pode mudá-la conforme necessário)
            'permission' => 'employee', // Permissão padrão (pode ser "admin" ou "employee")
            'remember_token' => Str::random(10),
        ];
    }
}
