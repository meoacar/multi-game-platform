<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LfgPost;
use App\Models\Clan;
use App\Models\GuidePost;
use App\Models\CommunityPost;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ContentController
 * 
 * Genel içerik yönetimi controller'ı
 * Tüm içerik türlerini (LFG, Klan, Rehber, Topluluk Gönderisi, Yorum) 
 * tek bir yerden görüntüleme ve yönetme imkanı sağlar
 */
class ContentController extends Controller
{
    /**
     * İçerik yönetimi ana sayfası
     * Tüm içerik türlerinin özet istatistiklerini gösterir
     */
    public function index(Request $request)
    {
        // Genel istatistikler
        $stats = [
            'lfg_posts' => [
                'total' => LfgPost::count(),
                'open' => LfgPost::where('status', 'open')->count(),
                'closed' => LfgPost::where('status', 'closed')->count(),
                'featured' => LfgPost::where('is_featured', true)->count(),
                'today' => LfgPost::whereDate('created_at', today())->count(),
            ],
            'clans' => [
                'total' => Clan::count(),
                'verified' => Clan::where('is_verified', true)->count(),
                'unverified' => Clan::where('is_verified', false)->count(),
                'today' => Clan::whereDate('created_at', today())->count(),
            ],
            'guides' => [
                'total' => GuidePost::count(),
                'published' => GuidePost::where('is_published', true)->count(),
                'draft' => GuidePost::where('is_published', false)->count(),
                'featured' => GuidePost::where('is_featured', true)->count(),
                'today' => GuidePost::whereDate('created_at', today())->count(),
            ],
            'community_posts' => [
                'total' => CommunityPost::count(),
                'featured' => CommunityPost::where('is_featured', true)->count(),
                'today' => CommunityPost::whereDate('created_at', today())->count(),
            ],
            'comments' => [
                'total' => Comment::count(),
                'today' => Comment::whereDate('created_at', today())->count(),
            ],
            'reports' => [
                'total' => Report::count(),
                'pending' => Report::where('status', 'pending')->count(),
                'resolved' => Report::where('status', 'resolved')->count(),
                'rejected' => Report::where('status', 'rejected')->count(),
            ],
        ];

        // Son içerikler (tüm türlerden)
        $recentContent = $this->getRecentContent(10);

        // Popüler içerikler (görüntülenme bazlı)
        $popularContent = $this->getPopularContent(10);

        // Raporlanan içerikler
        $reportedContent = $this->getReportedContent(10);

        return view('admin.content.index', compact(
            'stats',
            'recentContent',
            'popularContent',
            'reportedContent'
        ));
    }

