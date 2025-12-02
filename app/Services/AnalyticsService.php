<?php

namespace App\Services;

use App\Models\User;
use App\Models\LfgPost;
use App\Models\Clan;
use App\Models\GuidePost;
use App\Models\CommunityPost;
use App\Models\Comment;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Analytics Service
 * 
 * Kullanıcı, içerik ve platform analitiği için detaylı raporlar sağlar.
 */
class AnalyticsService
{
    /**
     * Kullanıcı analitiğini getir
     * 
     * @param string $dateRange Tarih aralığı (week, month, quarter, year)
     * @param array $customDates Özel tarih aralığı
     * @return array
     */
    public function getUserAnalytics(string $dateRange = 'month', array $customDates = []): array
    {
        $cacheKey = CacheService::makeKey("analytics.users.{$dateRange}", $customDates);
        $ttl = CacheService::getTtl('analytics.users');
        
        return Cache::remember($cacheKey, $ttl, function () use ($dateRange, $customDates) {
            $dates = $this->getDateRange($dateRange, $customDates);
            
            return [
                'registration_trend' => $this->getRegistrationTrend($dates),
                'active_users_trend' => $this->getActiveUsersTrend($dates),
                'churn_rate' => $this->calculateChurnRate($dates),
                'retention_rate' => $this->calculateRetentionRate($dates),
                'segmentation' => $this->getUserSegmentation(),
                'demographics' => $this->getUserDemographics(),
                'engagement' => $this->getUserEngagement($dates),
            ];
        });
    }

    /**
     * İçerik analitiğini getir
     * 
     * @param string $dateRange Tarih aralığı
     * @param array $customDates Özel tarih aralığı
     * @return array
     */
    public function getContentAnalytics(string $dateRange = 'month', array $customDates = []): array
    {
        $cacheKey = CacheService::makeKey("analytics.content.{$dateRange}", $customDates);
        $ttl = CacheService::getTtl('analytics.content');
        
        return Cache::remember($cacheKey, $ttl, function () use ($dateRange, $customDates) {
            $dates = $this->getDateRange($dateRange, $customDates);
            
            return [
                'creation_trend' => $this->getContentCreationTrend($dates),
                'popular_types' => $this->getPopularContentTypes(),
                'engagement_rate' => $this->getContentEngagementRate($dates),
                'quality_score' => $this->getContentQualityScore(),
                'top_creators' => $this->getTopContentCreators($dates),
                'content_by_game' => $this->getContentByGame(),
            ];
        });
    }

    /**
     * Platform analitiğini getir
     * 
     * @param string $dateRange Tarih aralığı
     * @param array $customDates Özel tarih aralığı
     * @return array
     */
    public function getPlatformAnalytics(string $dateRange = 'month', array $customDates = []): array
    {
        $cacheKey = CacheService::makeKey("analytics.platform.{$dateRange}", $customDates);
        $ttl = CacheService::getTtl('analytics.platform');
        
        return Cache::remember($cacheKey, $ttl, function () use ($dateRange, $customDates) {
            $dates = $this->getDateRange($dateRange, $customDates);
            
            return [
                'page_views' => $this->getPageViews($dates),
                'bounce_rate' => $this->calculateBounceRate($dates),
                'session_duration' => $this->getAverageSessionDuration($dates),
                'popular_pages' => $this->getPopularPages($dates),
                'traffic_sources' => $this->getTrafficSources($dates),
                'device_distribution' => $this->getDeviceDistribution(),
            ];
        });
    }

    // ==========================================
    // KULLANICI ANALİTİĞİ METODLARI
    // ==========================================

    /**
     * Kayıt trendini getir
     */
    private function getRegistrationTrend(array $dates): array
    {
        $registrations = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', $dates)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $previousPeriod = $this->getPreviousPeriod($dates);
        $previousRegistrations = User::whereBetween('created_at', $previousPeriod)->count();
        $currentRegistrations = $registrations->sum('count');
        
        $change = $previousRegistrations > 0 
            ? (($currentRegistrations - $previousRegistrations) / $previousRegistrations) * 100 
            : 0;
        
        return [
            'data' => $registrations,
            'total' => $currentRegistrations,
            'previous_total' => $previousRegistrations,
            'change_percentage' => round($change, 2),
        ];
    }

