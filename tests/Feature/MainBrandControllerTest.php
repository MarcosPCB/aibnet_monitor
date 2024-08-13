<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\MainBrand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainBrandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_mainbrand()
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/main-brand/create'.'/'.$user->account_id, [
            'name' => 'New MainBrand',
            'account_id' => $user->account_id,
            'main_brand_id' => $brand->id,
            'opponents' => [$brand->id],
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'New MainBrand',
                 ]);
    }

    /** @test */
    public function it_can_update_a_mainbrand()
    {
        $user = User::factory()->create();
        $mainBrand = MainBrand::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/main-brand/update/'.$mainBrand->id.'/'.$user->account_id, [
            'name' => 'Updated MainBrand',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Updated MainBrand',
                 ]);
    }

    /** @test */
    public function it_can_delete_a_mainbrand()
    {
        $user = User::factory()->create();
        $mainBrand = MainBrand::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/main-brand/delete/'.$mainBrand->id.'/'.$user->account_id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('main_brand', ['id' => $mainBrand->id]);
    }

    public function test_can_get_mainbrand()
    {
        $user = User::factory()->create();
        $mainBrand = MainBrand::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson(route('main-brand.get', [ $mainBrand->id, $user->account_id ]));

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $mainBrand->id,
                     'name' => $mainBrand->name,
                     // outros campos que você quer verificar
                 ]);
    }
}

