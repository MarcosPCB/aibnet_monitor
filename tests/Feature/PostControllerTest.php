<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_post()
    {
        $user = Operator::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/post/create', [
            'url' => 'https://example.com/post',
            'platform_id' => 'platform123',
            'title' => 'New Post',
            'description' => 'Description of the post',
            'tags' => 'tag1,tag2',
            'likes' => 10,
            'shares' => 5,
            'reactions_positive' => 7,
            'reactions_negative' => 1,
            'reactions_neutral' => 2,
            'item_url' => 'https://example.com/item',
            'is_video' => false,
            'is_image' => true,
            'is_external' => false,
            'mentions' => 'mention1,mention2',
            'internal_platform_id' => 1,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'title' => 'New Post',
                 ]);
    }

    /** @test */
    public function it_can_update_a_post()
    {
        $user = Operator::factory()->create();
        $post = Post::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/post/update/'.$post->id, [
            'title' => 'Updated Post',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'title' => 'Updated Post',
                     'description' => 'Updated description',
                 ]);
    }

    /** @test */
    public function it_can_delete_a_post()
    {
        $user = Operator::factory()->create();
        $post = Post::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/post/delete/'.$post->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('post', ['id' => $post->id]);
    }

    public function test_can_get_post()
    {
        $user = Operator::factory()->create();
        $post = Post::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson(route('post.get', $post->id));

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $post->id,
                     'title' => $post->title,
                     // outros campos que você quer verificar
                 ]);
    }
}

