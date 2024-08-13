<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Platform;
use App\Models\User;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_brand()
    {
        $user = Operator::factory()->create();
        $platform = Platform::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/brand/create', [
            'name' => 'New Brand',
            'description' => 'Test description',
            'platforms' => [$platform],
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'New Brand',
                 ]);
    }

    /** @test */
    public function it_can_update_a_brand()
    {
        $user = Operator::factory()->create();
        $brand = Brand::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/brand/update/'.$brand->id, [
            'name' => 'Updated Brand',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Updated Brand',
                 ]);
    }

    public function test_can_get_brand()
    {
        $user = Operator::factory()->create();
        $brand = Brand::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson(route('brand.get', $brand->id));

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $brand->id,
                     'name' => $brand->name,
                     // outros campos que você quer verificar
                 ]);
    }
}