    /**
     * Aktif kullanıcı trendini getir
     */
    private function getActiveUsersTrend(array $dates): array
    {
        // Son 7 gün içinde aktivite gösteren kullanıcılar
        $activeUsers = User::selectRaw('DATE(updated_at) as date, COUNT(DISTINCT id) as count')
            ->whereBetween('updated_at', $dates)
            ->where('updated_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 7 DAY)'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'data' => $activeUsers,
            'total' => User::where('updated_at', '>=', now()->subDays(7))->count(),
            'daily_average' => round($activeUsers->avg('count'), 2),
        ];
    }

    /**
     * Churn rate (kayıp oranı) hesapla
     */
    private function calculateChurnRate(array $dates): array
    {
        $startDate = Carbon::parse($dates[0]);
        $endDate = Carbon::parse($dates[1]);
        
        // Dönem başındaki kullanıcılar
        $usersAtStart = User::where('created_at', '<', $startDate)->count();
        
        // Dönem içinde kaybedilen kullanıcılar (30 gün aktivite yok)
        $churnedUsers = User::where('created_at', '<', $startDate)
            ->where('updated_at', '<', $endDate->copy()->subDays(30))
            ->count();
        
        $churnRate = $usersAtStart > 0 ? ($churnedUsers / $usersAtStart) * 100 : 0;
        
        return [
            'rate' => round($churnRate, 2),
            'churned_users' => $churnedUsers,
            'total_users' => $usersAtStart,
        ];
    }

    /**
     * Retention rate (elde tutma oranı) hesapla
     */
    private function calculateRetentionRate(array $dates): array
    {
        $startDate = Carbon::parse($dates[0]);
        $endDate = Carbon::parse($dates[1]);
        
        // Dönem başında kayıt olan kullanıcılar
        $newUsers = User::whereBetween('created_at', [$startDate, $startDate->copy()->addDays(7)])->count();
        
        // Bu kullanıcılardan hala aktif olanlar
        $retainedUsers = User::whereBetween('created_at', [$startDate, $startDate->copy()->addDays(7)])
            ->where('updated_at', '>=', $endDate->copy()->subDays(7))
            ->count();
        
        $retentionRate = $newUsers > 0 ? ($retainedUsers / $newUsers) * 100 : 0;
        
        return [
            'rate' => round($retentionRate, 2),
            'retained_users' => $retainedUsers,
            'new_users' => $newUsers,
        ];
    }

    /**
     * Kullanıcı segmentasyonu
     */
    private function getUserSegmentation(): array
    {
        $now = now();
        
        return [
            'new' => User::where('created_at', '>=', $now->copy()->subDays(7))->count(),
            'active' => User::where('updated_at', '>=', $now->copy()->subDays(7))
                ->where('created_at', '<', $now->copy()->subDays(7))
                ->count(),
            'inactive' => User::where('updated_at', '<', $now->copy()->subDays(30))
                ->where('updated_at', '>=', $now->copy()->subDays(90))
                ->count(),
            'churned' => User::where('updated_at', '<', $now->copy()->subDays(90))->count(),
        ];
    }

    /**
     * Kullanıcı demografisi
     */
    private function getUserDemographics(): array
    {
        return [
            'by_city' => Profile::select('city', DB::raw('COUNT(*) as count'))
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
            'by_gender' => Profile::select('gender', DB::raw('COUNT(*) as count'))
                ->whereNotNull('gender')
                ->groupBy('gender')
                ->get(),
            'by_age_group' => $this->getAgeGroupDistribution(),
        ];
    }

    /**
     * Yaş grubu dağılımı
     */
    private function getAgeGroupDistribution(): array
    {
        // Profiles tablosunda age_range kolonu var (birth_date değil)
        $ageRanges = Profile::select('age_range', DB::raw('COUNT(*) as count'))
            ->whereNotNull('age_range')
            ->groupBy('age_range')
            ->get()
            ->pluck('count', 'age_range')
            ->toArray();
        
        // Standart yaş gruplarına dönüştür
        $standardGroups = [
            '13-17' => $ageRanges['13-17'] ?? 0,
            '18-24' => $ageRanges['18-24'] ?? 0,
            '25-34' => ($ageRanges['25-30'] ?? 0) + ($ageRanges['31-34'] ?? 0),
            '35-44' => ($ageRanges['35-40'] ?? 0) + ($ageRanges['41-44'] ?? 0),
            '45+' => $ageRanges['45+'] ?? 0,
        ];
        
        return $standardGroups;
    }