    /**
     * Tüm içerikleri birleşik listele
     * Filtreleme ve arama desteği ile
     */
    public function all(Request $request)
    {
        $type = $request->get('type', 'all');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $contents = collect();

        // İçerik türüne göre filtreleme
        // Eager loading ve select ile N+1 problemini önle
        if ($type === 'all' || $type === 'lfg') {
            $lfgPosts = LfgPost::with([
                    'user:id,name,email',
                    'user.profile:id,user_id,avatar_path',
                    'game:id,name'
                ])
                ->select('id', 'user_id', 'game_id', 'title', 'status', 'is_featured', 'views_count', 'created_at')
                ->when($search, function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                })
                ->when($dateFrom, function($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'lfg',
                        'type_label' => 'LFG İlanı',
                        'title' => $item->title,
                        'user' => $item->user,
                        'status' => $item->status,
                        'is_featured' => $item->is_featured,
                        'created_at' => $item->created_at,
                        'views_count' => $item->views_count ?? 0,
                        'model' => $item,
                    ];
                });
            $contents = $contents->merge($lfgPosts);
        }

        if ($type === 'all' || $type === 'clan') {
            $clans = Clan::with([
                    'leader:id,name,email',
                    'leader.profile:id,user_id,avatar_path',
                    'game:id,name'
                ])
                ->select('id', 'leader_id', 'game_id', 'name', 'is_verified', 'created_at')
                ->when($search, function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                })
                ->when($dateFrom, function($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'clan',
                        'type_label' => 'Klan',
                        'title' => $item->name,
                        'user' => $item->leader,
                        'status' => $item->is_verified ? 'verified' : 'unverified',
                        'is_featured' => false,
                        'created_at' => $item->created_at,
                        'views_count' => 0,
                        'model' => $item,
                    ];
                });
            $contents = $contents->merge($clans);
        }

        if ($type === 'all' || $type === 'guide') {
            $guides = GuidePost::with([
                    'user:id,name,email',
                    'game:id,name'
                ])
                ->select('id', 'user_id', 'game_id', 'title', 'is_published', 'is_featured', 'views_count', 'created_at')
                ->when($search, function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                })
                ->when($dateFrom, function($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'guide',
                        'type_label' => 'Rehber',
                        'title' => $item->title,
                        'user' => $item->user,
                        'status' => $item->is_published ? 'published' : 'draft',
                        'is_featured' => $item->is_featured,
                        'created_at' => $item->created_at,
                        'views_count' => $item->views_count ?? 0,
                        'model' => $item,
                    ];
                });
            $contents = $contents->merge($guides);
        }

        if ($type === 'all' || $type === 'community') {
            $communityPosts = CommunityPost::with([
                    'user:id,name,email',
                    'user.profile:id,user_id,avatar_path'
                ])
                ->select('id', 'user_id', 'content', 'is_featured', 'created_at')
                ->when($search, function($q) use ($search) {
                    $q->where('content', 'like', "%{$search}%");
                })
                ->when($dateFrom, function($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'community',
                        'type_label' => 'Topluluk Gönderisi',
                        'title' => \Str::limit($item->content, 50),
                        'user' => $item->user,
                        'status' => 'active',
                        'is_featured' => $item->is_featured,
                        'created_at' => $item->created_at,
                        'views_count' => 0,
                        'model' => $item,
                    ];
                });
            $contents = $contents->merge($communityPosts);
        }

        // Sıralama
        $contents = $contents->sortBy([
            [$sortBy, $sortOrder === 'desc' ? 'desc' : 'asc']
        ]);

        // Pagination için manuel olarak slice kullanıyoruz
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $total = $contents->count();
        $contents = $contents->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return view('admin.content.all', compact('contents', 'total', 'currentPage', 'perPage'));
    }

    /**
     * Son eklenen içerikleri getir (tüm türlerden)
     * Eager loading ile N+1 problemini önle
     */
    private function getRecentContent($limit = 10)
    {
        $contents = collect();

        // LFG İlanları - Sadece gerekli kolonları seç
        $lfgPosts = LfgPost::with([
                'user:id,name,email',
                'user.profile:id,user_id,avatar_path',
                'game:id,name'
            ])
            ->select('id', 'user_id', 'game_id', 'title', 'created_at')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'lfg',
                    'type_label' => 'LFG İlanı',
                    'title' => $item->title,
                    'user' => $item->user,
                    'created_at' => $item->created_at,
                    'url' => route('admin.lfg-posts.show', $item->id),
                ];
            });

        // Klanlar - Sadece gerekli kolonları seç
        $clans = Clan::with([
                'leader:id,name,email',
                'leader.profile:id,user_id,avatar_path',
                'game:id,name'
            ])
            ->select('id', 'leader_id', 'game_id', 'name', 'created_at')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'clan',
                    'type_label' => 'Klan',
                    'title' => $item->name,
                    'user' => $item->leader,
                    'created_at' => $item->created_at,
                    'url' => route('admin.clans.show', $item->id),
                ];
            });

        // Rehberler - Sadece gerekli kolonları seç
        $guides = GuidePost::with([
                'user:id,name,email',
                'game:id,name'
            ])
            ->select('id', 'user_id', 'game_id', 'title', 'created_at')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'guide',
                    'type_label' => 'Rehber',
                    'title' => $item->title,
                    'user' => $item->user,
                    'created_at' => $item->created_at,
                    'url' => route('admin.guides.show', $item->id),
                ];
            });

        // Topluluk Gönderileri - Sadece gerekli kolonları seç
        $communityPosts = CommunityPost::with([
                'user:id,name,email',
                'user.profile:id,user_id,avatar_path'
            ])
            ->select('id', 'user_id', 'content', 'created_at')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'community',
                    'type_label' => 'Topluluk Gönderisi',
                    'title' => \Str::limit($item->content, 50),
                    'user' => $item->user,
                    'created_at' => $item->created_at,
                    'url' => route('admin.community-posts.show', $item->id),
                ];
            });

        return $contents
            ->merge($lfgPosts)
            ->merge($clans)
            ->merge($guides)
            ->merge($communityPosts)
            ->sortByDesc('created_at')
            ->take($limit);
    }

    /**
     * Popüler içerikleri getir (görüntülenme bazlı)
     * Eager loading ile N+1 problemini önle
     */
    private function getPopularContent($limit = 10)
    {
        $contents = collect();

        // LFG İlanları - Sadece gerekli kolonları seç
        $lfgPosts = LfgPost::with([
                'user:id,name,email',
                'user.profile:id,user_id,avatar_path',
                'game:id,name'
            ])
            ->select('id', 'user_id', 'game_id', 'title', 'views_count')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'lfg',
                    'type_label' => 'LFG İlanı',
                    'title' => $item->title,
                    'user' => $item->user,
                    'views_count' => $item->views_count ?? 0,
                    'url' => route('admin.lfg-posts.show', $item->id),
                ];
            });

        // Rehberler - Sadece gerekli kolonları seç
        $guides = GuidePost::with([
                'user:id,name,email',
                'game:id,name'
            ])
            ->select('id', 'user_id', 'game_id', 'title', 'views_count')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'guide',
                    'type_label' => 'Rehber',
                    'title' => $item->title,
                    'user' => $item->user,
                    'views_count' => $item->views_count ?? 0,
                    'url' => route('admin.guides.show', $item->id),
                ];
            });

        return $contents
            ->merge($lfgPosts)
            ->merge($guides)
            ->sortByDesc('views_count')
            ->take($limit);
    }

    /**
     * Raporlanan içerikleri getir
     * Eager loading ile N+1 problemini önle
     */
    private function getReportedContent($limit = 10)
    {
        return Report::with([
                'reportable', // Polymorphic ilişki
                'reporter:id,name,email'
            ])
            ->select('id', 'reporter_id', 'reportable_type', 'reportable_id', 'reason', 'status', 'created_at')
            ->where('status', 'pending')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($report) {
                $target = $report->reportable;
                $title = '';
                $url = '';

                if ($target) {
                    switch ($report->reportable_type) {
                        case 'App\Models\LfgPost':
                            $title = $target->title ?? 'Silinmiş İlan';
                            $url = route('admin.lfg-posts.show', $target->id);
                            break;
                        case 'App\Models\Clan':
                            $title = $target->name ?? 'Silinmiş Klan';
                            $url = route('admin.clans.show', $target->id);
                            break;
                        case 'App\Models\GuidePost':
                            $title = $target->title ?? 'Silinmiş Rehber';
                            $url = route('admin.guides.show', $target->id);
                            break;
                        case 'App\Models\CommunityPost':
                            $title = \Str::limit($target->content ?? 'Silinmiş Gönderi', 50);
                            $url = route('admin.community-posts.show', $target->id);
                            break;
                        case 'App\Models\Comment':
                            $title = \Str::limit($target->content ?? 'Silinmiş Yorum', 50);
                            $url = '#'; // Yorum detay sayfası henüz yok
                            break;
                        default:
                            $title = 'Bilinmeyen İçerik';
                            $url = '#';
                    }
                }

                return [
                    'report_id' => $report->id,
                    'type' => class_basename($report->reportable_type),
                    'title' => $title,
                    'reason' => $report->reason,
                    'reporter' => $report->reporter,
                    'created_at' => $report->created_at,
                    'url' => $url,
                    'report_url' => route('admin.reports.show', $report->id),
                ];
            });
    }

    /**
     * İçerik istatistikleri (grafik için)
     */
    public function statistics(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);

        // Günlük içerik oluşturma trendi
        $contentTrend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $contentTrend[] = [
                'date' => $date,
                'lfg' => LfgPost::whereDate('created_at', $date)->count(),
                'clan' => Clan::whereDate('created_at', $date)->count(),
                'guide' => GuidePost::whereDate('created_at', $date)->count(),
                'community' => CommunityPost::whereDate('created_at', $date)->count(),
            ];
        }

        // İçerik türü dağılımı
        $contentDistribution = [
            'lfg' => LfgPost::count(),
            'clan' => Clan::count(),
            'guide' => GuidePost::count(),
            'community' => CommunityPost::count(),
            'comment' => Comment::count(),
        ];

        return response()->json([
            'trend' => $contentTrend,
            'distribution' => $contentDistribution,
        ]);
    }
}

