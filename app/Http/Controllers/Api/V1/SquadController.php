<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Squad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Squad API Controller
 * Takım yönetimi API endpoint'leri
 */
class SquadController extends Controller
{
    /**
     * Takım listesi
     */
    public function index(Request $request): JsonResponse
    {
        $query = Squad::with(['leader.profile', 'game', 'members'])
            ->where('is_active', true);

        // Filtreleme
        if ($request->has('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        $squads = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $squads,
        ]);
    }

    /**
     * Takım detayı
     */
    public function show(int $id): JsonResponse
    {
        $squad = Squad::with(['leader.profile', 'game', 'members.profile'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $squad,
        ]);
    }

    /**
     * Yeni takım oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:squads,name',
            'game_id' => 'required|exists:games,id',
            'description' => 'nullable|string|max:1000',
            'max_members' => 'nullable|integer|min:2|max:10',
        ]);

        $validated['leader_id'] = $request->user()->id;
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;

        $squad = Squad::create($validated);

        // Lideri otomatik üye yap
        $squad->members()->attach($request->user()->id, [
            'role' => 'leader',
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Takım başarıyla oluşturuldu.',
            'data' => $squad->load(['leader.profile', 'game', 'members']),
        ], 201);
    }

    /**
     * Takımı güncelle
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $squad = Squad::findOrFail($id);

        // Yetki kontrolü
        if ($squad->leader_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu takımı güncelleme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:squads,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'max_members' => 'nullable|integer|min:2|max:10',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $squad->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Takım güncellendi.',
            'data' => $squad->load(['leader.profile', 'game', 'members']),
        ]);
    }

    /**
     * Takımı sil
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $squad = Squad::findOrFail($id);

        // Yetki kontrolü
        if ($squad->leader_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu takımı silme yetkiniz yok.',
            ], 403);
        }

        $squad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Takım silindi.',
        ]);
    }

    /**
     * Takıma üye davet et
     */
    public function invite(Request $request, int $id): JsonResponse
    {
        $squad = Squad::findOrFail($id);

        // Yetki kontrolü
        if ($squad->leader_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Üye davet etme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Kapasite kontrolü
        if ($squad->members()->count() >= $squad->max_members) {
            return response()->json([
                'success' => false,
                'message' => 'Takım dolu.',
            ], 400);
        }

        // Zaten üye mi kontrolü
        if ($squad->members()->where('user_id', $validated['user_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu kullanıcı zaten takım üyesi.',
            ], 400);
        }

        $squad->members()->attach($validated['user_id'], [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Üye takıma eklendi.',
        ]);
    }

    /**
     * Takımdan üye çıkar
     */
    public function removeMember(Request $request, int $id, int $userId): JsonResponse
    {
        $squad = Squad::findOrFail($id);

        // Yetki kontrolü
        if ($squad->leader_id !== $request->user()->id && $request->user()->id !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Bu işlemi yapma yetkiniz yok.',
            ], 403);
        }

        // Lider kendini çıkaramaz
        if ($userId === $squad->leader_id) {
            return response()->json([
                'success' => false,
                'message' => 'Lider takımdan çıkamaz.',
            ], 400);
        }

        $squad->members()->detach($userId);

        return response()->json([
            'success' => true,
            'message' => 'Üye takımdan çıkarıldı.',
        ]);
    }
}
