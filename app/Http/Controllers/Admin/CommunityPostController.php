<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use Illuminate\Http\Request;

class CommunityPostController extends Controller
{
    /**
     * Display a listing of community posts
     */
    public function index(Request $request)
    {
        // İstatistikler
        $stats = [
            'total' => CommunityPost::count(),
            'featured' => CommunityPost::where('is_featured', true)->count(),
            'today' => CommunityPost::whereDate('created_at', today())->count(),
            'this_week' => CommunityPost::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => CommunityPost::whereMonth('created_at', now()->month)->count(),
            'total_likes' => CommunityPost::sum('likes_count'),
            'avg_likes' => round(CommunityPost::avg('likes_count'), 1),
            'total_comments' => \App\Models\Comment::where('commentable_type', 'App\Models\CommunityPost')->count(),
            'intro_posts' => CommunityPost::where('type', 'intro')->count(),
            'general_posts' => CommunityPost::where('type', 'general')->count(),
        ];

        $query = CommunityPost::with(['user.profile']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
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

        return view('admin.community-posts.index', compact('posts', 'stats'));
    }

    /**
     * Pin/unpin post
     */
    public function togglePin($id)
    {
        $post = CommunityPost::findOrFail($id);
        
        // is_pinned alanı yoksa ekleyelim (migration gerekebilir)
        // Şimdilik is_featured kullanıyoruz
        $post->update(['is_featured' => !$post->is_featured]);

        $status = $post->is_featured ? 'sabitlendi' : 'sabitleme kaldırıldı';

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'toggle_pin_community_post',
            'target_type' => 'CommunityPost',
            'target_id' => $id,
            'meta' => json_encode([
                'user' => $post->user->name,
                'is_pinned' => $post->is_featured
            ]),
        ]);

        return back()->with('success', "Gönderi {$status}.");
    }

    /**
     * Get statistics for API (AJAX)
     */
    public function getStatistics(Request $request)
    {
        $days = $request->get('days', 30);
        
        // Günlük gönderi oluşturma trendi
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'count' => CommunityPost::whereDate('created_at', $date)->count(),
                'intro' => CommunityPost::whereDate('created_at', $date)->where('type', 'intro')->count(),
                'general' => CommunityPost::whereDate('created_at', $date)->where('type', 'general')->count(),
            ];
        }

        // Tip dağılımı
        $typeDistribution = [
            'intro' => CommunityPost::where('type', 'intro')->count(),
            'general' => CommunityPost::where('type', 'general')->count(),
        ];

        // En popüler gönderiler (top 10)
        $topPosts = CommunityPost::with(['user'])
            ->orderByDesc('likes_count')
            ->limit(10)
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'content' => \Str::limit($post->content, 50),
                    'author' => $post->user->name,
                    'likes' => $post->likes_count,
                    'type' => $post->type,
                ];
            });

        return response()->json([
            'trend' => $trend,
            'type_distribution' => $typeDistribution,
            'top_posts' => $topPosts,
        ]);
    }

    /**
     * Display the specified community post
     */
    public function show($id)
    {
        $post = CommunityPost::with(['user.profile', 'comments.user', 'reports'])
            ->findOrFail($id);

        return view('admin.community-posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified community post
     */
    public function edit($id)
    {
        $post = CommunityPost::with(['user.profile'])->findOrFail($id);

        return view('admin.community-posts.edit', compact('post'));
    }

    /**
     * Update the specified community post
     */
    public function update(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string',
            'type' => 'required|in:intro,general',
        ]);

        $post->update($validated);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_community_post',
            'target_type' => 'CommunityPost',
            'target_id' => $id,
            'meta' => json_encode(['user' => $post->user->name]),
        ]);

        return redirect()->route('admin.community-posts.index')
            ->with('success', 'Topluluk gönderisi başarıyla güncellendi.');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $post = CommunityPost::findOrFail($id);
        $post->update(['is_featured' => !$post->is_featured]);

        $status = $post->is_featured ? 'öne çıkarıldı' : 'öne çıkarmadan kaldırıldı';

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'toggle_featured_community_post',
            'target_type' => 'CommunityPost',
            'target_id' => $id,
            'meta' => json_encode([
                'user' => $post->user->name,
                'is_featured' => $post->is_featured
            ]),
        ]);

        return back()->with('success', "Gönderi {$status}.");
    }

    /**
     * Remove the specified community post
     */
    public function destroy($id)
    {
        $post = CommunityPost::findOrFail($id);
        $userName = $post->user->name;
        
        $post->delete();

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_community_post',
            'target_type' => 'CommunityPost',
            'target_id' => $id,
            'meta' => json_encode(['user' => $userName]),
        ]);

        return redirect()->route('admin.community-posts.index')
            ->with('success', 'Gönderi silindi.');
    }

    /**
     * Bulk feature selected posts
     */
    public function bulkFeature(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Hiçbir gönderi seçilmedi.');
        }

        CommunityPost::whereIn('id', $ids)->update(['is_featured' => true]);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'bulk_feature_community_posts',
            'target_type' => 'CommunityPost',
            'meta' => json_encode(['count' => count($ids), 'ids' => $ids]),
        ]);

        return back()->with('success', count($ids) . ' gönderi öne çıkarıldı.');
    }

    /**
     * Bulk unfeature selected posts
     */
    public function bulkUnfeature(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Hiçbir gönderi seçilmedi.');
        }

        CommunityPost::whereIn('id', $ids)->update(['is_featured' => false]);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'bulk_unfeature_community_posts',
            'target_type' => 'CommunityPost',
            'meta' => json_encode(['count' => count($ids), 'ids' => $ids]),
        ]);

        return back()->with('success', count($ids) . ' gönderi öne çıkarmadan kaldırıldı.');
    }

    /**
     * Bulk delete selected posts
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Hiçbir gönderi seçilmedi.');
        }

        CommunityPost::whereIn('id', $ids)->delete();

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'bulk_delete_community_posts',
            'target_type' => 'CommunityPost',
            'meta' => json_encode(['count' => count($ids), 'ids' => $ids]),
        ]);

        return back()->with('success', count($ids) . ' gönderi silindi.');
    }
}
