<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Ana Sayfa Controller
 */
class HomeController extends Controller
{
    /**
     * Ana sayfa
     * GET /
     */
    public function index()
    {
        try {
            // Cache ile istatistikleri 5 dakika boyunca sakla
            $stats = cache()->remember('home_stats', 300, function () {
                return [
                    'total_users' => User::count(),
                    'active_users' => User::where('status', 'active')->count(),
                    'active_lfg' => \App\Models\LfgPost::where('status', 'open')->count(),
                    'total_clans' => \App\Models\Clan::count(),
                ];
            });

            // Son ilanları cache'le (1 dakika)
            $latest_lfg = cache()->remember('home_latest_lfg', 60, function () {
                return \App\Models\LfgPost::with('user:id,name')
                    ->where('status', 'open')
                    ->latest()
                    ->take(5)
                    ->get();
            });

            // Yeni klanları cache'le (1 dakika)
            $latest_clans = cache()->remember('home_latest_clans', 60, function () {
                return \App\Models\Clan::withCount('members')
                    ->latest()
                    ->take(5)
                    ->get();
            });

            // Top kullanıcıları cache'le (5 dakika)
            $top_users = cache()->remember('home_top_users', 300, function () {
                return User::select('id', 'name', 'xp_total')
                    ->withCount(['lfgPosts', 'badges'])
                    ->orderBy('xp_total', 'desc')
                    ->take(3)
                    ->get()
                    ->map(function ($user) {
                        $user->xp = $user->xp_total;
                        $user->level = $user->getLevel();
                        $user->badges_count = $user->badges_count;
                        $user->posts_count = $user->lfg_posts_count;
                        return $user;
                    });
            });

            return view('welcome', compact('stats', 'latest_lfg', 'latest_clans', 'top_users'));
        } catch (\Exception $e) {
            return response($e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine(), 500);
        }
    }
}
