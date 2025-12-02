<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
            'parent_id' => 'nullable|exists:comments,id',
            'content' => 'required|string|max:500',
        ]);

        $validated['user_id'] = auth()->id();

        $comment = Comment::create($validated);

        return response()->json([
            'message' => 'Yorum eklendi',
            'data' => $comment->load(['user.profile', 'replies']),
        ], 201);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id() && !auth()->user()->is_admin) {
            return response()->json(['message' => 'Yetkisiz işlem'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Yorum silindi']);
    }
}
