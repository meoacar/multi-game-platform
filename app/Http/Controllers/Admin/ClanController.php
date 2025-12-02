<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clan;
use App\Models\ClanApplication;
use App\Models\Game;
use Illuminate\Http\Request;

class ClanController extends Controller
{
    /**
     * Display a listing of clans
     */
    public function index(Request $request)
    {
        // İstatistikler
        $stats = [
            'total' => Clan::count(),
            'verified' => Clan::where('is_verified', true)->count(),
            'unverified' => Clan::where('is_verified', false)->count(),
            'today' => Clan::whereDate('created_at', today())->count(),
            'this_week' => Clan::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Clan::whereMonth('created_at', now()->month)->count(),
            'total_members' => Clan::sum('member_count'),
            'avg_members' => round(Clan::avg('member_count'), 1),
            'pending_applications' => \App\Models\ClanApplication::where('status', 'pending')->count(),
            'total_applications' => \App\Models\ClanApplication::count(),
        ];

        $query = Clan::with(['leader.profile', 'game']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('leader', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified === 'yes');
        }

        if ($request->filled('min_rank')) {
            $query->where('min_rank', $request->min_rank);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $clans = $query->paginate(20);

        // Get filter options
        $games = Game::where('is_active', true)->get();
        $cities = Clan::distinct()->pluck('city')->filter()->sort()->values();
        $ranks = Clan::distinct()->pluck('min_rank')->filter()->sort()->values();

        return view('admin.clans.index', compact('clans', 'games', 'cities', 'ranks', 'stats'));
    }

    /**
     * Get statistics for API (AJAX)
     */
    public function getStatistics(Request $request)
    {
        $days = $request->get('days', 30);
        
        // Günlük klan oluşturma trendi
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'count' => Clan::whereDate('created_at', $date)->count(),
                'verified' => Clan::whereDate('created_at', $date)->where('is_verified', true)->count(),
            ];
        }

        // Oyun dağılımı
        $gameDistribution = Clan::select('game_id', \DB::raw('count(*) as count'))
            ->with('game:id,name')
            ->groupBy('game_id')
            ->get()
            ->map(function($item) {
                return [
                    'game' => $item->game->name ?? 'Bilinmeyen',
                    'count' => $item->count,
                ];
            });

        // Şehir dağılımı (top 10)
        $cityDistribution = Clan::select('city', \DB::raw('count(*) as count'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Üye sayısı dağılımı
        $memberDistribution = [
            '1-5' => Clan::whereBetween('member_count', [1, 5])->count(),
            '6-10' => Clan::whereBetween('member_count', [6, 10])->count(),
            '11-20' => Clan::whereBetween('member_count', [11, 20])->count(),
            '21-50' => Clan::whereBetween('member_count', [21, 50])->count(),
            '50+' => Clan::where('member_count', '>', 50)->count(),
        ];

        return response()->json([
            'trend' => $trend,
            'game_distribution' => $gameDistribution,
            'city_distribution' => $cityDistribution,
            'member_distribution' => $memberDistribution,
        ]);
    }

    /**
     * Display the specified clan
     */
    public function show($id)
    {
        $clan = Clan::with(['leader.profile', 'game', 'applications.user.profile', 'members.profile', 'reports'])
            ->findOrFail($id);

        return view('admin.clans.show', compact('clan'));
    }

    /**
     * Show the form for editing the specified clan
     */
    public function edit($id)
    {
        $clan = Clan::with(['leader.profile', 'game'])->findOrFail($id);
        $games = Game::where('is_active', true)->get();

        return view('admin.clans.edit', compact('clan', 'games'));
    }

    /**
     * Update the specified clan
     */
    public function update(Request $request, $id)
    {
        $clan = Clan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'game_id' => 'required|exists:games,id',
            'min_rank' => 'nullable|string|max:255',
            'max_rank' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'min_age_range' => 'nullable|string|max:255',
            'max_age_range' => 'nullable|string|max:255',
            'max_members' => 'nullable|integer|min:1',
            'discord_invite' => 'nullable|string|max:255',
        ]);

        $clan->update($validated);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_clan',
            'target_type' => 'Clan',
            'target_id' => $id,
            'meta' => json_encode(['name' => $clan->name]),
        ]);

        return redirect()->route('admin.clans.index')
            ->with('success', 'Klan başarıyla güncellendi.');
    }

    /**
     * Toggle verified status
     */
    public function toggleVerified($id)
    {
        $clan = Clan::findOrFail($id);
        $clan->update(['is_verified' => !$clan->is_verified]);

        $status = $clan->is_verified ? 'doğrulandı' : 'doğrulaması kaldırıldı';

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'toggle_verified_clan',
            'target_type' => 'Clan',
            'target_id' => $id,
            'meta' => json_encode([
                'name' => $clan->name,
                'is_verified' => $clan->is_verified
            ]),
        ]);

        return back()->with('success', "Klan {$status}.");
    }

    /**
     * Remove the specified clan
     */
    public function destroy($id)
    {
        $clan = Clan::findOrFail($id);
        $name = $clan->name;
        
        $clan->delete();

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_clan',
            'target_type' => 'Clan',
            'target_id' => $id,
            'meta' => json_encode(['name' => $name]),
        ]);

        return redirect()->route('admin.clans.index')
            ->with('success', 'Klan silindi.');
    }

    /**
     * Display clan applications
     */
    public function applications(Request $request)
    {
        $query = ClanApplication::with(['user.profile', 'clan.leader']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('clan_id')) {
            $query->where('clan_id', $request->clan_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('clan', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        $applications = $query->latest()->paginate(20);

        // Get filter options
        $clans = Clan::orderBy('name')->get();

        return view('admin.clans.applications', compact('applications', 'clans'));
    }

    /**
     * Override application status
     */
    public function updateApplicationStatus(Request $request, $id)
    {
        $application = ClanApplication::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $application->update($validated);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_clan_application_status',
            'target_type' => 'ClanApplication',
            'target_id' => $id,
            'meta' => json_encode([
                'clan' => $application->clan->name,
                'user' => $application->user->name,
                'status' => $validated['status']
            ]),
        ]);

        return back()->with('success', 'Başvuru durumu güncellendi.');
    }

    /**
     * Remove member from clan
     */
    public function removeMember(Request $request, $id)
    {
        $clan = Clan::findOrFail($id);
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $clan->members()->detach($validated['user_id']);

        // Update member count
        $clan->update(['member_count' => $clan->members()->count() + 1]); // +1 for leader

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'remove_clan_member',
            'target_type' => 'Clan',
            'target_id' => $id,
            'meta' => json_encode([
                'clan' => $clan->name,
                'user_id' => $validated['user_id']
            ]),
        ]);

        return back()->with('success', 'Üye klandan çıkarıldı.');
    }
}
