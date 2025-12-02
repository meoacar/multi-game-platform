<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Tournament API Controller
 * Turnuva yönetimi API endpoint'leri
 */
class TournamentController extends Controller
{
    /**
     * Turnuva listesi
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tournament::with(['organizer.profile', 'game', 'teams']);

        // Filtreleme
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        $tournaments = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tournaments,
        ]);
    }

    /**
     * Turnuva detayı
     */
    public function show(int $id): JsonResponse
    {
        $tournament = Tournament::with(['organizer.profile', 'game', 'teams.captain.profile'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $tournament,
        ]);
    }

    /**
     * Yeni turnuva oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'description' => 'required|string',
            'rules' => 'required|string',
            'prize_pool' => 'nullable|string|max:255',
            'max_teams' => 'required|integer|min:2|max:64',
            'team_size' => 'required|integer|min:1|max:10',
            'registration_starts_at' => 'required|date',
            'registration_ends_at' => 'required|date|after:registration_starts_at',
            'tournament_starts_at' => 'required|date|after:registration_ends_at',
            'tournament_ends_at' => 'required|date|after:tournament_starts_at',
        ]);

        $validated['organizer_id'] = $request->user()->id;
        $validated['slug'] = Str::slug($validated['name']);
        $validated['status'] = 'upcoming';

        $tournament = Tournament::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Turnuva oluşturuldu.',
            'data' => $tournament->load(['organizer.profile', 'game']),
        ], 201);
    }

    /**
     * Turnuvaya kayıt ol
     */
    public function register(Request $request, int $id): JsonResponse
    {
        $tournament = Tournament::findOrFail($id);

        // Durum kontrolü
        if ($tournament->status !== 'registration_open') {
            return response()->json([
                'success' => false,
                'message' => 'Kayıtlar açık değil.',
            ], 400);
        }

        // Kapasite kontrolü
        if ($tournament->teams()->count() >= $tournament->max_teams) {
            return response()->json([
                'success' => false,
                'message' => 'Turnuva dolu.',
            ], 400);
        }

        $validated = $request->validate([
            'team_name' => 'required|string|max:255',
            'members' => 'required|array|size:' . $tournament->team_size,
            'members.*' => 'required|integer|exists:users,id',
        ]);

        // Duplicate kayıt kontrolü
        $existing = $tournament->teams()
            ->where('captain_id', $request->user()->id)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Zaten kayıtlısınız.',
            ], 400);
        }

        $team = $tournament->teams()->create([
            'captain_id' => $request->user()->id,
            'name' => $validated['team_name'],
            'members' => $validated['members'],
            'status' => 'registered',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Turnuvaya kayıt oldunuz.',
            'data' => $team,
        ]);
    }
}
