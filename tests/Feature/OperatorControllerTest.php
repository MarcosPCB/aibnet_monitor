<?php

namespace Tests\Feature;

use App\Models\Operator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorTest extends TestCase
{
    use RefreshDatabase;

    // Teste para registrar um novo operador
    public function test_operator_can_be_registered()
    {
        $response = $this->postJson('/api/operator/register', [
            'name' => 'Test Operator',
            'email' => 'operator@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('operator', [
            'email' => 'operator@example.com',
        ]);
    }

    // Teste para atualizar nome ou senha do operador
    public function test_operator_can_be_updated()
    {
        $operator = Operator::factory()->create();

        $response = $this->actingAs($operator, 'sanctum')->patchJson('/api/operator/update/'.$operator->id, [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('operator', [
            'name' => 'Updated Name',
        ]);
    }

    // Teste para atualizar permissão do operador
    public function test_operator_permission_can_be_updated()
    {
        $user = User::factory()->create();
        $operator = Operator::factory()->create();

        $response = $this->actingAs($operator, 'sanctum')->patchJson('/api/operator/permit/'.$operator->id, [
            'permission' => 'admin',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('operator', [
            'permission' => 'admin',
        ]);
    }

    // Teste para deletar operador
    public function test_operator_can_be_deleted()
    {
        $user = User::factory()->create();
        $operator = Operator::factory()->create();

        $response = $this->actingAs($operator, 'sanctum')->deleteJson('/api/operator/delete/'.$operator->id);

        $response->assertStatus(204);
        $this->assertDeleted($operator);
    }

    public function test_can_get_operator()
    {
        $operator = Operator::factory()->create();

        $response = $this->actingAs($operator, 'sanctum')->getJson(route('operator.get', $operator->id));

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $operator->id,
                     'name' => $operator->name,
                     // outros campos que você quer verificar
                 ]);
    }
}
