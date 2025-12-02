<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use Illuminate\Http\Request;

class CommunityPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = CommunityPost::with(['user.profile', 'comments']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $posts = $query->latest()->paginate(20);

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:intro,general',
            'content' => 'required|string|max:1000',
        ]);

        $validated['user_id'] = auth()->id();

        $post = CommunityPost::create($validated);

        return response()->json([
            'message' => 'Post oluşturuldu',
            'data' => $post->load(['user.profile']),
        ], 201);
    }

    public function show(CommunityPost $communityPost)
    {
        return response()->json([
            'data' => $communityPost->load(['user.profile', 'comments.user.profile']),
        ]);
    }

    public function destroy(CommunityPost $communityPost)
    {
        if ($communityPost->user_id !== auth()->id() && !auth()->user()->is_admin) {
            return response()->json(['message' => 'Yetkisiz işlem'], 403);
        }

        $communityPost->delete();

        return response()->json(['message' => 'Post silindi']);
    }

    public function like(CommunityPost $communityPost)
    {
        $communityPost->increment('likes_count');

        return response()->json([
            'message' => 'Beğenildi',
            'likes_count' => $communityPost->likes_count,
        ]);
    }
}