    /**
     * Kullanıcı etkileşimi
     */
    private function getUserEngagement(array $dates): array
    {
        return [
            'posts_per_user' => $this->getAveragePostsPerUser($dates),
            'comments_per_user' => $this->getAverageCommentsPerUser($dates),
            'active_days_per_user' => $this->getAverageActiveDaysPerUser($dates),
        ];
    }

    // ==========================================
    // İÇERİK ANALİTİĞİ METODLARI
    // ==========================================

    /**
     * İçerik oluşturma trendi
     */
    private function getContentCreationTrend(array $dates): array
    {
        $lfgPosts = LfgPost::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', $dates)
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');
        
        $clans = Clan::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', $dates)
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');
        
        $guides = GuidePost::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', $dates)
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');
        
        $posts = CommunityPost::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', $dates)
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');
        
        return [
            'lfg_posts' => $lfgPosts,
            'clans' => $clans,
            'guides' => $guides,
            'community_posts' => $posts,
        ];
    }

    /**
     * Popüler içerik türleri
     */
    private function getPopularContentTypes(): array
    {
        return [
            'lfg_posts' => LfgPost::count(),
            'clans' => Clan::count(),
            'guides' => GuidePost::count(),
            'community_posts' => CommunityPost::count(),
        ];
    }

    /**
     * İçerik etkileşim oranı
     */
    private function getContentEngagementRate(array $dates): array
    {
        $totalContent = LfgPost::whereBetween('created_at', $dates)->count() +
                       Clan::whereBetween('created_at', $dates)->count() +
                       GuidePost::whereBetween('created_at', $dates)->count() +
                       CommunityPost::whereBetween('created_at', $dates)->count();
        
        $totalComments = Comment::whereBetween('created_at', $dates)->count();
        
        $engagementRate = $totalContent > 0 ? ($totalComments / $totalContent) : 0;
        
        return [
            'rate' => round($engagementRate, 2),
            'total_content' => $totalContent,
            'total_comments' => $totalComments,
        ];
    }

    /**
     * İçerik kalitesi skoru
     * Sadece gerekli kolonları seç ve limit ekle
     */
    private function getContentQualityScore(): array
    {
        // Basit bir kalite skoru: yorum sayısı + beğeni sayısı
        $guides = GuidePost::select('id', 'title', 'likes')
            ->withCount('comments')
            ->orderByDesc('comments_count')
            ->limit(50) // Tüm guide'ları çekmek yerine ilk 50'yi al
            ->get()
            ->map(function ($guide) {
                return [
                    'id' => $guide->id,
                    'title' => $guide->title,
                    'score' => $guide->comments_count + ($guide->likes ?? 0),
                ];
            })
            ->sortByDesc('score')
            ->take(10);
        
        return [
            'top_guides' => $guides->values()->toArray(),
            'average_score' => round($guides->avg('score'), 2),
        ];
    }

