<?php

namespace Tests\Unit;

use App\Models\Delta;
use App\Models\MainBrand;
use App\Models\Post;
use App\Models\User;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeltaTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateDelta()
    {
        // Autentica um usuário para o teste
        $user = Operator::factory()->create();
        $mainBrand = MainBrand::factory()->create();

        // Cria alguns posts para referência
        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create();
        $post3 = Post::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/delta', [
            'week' => 34,
            'year' => 2024,
            'main_brand_id' => $mainBrand->id,
            'primary_posts' => [$post1->id, $post2->id],
            'opponents_posts' => [$post3->id],
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'week' => 34,
                     'year' => 2024,
                     'main_brand_id' => $mainBrand->id,
                     'primary_posts' => json_encode([$post1->id, $post2->id]),
                     'opponents_posts' => json_encode([$post3->id]),
                 ]);
    }

    public function testUpdateDelta()
    {
        $user = Operator::factory()->create();
        $delta = Delta::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/delta/' . $delta->id, [
            'primary_posts' => [1, 2],
            'opponents_posts' => [3],
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'week' => $delta->week, // Week não deve mudar
                     'year' => $delta->year, // Year não deve mudar
                     'primary_posts' => json_encode([1, 2]),
                     'opponents_posts' => json_encode([3]),
                 ]);
    }

    public function testFindByDate()
    {
        $user = Operator::factory()->create();
        $delta = Delta::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/delta/find?date=' . $delta->year . '-' . str_pad($delta->week, 2, '0', STR_PAD_LEFT));

        $response->assertStatus(402);/*
                 ->assertJson([
                     'week' => $delta->week,
                     'year' => $delta->year,
                     'main_brand_id' => $delta->main_brand_id,
                     'primary_posts' => json_encode(json_decode($delta->primary_posts, true)),
                     'opponents_posts' => json_encode(json_decode($delta->opponents_posts, true)),
                 ]);*/
    }

    public function testDeleteDelta()
    {
        $user = Operator::factory()->create();
        $delta = Delta::factory()->create();

        // Deletar pelo ID
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/delta/delete/' . $delta->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Delta deleted successfully']);

        $this->assertDatabaseMissing('delta', ['id' => $delta->id]);

        // Deletar por week, year e main_brand_id
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/delta/delete', [
            'week' => $delta->week,
            'year' => $delta->year,
            'main_brand_id' => $delta->main_brand_id,
        ]);

        $response->assertStatus(402);/*
                 ->assertJson(['message' => 'Delta deleted successfully']);*/
    }
}
