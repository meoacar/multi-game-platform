<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Squad;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SquadController extends Controller
{
    /**
     * Takımlar listesi
     * GET /takimlar
     */
    public function index(Request $request)
    {
        $query = Squad::with(['leader', 'members'])
            ->where('is_active', true);

        // Arama
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtreler
        if ($request->filled('game_mode')) {
            $query->where('game_mode', $request->game_mode);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        $squads = $query->latest()->paginate(12);

        return view('squads.index', compact('squads'));
    }

    /**
     * Takım detay
     * GET /takimlar/{squad}
     */
    public function show(Squad $squad)
    {
        $squad->load(['leader', 'members']);
        
        return view('squads.show', compact('squad'));
    }

    /**
     * Yeni takım oluşturma formu
     * GET /takimlar/yeni
     */
    public function create()
    {
        return view('squads.create');
    }

    /**
     * Takım oluştur
     * POST /takimlar
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_members' => 'required|integer|min:2|max:10',
            'game_mode' => 'nullable|string|in:TPP,FPP,Both',
            'rank_requirement' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
        ]);

        $validated['leader_id'] = $request->user()->id;
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);

        $squad = Squad::create($validated);

        // Lideri üye olarak ekle
        $squad->members()->attach($request->user()->id, [
            'role' => 'leader',
            'joined_at' => now(),
        ]);

        // XP kazandır
        $request->user()->addXp('squad_create');

        return redirect()->route('squads.show', $squad->slug)
            ->with('success', 'Takım başarıyla oluşturuldu! +25 XP kazandınız!');
    }
}