    /**
     * En çok içerik üreten kullanıcılar
     * Sadece gerekli kolonları seç
     */
    private function getTopContentCreators(array $dates): array
    {
        $creators = User::select('id', 'name', 'email')
            ->withCount([
                'lfgPosts' => function ($query) use ($dates) {
                    $query->whereBetween('lfg_posts.created_at', $dates);
                },
                'clans' => function ($query) use ($dates) {
                    $query->whereBetween('clans.created_at', $dates);
                },
            ])
            ->having(DB::raw('lfg_posts_count + clans_count'), '>', 0)
            ->orderByDesc(DB::raw('lfg_posts_count + clans_count'))
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'total_content' => $user->lfg_posts_count + $user->clans_count,
                ];
            });
        
        return $creators->toArray();
    }

    /**
     * Oyuna göre içerik dağılımı
     */
    private function getContentByGame(): array
    {
        $lfgByGame = LfgPost::select('game_id', DB::raw('COUNT(*) as count'))
            ->groupBy('game_id')
            ->with('game:id,name')
            ->get();
        
        $clansByGame = Clan::select('game_id', DB::raw('COUNT(*) as count'))
            ->groupBy('game_id')
            ->with('game:id,name')
            ->get();
        
        return [
            'lfg_posts' => $lfgByGame,
            'clans' => $clansByGame,
        ];
    }

    // ==========================================
    // PLATFORM ANALİTİĞİ METODLARI
    // ==========================================

    /**
     * Sayfa görüntülenmeleri (simüle edilmiş)
     */
    private function getPageViews(array $dates): array
    {
        // Gerçek implementasyonda Google Analytics veya benzeri kullanılmalı
        return [
            'total' => 0,
            'unique' => 0,
            'note' => 'Google Analytics entegrasyonu gerekli',
        ];
    }

    /**
     * Bounce rate hesapla (simüle edilmiş)
     */
    private function calculateBounceRate(array $dates): array
    {
        return [
            'rate' => 0,
            'note' => 'Google Analytics entegrasyonu gerekli',
        ];
    }

    /**
     * Ortalama oturum süresi (simüle edilmiş)
     */
    private function getAverageSessionDuration(array $dates): array
    {
        return [
            'duration' => 0,
            'note' => 'Google Analytics entegrasyonu gerekli',
        ];
    }

    /**
     * Popüler sayfalar (simüle edilmiş)
     */
    private function getPopularPages(array $dates): array
    {
        return [
            'pages' => [],
            'note' => 'Google Analytics entegrasyonu gerekli',
        ];
    }

    /**
     * Trafik kaynakları (simüle edilmiş)
     */
    private function getTrafficSources(array $dates): array
    {
        return [
            'sources' => [],
            'note' => 'Google Analytics entegrasyonu gerekli',
        ];
    }

    /**
     * Cihaz dağılımı
     */
    private function getDeviceDistribution(): array
    {
        // Profiles tablosunda device_type kolonu yok
        // Gelecekte eklenebilir veya Google Analytics'ten alınabilir
        return [
            'mobile' => 0,
            'desktop' => 0,
            'tablet' => 0,
            'note' => 'Cihaz takibi için ek kolon veya Google Analytics entegrasyonu gerekli',
        ];
    }

    // ==========================================
    // YARDIMCI METODLAR
    // ==========================================

    /**
     * Tarih aralığını hesapla
     */
    private function getDateRange(string $dateRange, array $customDates): array
    {
        return match ($dateRange) {
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'custom' => [
                Carbon::parse($customDates['start'] ?? now()->startOfMonth()),
                Carbon::parse($customDates['end'] ?? now()->endOfMonth())
            ],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    /**
     * Önceki dönemi hesapla
     */
    private function getPreviousPeriod(array $dates): array
    {
        $start = Carbon::parse($dates[0]);
        $end = Carbon::parse($dates[1]);
        $diff = $start->diffInDays($end);
        
        return [
            $start->copy()->subDays($diff + 1),
            $start->copy()->subDay(),
        ];
    }

    /**
     * Kullanıcı başına ortalama gönderi sayısı
     */
    private function getAveragePostsPerUser(array $dates): float
    {
        $totalPosts = LfgPost::whereBetween('created_at', $dates)->count() +
                     CommunityPost::whereBetween('created_at', $dates)->count();
        
        $activeUsers = User::whereBetween('updated_at', $dates)->count();
        
        return $activeUsers > 0 ? round($totalPosts / $activeUsers, 2) : 0;
    }

    /**
     * Kullanıcı başına ortalama yorum sayısı
     */
    private function getAverageCommentsPerUser(array $dates): float
    {
        $totalComments = Comment::whereBetween('created_at', $dates)->count();
        $activeUsers = User::whereBetween('updated_at', $dates)->count();
        
        return $activeUsers > 0 ? round($totalComments / $activeUsers, 2) : 0;
    }

    /**
     * Kullanıcı başına ortalama aktif gün sayısı
     */
    private function getAverageActiveDaysPerUser(array $dates): float
    {
        // Basitleştirilmiş hesaplama
        $users = User::whereBetween('updated_at', $dates)->count();
        $days = Carbon::parse($dates[0])->diffInDays(Carbon::parse($dates[1]));
        
        return $users > 0 ? round($days / 2, 2) : 0; // Ortalama yarı gün aktif
    }
}
