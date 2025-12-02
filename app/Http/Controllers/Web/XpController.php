<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\XpService;
use Illuminate\Http\Request;

/**
 * XP Controller
 * XP sistemi ve leaderboard
 */
class XpController extends Controller
{
    protected $xpService;

    public function __construct(XpService $xpService)
    {
        $this->xpService = $xpService;
    }

    /**
     * Leaderboard sayfası
     * GET /liderlik-tablosu
     */
    public function leaderboard()
    {
        $leaderboard = $this->xpService->getLeaderboard(50);

        return view('xp.leaderboard', compact('leaderboard'));
    }

    /**
     * Kullanıcının XP geçmişi
     * GET /profilim/xp-gecmisi
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $xpHistory = $this->xpService->getUserXpHistory($user, 50);
        
        $level = $user->getLevel();
        $progress = $user->getLevelProgress();

        return view('xp.history', compact('xpHistory', 'level', 'progress'));
    }

    /**
     * Badge'ler sayfası
     * GET /rozetler
     */
    public function badges(Request $request)
    {
        $user = $request->user();
        
        // Kullanıcının kazandığı badge'ler
        $unlockedBadges = $user->badges()->get();
        
        // Henüz kazanılmamış badge'ler
        $lockedBadges = \App\Models\Badge::whereNotIn('id', $unlockedBadges->pluck('id'))
            ->orderBy('xp_required')
            ->get();

        return view('xp.badges', compact('unlockedBadges', 'lockedBadges'));
    }
}
