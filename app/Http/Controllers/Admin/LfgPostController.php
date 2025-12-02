<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LfgPost;
use App\Models\Game;
use Illuminate\Http\Request;

class LfgPostController extends Controller
{
    /**
     * Display a listing of LFG posts
     */
    public function index(Request $request)
    {
        // İstatistikler
        $stats = [
            'total' => LfgPost::count(),
            'open' => LfgPost::where('status', 'open')->count(),
            'closed' => LfgPost::where('status', 'closed')->count(),
            'featured' => LfgPost::where('is_featured', true)->count(),
            'today' => LfgPost::whereDate('created_at', today())->count(),
            'this_week' => LfgPost::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => LfgPost::whereMonth('created_at', now()->month)->count(),
            'total_views' => LfgPost::sum('views_count'),
            'avg_views' => LfgPost::avg('views_count'),
            'total_applications' => \App\Models\LfgApplication::count(),
        ];

        $query = LfgPost::with(['user.profile', 'game']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('play_style_tag')) {
            $query->where('play_style_tag', $request->play_style_tag);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->is_featured === 'yes');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $posts = $query->paginate(20);

        // Get filter options
        $games = Game::where('is_active', true)->get();
        $cities = LfgPost::distinct()->pluck('city')->filter()->sort()->values();
        $playStyles = LfgPost::distinct()->pluck('play_style_tag')->filter()->sort()->values();

        return view('admin.lfg-posts.index', compact('posts', 'games', 'cities', 'playStyles', 'stats'));
    }

    /**
     * Get statistics for API (AJAX)
     */
    public function getStatistics(Request $request)
    {
        $days = $request->get('days', 30);
        
        // Günlük ilan oluşturma trendi
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'count' => LfgPost::whereDate('created_at', $date)->count(),
                'open' => LfgPost::whereDate('created_at', $date)->where('status', 'open')->count(),
                'closed' => LfgPost::whereDate('created_at', $date)->where('status', 'closed')->count(),
            ];
        }

        // Oyun dağılımı
        $gameDistribution = LfgPost::select('game_id', \DB::raw('count(*) as count'))
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
        $cityDistribution = LfgPost::select('city', \DB::raw('count(*) as count'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Oyun stili dağılımı
        $playStyleDistribution = LfgPost::select('play_style_tag', \DB::raw('count(*) as count'))
            ->whereNotNull('play_style_tag')
            ->groupBy('play_style_tag')
            ->orderByDesc('count')
            ->get();

        return response()->json([
            'trend' => $trend,
            'game_distribution' => $gameDistribution,
            'city_distribution' => $cityDistribution,
            'play_style_distribution' => $playStyleDistribution,
        ]);
    }

    /**
     * Display the specified LFG post
     */
    public function show($id)
    {
        $post = LfgPost::with(['user.profile', 'game', 'applications.user.profile', 'reports'])
            ->findOrFail($id);

        return view('admin.lfg-posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified LFG post
     */
    public function edit($id)
    {
        $post = LfgPost::with(['user.profile', 'game'])->findOrFail($id);
        $games = Game::where('is_active', true)->get();

        return view('admin.lfg-posts.edit', compact('post', 'games'));
    }

    /**
     * Update the specified LFG post
     */
    public function update(Request $request, $id)
    {
        $post = LfgPost::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'game_id' => 'required|exists:games,id',
            'min_rank' => 'nullable|string|max:255',
            'max_rank' => 'nullable|string|max:255',
            'mode' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'play_style_tag' => 'nullable|string|max:255',
            'microphone_required' => 'boolean',
            'status' => 'required|in:open,closed',
            'admin_notes' => 'nullable|string',
        ]);

        $post->update($validated);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_lfg_post',
            'target_type' => 'LfgPost',
            'target_id' => $id,
            'meta' => json_encode(['title' => $post->title]),
        ]);

        return redirect()->route('admin.lfg-posts.index')
            ->with('success', 'İlan başarıyla güncellendi.');
    }

    /**
     * Close the specified LFG post
     */
    public function close($id)
    {
        $post = LfgPost::findOrFail($id);
        $post->update(['status' => 'closed']);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'close_lfg_post',
            'target_type' => 'LfgPost',
            'target_id' => $id,
            'meta' => json_encode(['title' => $post->title]),
        ]);

        return back()->with('success', 'İlan kapatıldı.');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $post = LfgPost::findOrFail($id);
        $post->update(['is_featured' => !$post->is_featured]);

        $status = $post->is_featured ? 'öne çıkarıldı' : 'öne çıkarmadan kaldırıldı';

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'toggle_featured_lfg',
            'target_type' => 'LfgPost',
            'target_id' => $id,
            'meta' => json_encode([
                'title' => $post->title,
                'is_featured' => $post->is_featured
            ]),
        ]);

        return back()->with('success', "İlan {$status}.");
    }

    /**
     * Remove the specified LFG post
     */
    public function destroy($id)
    {
        $post = LfgPost::findOrFail($id);
        $title = $post->title;
        
        $post->delete();

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_lfg_post',
            'target_type' => 'LfgPost',
            'target_id' => $id,
            'meta' => json_encode(['title' => $title]),
        ]);

        return redirect()->route('admin.lfg-posts.index')
            ->with('success', 'İlan silindi.');
    }

    /**
     * Bulk close selected posts
     */
    public function bulkClose(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Hiçbir ilan seçilmedi.');
        }

        LfgPost::whereIn('id', $ids)->update(['status' => 'closed']);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'bulk_close_lfg_posts',
            'target_type' => 'LfgPost',
            'meta' => json_encode(['count' => count($ids), 'ids' => $ids]),
        ]);

        return back()->with('success', count($ids) . ' ilan kapatıldı.');
    }

    /**
     * Bulk delete selected posts
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Hiçbir ilan seçilmedi.');
        }

        LfgPost::whereIn('id', $ids)->delete();

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'bulk_delete_lfg_posts',
            'target_type' => 'LfgPost',
            'meta' => json_encode(['count' => count($ids), 'ids' => $ids]),
        ]);

        return back()->with('success', count($ids) . ' ilan silindi.');
    }
}
