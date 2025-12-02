<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

/**
 * CommentController
 * 
 * Admin panelinde yorum yönetimi
 * - Tüm yorumları listeleme ve filtreleme
 * - Toplu silme işlemleri
 * - Spam işaretleme
 */
class CommentController extends Controller
{
    /**
     * Tüm yorumları listele
     * Filtreleme ve arama desteği ile
     */
    public function index(Request $request)
    {
        // İstatistikler
        $stats = [
            'total' => Comment::count(),
            'today' => Comment::whereDate('created_at', today())->count(),
            'this_week' => Comment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Comment::whereMonth('created_at', now()->month)->count(),
            'by_type' => [
                'lfg' => Comment::where('commentable_type', 'App\Models\LfgPost')->count(),
                'clan' => Comment::where('commentable_type', 'App\Models\Clan')->count(),
                'guide' => Comment::where('commentable_type', 'App\Models\GuidePost')->count(),
                'community' => Comment::where('commentable_type', 'App\Models\CommunityPost')->count(),
            ],
            'with_replies' => Comment::whereNotNull('parent_id')->count(),
            'top_level' => Comment::whereNull('parent_id')->count(),
        ];

        $query = Comment::with(['user.profile', 'commentable']);

        // Arama filtresi
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

        // İçerik türü filtresi
        if ($request->filled('type')) {
            $typeMap = [
                'lfg' => 'App\Models\LfgPost',
                'clan' => 'App\Models\Clan',
                'guide' => 'App\Models\GuidePost',
                'community' => 'App\Models\CommunityPost',
            ];
            
            if (isset($typeMap[$request->type])) {
                $query->where('commentable_type', $typeMap[$request->type]);
            }
        }

        // Yorum tipi filtresi (ana yorum / cevap)
        if ($request->filled('comment_type')) {
            if ($request->comment_type === 'parent') {
                $query->whereNull('parent_id');
            } elseif ($request->comment_type === 'reply') {
                $query->whereNotNull('parent_id');
            }
        }

        // Tarih filtreleri
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

        $comments = $query->paginate(20);

        return view('admin.comments.index', compact('comments', 'stats'));
    }

    /**
     * Yorum detayını göster
     */
    public function show($id)
    {
        $comment = Comment::with([
            'user.profile',
            'commentable',
            'parent.user',
            'replies.user',
        ])->findOrFail($id);

        return view('admin.comments.show', compact('comment'));
    }

    /**
     * Yorumu düzenle
     */
    public function edit($id)
    {
        $comment = Comment::with(['user.profile', 'commentable'])->findOrFail($id);

        return view('admin.comments.edit', compact('comment'));
    }

    /**
     * Yorumu güncelle
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update($validated);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_comment',
            'target_type' => 'Comment',
            'target_id' => $id,
            'meta' => json_encode([
                'user' => $comment->user->name,
                'content_preview' => \Str::limit($comment->content, 50),
            ]),
        ]);

        return redirect()->route('admin.comments.index')
            ->with('success', 'Yorum başarıyla güncellendi.');
    }

    /**
     * Yorumu sil
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $userName = $comment->user->name;
        $contentPreview = \Str::limit($comment->content, 50);
        
        // Cevapları da sil (cascade)
        $comment->replies()->delete();
        $comment->delete();

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_comment',
            'target_type' => 'Comment',
            'target_id' => $id,
            'meta' => json_encode([
                'user' => $userName,
                'content_preview' => $contentPreview,
            ]),
        ]);

        return redirect()->route('admin.comments.index')
            ->with('success', 'Yorum silindi.');
    }

    /**
     * Toplu silme işlemi
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Hiçbir yorum seçilmedi.');
        }

        // Seçilen yorumların cevaplarını da sil
        Comment::whereIn('parent_id', $ids)->delete();
        
        // Ana yorumları sil
        Comment::whereIn('id', $ids)->delete();

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'bulk_delete_comments',
            'target_type' => 'Comment',
            'meta' => json_encode([
                'count' => count($ids),
                'ids' => $ids,
            ]),
        ]);

        return back()->with('success', count($ids) . ' yorum silindi.');
    }

    /**
     * Spam işaretleme
     * Not: Şu an için spam alanı yok, gelecekte eklenebilir
     * Şimdilik yorumu siliyoruz
     */
    public function markAsSpam($id)
    {
        $comment = Comment::findOrFail($id);
        
        // Spam olarak işaretle ve sil
        // Gelecekte is_spam alanı eklenebilir
        $userName = $comment->user->name;
        $contentPreview = \Str::limit($comment->content, 50);
        
        $comment->delete();

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'mark_comment_as_spam',
            'target_type' => 'Comment',
            'target_id' => $id,
            'meta' => json_encode([
                'user' => $userName,
                'content_preview' => $contentPreview,
            ]),
        ]);

        return back()->with('success', 'Yorum spam olarak işaretlendi ve silindi.');
    }

    /**
     * Toplu spam işaretleme
     */
    public function bulkMarkAsSpam(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Hiçbir yorum seçilmedi.');
        }

        // Spam olarak işaretle ve sil
        Comment::whereIn('id', $ids)->delete();

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'bulk_mark_comments_as_spam',
            'target_type' => 'Comment',
            'meta' => json_encode([
                'count' => count($ids),
                'ids' => $ids,
            ]),
        ]);

        return back()->with('success', count($ids) . ' yorum spam olarak işaretlendi ve silindi.');
    }

    /**
     * Yorum istatistikleri (AJAX için)
     */
    public function getStatistics(Request $request)
    {
        $days = $request->get('days', 30);
        
        // Günlük yorum oluşturma trendi
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'count' => Comment::whereDate('created_at', $date)->count(),
                'lfg' => Comment::whereDate('created_at', $date)
                    ->where('commentable_type', 'App\Models\LfgPost')->count(),
                'guide' => Comment::whereDate('created_at', $date)
                    ->where('commentable_type', 'App\Models\GuidePost')->count(),
                'community' => Comment::whereDate('created_at', $date)
                    ->where('commentable_type', 'App\Models\CommunityPost')->count(),
            ];
        }

        // İçerik türü dağılımı
        $typeDistribution = [
            'lfg' => Comment::where('commentable_type', 'App\Models\LfgPost')->count(),
            'clan' => Comment::where('commentable_type', 'App\Models\Clan')->count(),
            'guide' => Comment::where('commentable_type', 'App\Models\GuidePost')->count(),
            'community' => Comment::where('commentable_type', 'App\Models\CommunityPost')->count(),
        ];

        // En aktif yorumcular (top 10)
        $topCommenters = Comment::selectRaw('user_id, COUNT(*) as comment_count')
            ->groupBy('user_id')
            ->orderByDesc('comment_count')
            ->limit(10)
            ->with('user')
            ->get()
            ->map(function($item) {
                return [
                    'user_id' => $item->user_id,
                    'user_name' => $item->user->name ?? 'Bilinmeyen',
                    'comment_count' => $item->comment_count,
                ];
            });

        return response()->json([
            'trend' => $trend,
            'type_distribution' => $typeDistribution,
            'top_commenters' => $topCommenters,
        ]);
    }
}
