<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        // Eager loading ile count'ları al (Requirements 15.3)
        $games = Game::withCount(['lfgPosts', 'clans', 'guides'])->get();
        
        return view('admin.games.index', compact('games'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games',
            'is_active' => 'boolean',
        ]);

        Game::create($validated);
        return back()->with('success', 'Oyun eklendi.');
    }

    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $game->update($validated);
        return back()->with('success', 'Oyun güncellendi.');
    }

    public function destroy($id)
    {
        Game::findOrFail($id)->delete();
        return back()->with('success', 'Oyun silindi.');
    }
}
