<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_account()
    {
        $user = Operator::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/account/create', [
            'name' => 'New Account',
            'token' => 'exampletoken',
            'payment_method' => 'credit_card',
            'installments' => 12,
            'contract_time' => 24,
            'contract_type' => 'full',
            'contract_description' => 'Description of the contract',
            'contract_brands' => 5,
            'contract_brand_opponents' => 3,
            'contract_users' => 10,
            'contract_build_brand_time' => 6,
            'contract_monitored' => 2,
            'cancel_time' => 0,
            'active' => true,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'New Account',
                     'payment_method' => 'credit_card',
                 ]);
    }

    /** @test */
    public function it_can_update_an_account()
    {
        $account = Account::factory()->create();
        $user = User::factory()->create();
        $user->account_id = $account->id;
        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/account/update/'.$account->id, [
            'name' => 'Updated Account Name',
            'token' => 'newtoken',
            'payment_method' => 'paypal',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Updated Account Name',
                     'payment_method' => 'paypal',
                 ]);
    }

    /** @test */
    public function it_can_create_account_and_user()
    {
        $user = Operator::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/account/complete', [
            'account' => [
                'name' => 'Complete Account',
                'token' => 'completeexampletoken',
                'payment_method' => 'credit_card',
                'installments' => 12,
                'contract_time' => 24,
                'contract_type' => 'full',
                'contract_description' => 'Complete description',
                'contract_brands' => 5,
                'contract_brand_opponents' => 3,
                'contract_users' => 10,
                'contract_build_brand_time' => 6,
                'contract_monitored' => 2,
                'cancel_time' => 0,
                'active' => true,
            ],
            'user' => [
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'password' => 'password',
                'permission' => 'admin'
            ],
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'account' => [
                        'name' => 'Complete Account'
                     ]
                 ]);

         // Verificar se o usuário foi criado
         $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);

        $this->assertDatabaseHas('users', ['account_id' => $response->json()['account']['id']]);
    }

    public function test_can_get_account()
    {
        $account = Account::factory()->create();
        $user = User::factory()->create();
        $user->account_id = $account->id;
        $response = $this->actingAs($user, 'sanctum')->getJson(route('account.get', $account->id));

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $account->id,
                     'name' => $account->name,
                     // outros campos que você quer verificar
                 ]);
    }
}
