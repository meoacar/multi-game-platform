<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Clan\StoreClanRequest;
use App\Http\Requests\Api\V1\Clan\UpdateClanRequest;
use App\Http\Requests\Api\V1\Clan\ApplyClanRequest;
use App\Models\Clan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Clan Controller
 * Klan yönetimi
 */
class ClanController extends Controller
{
    /**
     * Klan listesi (filtreleme ile)
     * GET /api/v1/clans
     */
    public function index(Request $request)
    {
        $query = Clan::with(['leader.profile', 'game']);

        // Filtreler
        if ($request->has('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('verified')) {
            $query->where('is_verified', $request->boolean('verified'));
        }

        // Sıralama
        $query->latest();

        $clans = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $clans,
        ]);
    }

    /**
     * Klan detayı
     * GET /api/v1/clans/{id}
     */
    public function show($id)
    {
        $clan = Clan::with(['leader.profile', 'game', 'members.profile'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $clan,
        ]);
    }

    /**
     * Yeni klan oluştur
     * POST /api/v1/clans
     */
    public function store(StoreClanRequest $request)
    {
        // Policy kontrolü
        $this->authorize('create', Clan::class);

        $validated = $request->validated();

        // Slug oluştur
        $validated['slug'] = Str::slug($validated['name']);
        
        // Aynı slug varsa unique yap
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Clan::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $clan = $request->user()->ownedClans()->create($validated);

        // Lideri otomatik üye yap
        $clan->members()->attach($request->user()->id, [
            'role' => 'leader',
            'joined_at' => now(),
        ]);

        $clan->updateMemberCount();

        return response()->json([
            'success' => true,
            'message' => 'Klan başarıyla oluşturuldu',
            'data' => $clan->load(['leader.profile', 'game']),
        ], 201);
    }

    /**
     * Klan güncelle
     * PUT /api/v1/clans/{id}
     */
    public function update(UpdateClanRequest $request, $id)
    {
        $clan = Clan::findOrFail($id);

        // Policy kontrolü
        $this->authorize('update', $clan);

        $validated = $request->validated();

        $clan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Klan güncellendi',
            'data' => $clan->fresh(['leader.profile', 'game']),
        ]);
    }

    /**
     * Klana başvur
     * POST /api/v1/clans/{id}/apply
     */
    public function apply(ApplyClanRequest $request, $id)
    {
        $clan = Clan::findOrFail($id);

        // Policy kontrolü
        $this->authorize('apply', $clan);

        $validated = $request->validated();

        // Daha önce başvuru yapılmış mı?
        $existingApplication = $clan->applications()
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'Bu klana zaten başvurdunuz',
            ], 400);
        }

        $application = $clan->applications()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Başvurunuz gönderildi',
            'data' => $application->load('user.profile'),
        ], 201);
    }

    /**
     * Klanın başvurularını listele
     * GET /api/v1/clans/{id}/applications
     */
    public function applications(Request $request, $id)
    {
        $clan = Clan::findOrFail($id);

        // Policy kontrolü
        $this->authorize('viewApplications', $clan);

        $applications = $clan->applications()
            ->with('user.profile')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }
}
