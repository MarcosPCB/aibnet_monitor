<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Cria um novo Comment
    public function create(Request $request)
    {
        $request->validate([
            'url' => 'required|string',
            'platform_id' => 'required|string',
            'message' => 'nullable|string',
            'likes' => 'nullable|integer',
            'shares' => 'nullable|integer',
            'mentions' => 'nullable|string',
            'reactions_positive' => 'nullable|integer',
            'reactions_negative' => 'nullable|integer',
            'reactions_neutral' => 'nullable|integer',
            'item_url' => 'nullable|string',
            'has_video' => 'nullable|boolean',
            'has_image' => 'nullable|boolean',
            'has_external' => 'nullable|boolean',
            'user_gender' => 'nullable|in:Male,Female',
            'user_age' => 'nullable|integer',
            'num_user_followers' => 'nullable|integer',
            'post_id' => 'required|exists:post,id',
        ]);

        $comment = Comment::create($request->all());

        return response()->json($comment, 201);
    }

    // Atualiza um Comment existente
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $request->validate([
            'url' => 'sometimes|nullable|string',
            'platform_id' => 'sometimes|nullable|string',
            'message' => 'sometimes|nullable|string',
            'likes' => 'sometimes|nullable|integer',
            'shares' => 'sometimes|nullable|integer',
            'mentions' => 'sometimes|nullable|string',
            'reactions_positive' => 'sometimes|nullable|integer',
            'reactions_negative' => 'sometimes|nullable|integer',
            'reactions_neutral' => 'sometimes|nullable|integer',
            'item_url' => 'sometimes|nullable|string',
            'has_video' => 'sometimes|nullable|boolean',
            'has_image' => 'sometimes|nullable|boolean',
            'has_external' => 'sometimes|nullable|boolean',
            'user_gender' => 'sometimes|nullable|in:Male,Female',
            'user_age' => 'sometimes|nullable|integer',
            'num_user_followers' => 'sometimes|nullable|integer',
            'post_id' => 'sometimes|nullable|exists:post,id',
        ]);

        $comment->update($request->all());

        return response()->json($comment, 200);
    }

    // Deleta um Comment
    public function delete($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }

    public function get($id)
    {
        $comment = Comment::findOrFail($id);
        return response()->json($comment, 200);
    }
}

