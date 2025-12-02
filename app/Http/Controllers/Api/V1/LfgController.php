<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Lfg\StoreLfgRequest;
use App\Http\Requests\Api\V1\Lfg\UpdateLfgRequest;
use App\Http\Requests\Api\V1\Lfg\ApplyLfgRequest;
use App\Models\LfgPost;
use Illuminate\Http\Request;

/**
 * LFG (Looking For Group) Controller
 * Takım arama ilanları yönetimi
 */
class LfgController extends Controller
{
    /**
     * İlan listesi (filtreleme ile)
     * GET /api/v1/lfg
     */
    public function index(Request $request)
    {
        $query = LfgPost::with(['user.profile', 'game'])
            ->open();

        // Filtreler
        if ($request->has('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('rank')) {
            $query->where(function ($q) use ($request) {
                $q->where('min_rank', '<=', $request->rank)
                  ->orWhereNull('min_rank');
            })->where(function ($q) use ($request) {
                $q->where('max_rank', '>=', $request->rank)
                  ->orWhereNull('max_rank');
            });
        }

        if ($request->has('play_style')) {
            $query->where('play_style_tag', $request->play_style);
        }

        if ($request->has('microphone_required')) {
            $query->where('microphone_required', $request->boolean('microphone_required'));
        }

        // Sıralama
        $query->latest();

        $posts = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    /**
     * İlan detayı
     * GET /api/v1/lfg/{id}
     */
    public function show($id)
    {
        $post = LfgPost::with(['user.profile', 'game', 'applications.user.profile'])
            ->findOrFail($id);

        // Görüntülenme sayısını artır
        $post->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    /**
     * Yeni ilan oluştur
     * POST /api/v1/lfg
     */
    public function store(StoreLfgRequest $request)
    {
        // Policy kontrolü
        $this->authorize('create', LfgPost::class);

        $validated = $request->validated();

        $post = $request->user()->lfgPosts()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'İlan başarıyla oluşturuldu',
            'data' => $post->load(['user.profile', 'game']),
        ], 201);
    }

    /**
     * İlan güncelle
     * PUT /api/v1/lfg/{id}
     */
    public function update(UpdateLfgRequest $request, $id)
    {
        $post = LfgPost::findOrFail($id);

        // Policy kontrolü
        $this->authorize('update', $post);

        $validated = $request->validated();

        $post->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'İlan güncellendi',
            'data' => $post->fresh(['user.profile', 'game']),
        ]);
    }

    /**
     * İlan sil
     * DELETE /api/v1/lfg/{id}
     */
    public function destroy(Request $request, $id)
    {
        $post = LfgPost::findOrFail($id);

        // Policy kontrolü
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'İlan silindi',
        ]);
    }

    /**
     * İlana başvur
     * POST /api/v1/lfg/{id}/apply
     */
    public function apply(ApplyLfgRequest $request, $id)
    {
        $post = LfgPost::findOrFail($id);

        // Policy kontrolü
        $this->authorize('apply', $post);

        $validated = $request->validated();

        // Daha önce başvuru yapılmış mı kontrol et
        $existingApplication = $post->applications()
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'Bu ilana zaten başvurdunuz',
            ], 400);
        }

        $application = $post->applications()->create([
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
     * İlanın başvurularını listele
     * GET /api/v1/lfg/{id}/applications
     */
    public function applications(Request $request, $id)
    {
        $post = LfgPost::findOrFail($id);

        // Policy kontrolü
        $this->authorize('viewApplications', $post);

        $applications = $post->applications()
            ->with('user.profile')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }
}
