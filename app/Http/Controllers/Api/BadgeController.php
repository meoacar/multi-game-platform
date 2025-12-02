<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function __construct(
        private BadgeService $badgeService
    ) {}

    /**
     * Tüm rozetleri listele
     */
    public function index(Request $request): JsonResponse
    {
        $query = Badge::active()->visible();

        // Kategori filtresi
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Nadirlik filtresi
        if ($request->has('rarity')) {
            $query->byRarity($request->rarity);
        }

        $badges = $query->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $badges,
        ]);
    }

    /**
     * Kullanıcının rozetlerini getir
     */
    public function userBadges(Request $request): JsonResponse
    {
        $user = $request->user();

        // Açılmış rozetler
        $unlockedBadges = $user->badges()
            ->wherePivot('unlocked_at', '!=', null)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // İlerleme kaydedilen rozetler
        $inProgressBadges = $user->badges()
            ->wherePivot('unlocked_at', null)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // İstatistikler
        $stats = $this->badgeService->getUserBadgeStats($user);

        return response()->json([
            'success' => true,
            'data' => [
                'unlocked' => $unlockedBadges,
                'in_progress' => $inProgressBadges,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Kategoriye göre rozetler
     */
    public function byCategory(string $category): JsonResponse
    {
        $badges = $this->badgeService->getBadgesByCategory($category);

        return response()->json([
            'success' => true,
            'data' => $badges,
        ]);
    }

    /**
     * Nadirliğe göre rozetler
     */
    public function byRarity(string $rarity): JsonResponse
    {
        $badges = $this->badgeService->getBadgesByRarity($rarity);

        return response()->json([
            'success' => true,
            'data' => $badges,
        ]);
    }

    /**
     * Rozet detayı
     */
    public function show(string $slug): JsonResponse
    {
        $badge = Badge::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Kaç kullanıcı bu rozeti açmış
        $unlockedCount = $badge->users()
            ->wherePivot('unlocked_at', '!=', null)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'badge' => $badge,
                'unlocked_by' => $unlockedCount,
            ],
        ]);
    }
}
