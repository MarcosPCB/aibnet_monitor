<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Cria um novo Post
    public function create(Request $request)
    {
        $request->validate([
            'url' => 'nullable|string',
            'platform_id' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'likes' => 'nullable|integer',
            'shares' => 'nullable|integer',
            'reactions_positive' => 'nullable|integer',
            'reactions_negative' => 'nullable|integer',
            'reactions_neutral' => 'nullable|integer',
            'item_url' => 'nullable|string',
            'is_video' => 'nullable|boolean',
            'is_image' => 'nullable|boolean',
            'is_external' => 'nullable|boolean',
            'mentions' => 'nullable|string',
        ]);

        $post = Post::create($request->all());

        return response()->json($post, 201);
    }

    // Atualiza um Post existente
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'url' => 'nullable|string',
            'platform_id' => 'sometimes|string',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'likes' => 'nullable|integer',
            'shares' => 'nullable|integer',
            'reactions_positive' => 'nullable|integer',
            'reactions_negative' => 'nullable|integer',
            'reactions_neutral' => 'nullable|integer',
            'item_url' => 'nullable|string',
            'is_video' => 'nullable|boolean',
            'is_image' => 'nullable|boolean',
            'is_external' => 'nullable|boolean',
            'mentions' => 'nullable|string',
        ]);

        $post->update($request->all());

        return response()->json($post, 200);
    }

    // Deleta um Post
    public function delete($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }

    public function get($id)
    {
        $post = Post::findOrFail($id);
        return response()->json($post, 200);
    }
}

