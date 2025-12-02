<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GuidePost;
use App\Models\Game;
use Illuminate\Http\Request;

class GuideController extends Controller
{
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

        $guides = $query->latest()->paginate(12);
        $games = Game::where('is_active', true)->get();

        return view('guides.index', compact('guides', 'games'));
    }

    public function show(GuidePost $guide)
    {
        $guide->increment('views_count');
        $guide->load(['user.profile', 'game', 'comments.user.profile']);

        return view('guides.show', compact('guide'));
    }

    public function create()
    {
        $games = Game::where('is_active', true)->get();
        return view('guides.create', compact('games'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'title' => 'required|string|min:10|max:200',
            'content' => 'required|string|min:100',
            'is_published' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');

        $guide = GuidePost::create($validated);

        return redirect()
            ->route('guides.show', $guide->id)
            ->with('success', 'Rehber başarıyla oluşturuldu!');
    }

    public function edit(GuidePost $guide)
    {
        $this->authorize('update', $guide);
        $games = Game::where('is_active', true)->get();
        return view('guides.edit', compact('guide', 'games'));
    }

    public function update(Request $request, GuidePost $guide)
    {
        $this->authorize('update', $guide);

        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'title' => 'required|string|min:10|max:200',
            'content' => 'required|string|min:100',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');

        $guide->update($validated);

        return redirect()
            ->route('guides.show', $guide->id)
            ->with('success', 'Rehber başarıyla güncellendi!');
    }

    public function destroy(GuidePost $guide)
    {
        $this->authorize('delete', $guide);
        
        $guide->delete();

        return redirect()
            ->route('guides.index')
            ->with('success', 'Rehber başarıyla silindi!');
    }
}
