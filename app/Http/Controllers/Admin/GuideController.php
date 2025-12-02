<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuidePost;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    /**
     * Rehberler listesi sayfası
     */
    public function index(Request $request)
    {
        // İstatistikler
        $stats = [
            'total' => GuidePost::count(),
            'published' => GuidePost::where('is_published', true)->count(),
            'draft' => GuidePost::where('is_published', false)->count(),
            'featured' => GuidePost::where('is_featured', true)->count(),
            'today' => GuidePost::whereDate('created_at', today())->count(),
            'this_week' => GuidePost::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => GuidePost::whereMonth('created_at', now()->month)->count(),
            'total_views' => GuidePost::sum('views_count'),
            'total_likes' => GuidePost::sum('likes_count'),
            'avg_views' => GuidePost::avg('views_count'),
            'total_comments' => \App\Models\Comment::where('commentable_type', 'App\Models\GuidePost')->count(),
        ];

        $query = GuidePost::with(['user', 'game'])
            ->withCount(['comments']);

        // Gelişmiş Filtreleme
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        if ($request->filled('is_published')) {
            $query->where('is_published', $request->is_published === 'yes');
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

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $guides = $query->paginate(12);

        // Filtre seçenekleri
        $games = \App\Models\Game::where('is_active', true)->get();

        return view('admin.guides.index', compact('guides', 'stats', 'games'));
    }

    /**
     * Get statistics for API (AJAX)
     */
    public function getStatistics(Request $request)
    {
        $days = $request->get('days', 30);
        
        // Günlük rehber oluşturma trendi
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'count' => GuidePost::whereDate('created_at', $date)->count(),
                'published' => GuidePost::whereDate('created_at', $date)->where('is_published', true)->count(),
                'views' => GuidePost::whereDate('created_at', $date)->sum('views_count'),
            ];
        }

        // Oyun dağılımı
        $gameDistribution = GuidePost::select('game_id', \DB::raw('count(*) as count'))
            ->with('game:id,name')
            ->groupBy('game_id')
            ->get()
            ->map(function($item) {
                return [
                    'game' => $item->game->name ?? 'Bilinmeyen',
                    'count' => $item->count,
                ];
            });

        // En popüler rehberler (top 10)
        $topGuides = GuidePost::with(['user', 'game'])
            ->orderByDesc('views_count')
            ->limit(10)
            ->get()
            ->map(function($guide) {
                return [
                    'id' => $guide->id,
                    'title' => $guide->title,
                    'author' => $guide->user->name,
                    'views' => $guide->views_count,
                    'likes' => $guide->likes_count,
                ];
            });

        return response()->json([
            'trend' => $trend,
            'game_distribution' => $gameDistribution,
            'top_guides' => $topGuides,
        ]);
    }

    /**
     * Rehber detay sayfası
     */
    public function show($id)
    {
        $guide = GuidePost::with(['user', 'game', 'comments'])
            ->withCount(['comments'])
            ->findOrFail($id);

        return view('admin.guides.show', compact('guide'));
    }

    /**
     * Rehber düzenleme sayfası
     */
    public function edit($id)
    {
        $guide = GuidePost::findOrFail($id);
        return view('admin.guides.edit', compact('guide'));
    }

    /**
     * Rehber güncelleme
     */
    public function update(Request $request, $id)
    {
        $guide = GuidePost::findOrFail($id);
        $guide->update($request->all());

        return redirect()->route('admin.guides.index')
            ->with('success', 'Rehber başarıyla güncellendi!');
    }

    /**
     * Yayın durumunu değiştir
     */
    public function togglePublished($id)
    {
        $guide = GuidePost::findOrFail($id);
        $guide->is_published = !$guide->is_published;
        $guide->save();

        return back()->with('success', 'Rehber durumu güncellendi!');
    }

    /**
     * Öne çıkan durumunu değiştir
     */
    public function toggleFeatured($id)
    {
        $guide = GuidePost::findOrFail($id);
        $guide->is_featured = !$guide->is_featured;
        $guide->save();

        return back()->with('success', 'Rehber öne çıkan durumu güncellendi!');
    }

    /**
     * Rehber silme
     */
    public function destroy($id)
    {
        $guide = GuidePost::findOrFail($id);
        $guide->delete();

        return back()->with('success', 'Rehber başarıyla silindi!');
    }

    /**
     * Toplu yayınlama
     */
    public function bulkPublish(Request $request)
    {
        GuidePost::whereIn('id', $request->ids)->update(['is_published' => true]);
        return response()->json(['success' => true]);
    }

    /**
     * Toplu yayından kaldırma
     */
    public function bulkUnpublish(Request $request)
    {
        GuidePost::whereIn('id', $request->ids)->update(['is_published' => false]);
        return response()->json(['success' => true]);
    }

    /**
     * Toplu silme
     */
    public function bulkDelete(Request $request)
    {
        GuidePost::whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true]);
    }
}
