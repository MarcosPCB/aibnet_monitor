<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\User;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_platform()
    {
        $user = Operator::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/platform/create', [
            'type' => 'Social Media',
            'url' => 'https://example.com',
            'platform_id' => 'platform123',
            'name' => 'Example Platform',
            'avatar_url' => 'https://example.com/avatar.jpg',
            'description' => 'Description of the platform',
            'tags' => 'tag1,tag2',
            'num_followers' => 1000,
            'num_likes' => 500,
            'capture_comments' => true,
            'capture_users_from_comments' => false,
            'active' => true,
            'brand_id' => 1,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                    'platform' => [
                        'name' => 'Example Platform',
                    ]
                 ]);
    }

    /** @test */
    public function it_can_update_a_platform()
    {
        $user = Operator::factory()->create();
        $platform = Platform::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/platform/update/'.$platform->id, [
            'name' => 'Updated Platform',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'platform' => [
                        'name' => 'Updated Platform',
                        'description' => 'Updated description',
                    ]
                 ]);
    }

    /** @test */
    public function it_can_delete_a_platform()
    {
        $user = Operator::factory()->create();
        $platform = Platform::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/platform/delete/'.$platform->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('platform', ['id' => $platform->id]);
    }

    /** @test */
    public function it_can_check_if_platform_exists()
    {
        $user = Operator::factory()->create();
        $platform = Platform::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/platform/check', [
            'platform_id' => $platform->platform_id,
            'url' => $platform->url,
        ]);

        $response->assertStatus(200);
    }

    public function test_can_get_platform()
    {
        $user = Operator::factory()->create();
        $platform = Platform::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson(route('platform.get', $platform->id));

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $platform->id,
                     'name' => $platform->name,
                     // outros campos que você quer verificar
                 ]);
    }
}
