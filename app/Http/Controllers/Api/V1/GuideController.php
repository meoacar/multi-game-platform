<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GuidePost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = GuidePost::with(['user.profile', 'game'])
            ->where('is_published', true);

        if ($request->has('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $guides = $query->latest()->paginate(15);

        return response()->json($guides);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'nullable|exists:games,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();

        $guide = GuidePost::create($validated);

        return response()->json([
            'message' => 'Rehber başarıyla oluşturuldu',
            'data' => $guide->load(['user.profile', 'game']),
        ], 201);
    }

    public function show(GuidePost $guide)
    {
        $guide->increment('views_count');
        
        return response()->json([
            'data' => $guide->load(['user.profile', 'game', 'comments.user.profile']),
        ]);
    }

    public function update(Request $request, GuidePost $guide)
    {
        $this->authorize('update', $guide);

        $validated = $request->validate([
            'game_id' => 'nullable|exists:games,id',
            'title' => 'string|max:255',
            'content' => 'string',
            'is_published' => 'boolean',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        }

        $guide->update($validated);

        return response()->json([
            'message' => 'Rehber güncellendi',
            'data' => $guide->load(['user.profile', 'game']),
        ]);
    }

    public function destroy(GuidePost $guide)
    {
        $this->authorize('delete', $guide);

        $guide->delete();

        return response()->json([
            'message' => 'Rehber silindi',
        ]);
    }

    public function like(GuidePost $guide)
    {
        $guide->increment('likes_count');

        return response()->json([
            'message' => 'Beğenildi',
            'likes_count' => $guide->likes_count,
        ]);
    }
}
