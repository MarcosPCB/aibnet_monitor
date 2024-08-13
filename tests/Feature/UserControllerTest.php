<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/user/create'.'/'.$user->account_id, [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'account_id' => $account->id,
            'permission' => 'user',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'New User',
                     'email' => 'newuser@example.com',
                 ]);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/user/update/'.$user->id.'/'.$user->account_id, [
            'name' => 'Updated User',
            'permission' => 'admin',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Updated User',
                     'permission' => 'admin',
                 ]);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/user/delete/'.$user->id.'/'.$user->account_id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_can_get_user()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson(route('user.get', [ $user->id, $user->account_id ]));

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'name' => $user->name,
                     // outros campos que você quer verificar
                 ]);
    }
}

