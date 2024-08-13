<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_comment()
    {
        $user = Operator::factory()->create();
        $post = Post::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/comment/create', [
            'url' => 'https://example.com/comment',
            'platform_id' => 'platform123',
            'message' => 'This is a comment',
            'likes' => 5,
            'shares' => 2,
            'mentions' => 'mention1',
            'reactions_positive' => 3,
            'reactions_negative' => 1,
            'reactions_neutral' => 1,
            'item_url' => 'https://example.com/item',
            'has_video' => false,
            'has_image' => true,
            'has_external' => false,
            'user_gender' => 'Female',
            'user_age' => 25,
            'num_user_followers' => 100,
            'post_id' => $post->id,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'This is a comment',
                 ]);
    }

    /** @test */
    public function it_can_update_a_comment()
    {
        $user = Operator::factory()->create();
        $comment = Comment::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/comment/update/'.$comment->id, [
            'message' => 'Updated comment message',
            'likes' => 10,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Updated comment message',
                     'likes' => 10,
                 ]);
    }

    /** @test */
    public function it_can_delete_a_comment()
    {
        $user = Operator::factory()->create();
        $comment = Comment::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/comment/delete/'.$comment->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comment', ['id' => $comment->id]);
    }

    public function test_can_get_comment()
    {
        $comment = Comment::factory()->create();
        $user = Operator::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson(route('comment.get', $comment->id));

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $comment->id,
                     'message' => $comment->message,
                     // outros campos que você quer verificar
                 ]);
    }
}

