<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Criação de um operador para autenticação
        $this->operator = Operator::factory()->create([
            'permission' => 'operator',
        ]);

        $this->actingAs($this->operator, 'sanctum');
    }

    public function testCreateApiToken()
    {
        $response = $this->postJson('/api/api-tokens', [
            'name' => 'Test API',
            'url' => 'https://testapi.com',
            'doc_url' => 'https://testapi.com/docs',
            'token' => 'testtoken123',
            'email' => 'test@example.com',
            'limit' => 1000,
            'limit_type' => 'daily',
            'limit_used' => 0,
            'status' => true,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'Test API',
                     'url' => 'https://testapi.com',
                     'doc_url' => 'https://testapi.com/docs',
                     'token' => 'testtoken123',
                     'email' => 'test@example.com',
                     'limit' => 1000,
                     'limit_type' => 'daily',
                     'limit_used' => 0,
                     'status' => true,
                 ]);
    }

    public function testUpdateApiToken()
    {
        $apiToken = ApiToken::factory()->create();

        $response = $this->patchJson("/api/api-tokens/{$apiToken->id}", [
            'name' => 'Updated API',
            'url' => 'https://updatedapi.com',
            'doc_url' => 'https://updatedapi.com/docs',
            'token' => 'updatedtoken123',
            'email' => 'updated@example.com',
            'limit' => 2000,
            'limit_type' => 'weekly',
            'last_used' => now(),
            'limit_used' => 10,
            'status' => false,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Updated API',
                     'url' => 'https://updatedapi.com',
                     'doc_url' => 'https://updatedapi.com/docs',
                     'token' => 'updatedtoken123',
                     'email' => 'updated@example.com',
                     'limit' => 2000,
                     'limit_type' => 'weekly',
                     'limit_used' => 10,
                     'status' => false,
                 ]);
    }

    public function testGetApiToken()
    {
        $apiToken = ApiToken::factory()->create();

        $response = $this->getJson("/api/api-tokens/{$apiToken->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => $apiToken->name,
                     'url' => $apiToken->url,
                     'doc_url' => $apiToken->doc_url,
                     'token' => $apiToken->token,
                     'email' => $apiToken->email,
                     'limit' => $apiToken->limit,
                     'limit_type' => $apiToken->limit_type,
                     'limit_used' => $apiToken->limit_used,
                     'status' => $apiToken->status,
                 ]);
    }

    public function testRestartLimit()
    {
        $apiToken = ApiToken::factory()->create([
            'limit_type' => 'daily',
            'last_used' => now()->subDays(1),
        ]);

        $response = $this->patchJson("/api/api-tokens/{$apiToken->id}/restart-limit");

        $response->assertStatus(200);
        $this->assertEquals(0, $apiToken->fresh()->limit_used);
    }

    public function testRestartLimitForce()
    {
        $apiToken = ApiToken::factory()->create();

        $response = $this->patchJson("/api/api-tokens/{$apiToken->id}/restart-limit-force");

        $response->assertStatus(200);
        $this->assertEquals(0, $apiToken->fresh()->limit_used);
    }

    public function testUpdateLimitUsed()
    {
        $apiToken = ApiToken::factory()->create();

        $response = $this->patchJson("/api/api-tokens/{$apiToken->id}/update-limit-used", [
            'limit_used' => 50,
        ]);

        $response->assertStatus(200);
        $this->assertEquals(50, $apiToken->fresh()->limit_used);
    }

    public function testDeactivateApiToken()
    {
        $apiToken = ApiToken::factory()->create();

        $response = $this->patchJson("/api/api-tokens/{$apiToken->id}/deactivate");

        $response->assertStatus(200);
    }

    public function testDeleteApiToken()
    {
        $apiToken = ApiToken::factory()->create();

        $response = $this->deleteJson("/api/api-tokens/{$apiToken->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('api_tokens', ['id' => $apiToken->id]);
    }
}

