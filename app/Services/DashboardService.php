<?php

namespace App\Services;

use App\Models\User;
use App\Models\LfgPost;
use App\Models\Clan;
use App\Models\GuidePost;
use App\Models\CommunityPost;
use App\Models\Report;
use App\Models\ClanApplication;
use App\Models\LfgApplication;
use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Dashboard Service
 * 
 * Admin paneli dashboard için istatistik, grafik ve aktivite verilerini sağlar.
 */
class DashboardService
{
    /**
     * Dashboard istatistiklerini getir
     * 
     * @param string $dateRange Tarih aralığı (today, yesterday, week, month, custom)
     * @param array $customDates Özel tarih aralığı ['start' => '2024-01-01', 'end' => '2024-01-31']
     * @return array
     */
    public function getStatistics(string $dateRange = 'today', array $customDates = []): array
    {
        $cacheKey = CacheService::makeKey("dashboard.stats.{$dateRange}", $customDates);
        $ttl = CacheService::getTtl('dashboard.stats');
        
        return Cache::remember($cacheKey, $ttl, function () use ($dateRange, $customDates) {
            return [
                'users' => $this->getUserStats($dateRange, $customDates),
                'content' => $this->getContentStats($dateRange, $customDates),
                'moderation' => $this->getModerationStats($dateRange, $customDates),
                'system' => $this->getSystemStats(),
            ];
        });
    }

    /**
     * Kullanıcı istatistiklerini getir
     */
    private function getUserStats(string $dateRange, array $customDates): array
    {
        $dates = $this->getDateRange($dateRange, $customDates);
        
        return [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'banned' => User::where('status', 'banned')->count(),
            'frozen' => User::where('status', 'frozen')->count(),
            'new_today' => User::whereDate('created_at', today())->count(),
            'new_this_week' => User::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'new_in_range' => User::whereBetween('created_at', $dates)->count(),
            'admins' => User::where('is_admin', true)->count(),
            'verified_emails' => User::whereNotNull('email_verified_at')->count(),
        ];
    }

    /**
     * İçerik istatistiklerini getir
     */
    private function getContentStats(string $dateRange, array $customDates): array
    {
        $dates = $this->getDateRange($dateRange, $customDates);
        
        return [
            'lfg_posts' => [
                'total' => LfgPost::count(),
                'open' => LfgPost::where('status', 'open')->count(),
                'closed' => LfgPost::where('status', 'closed')->count(),
                'featured' => LfgPost::where('is_featured', true)->count(),
                'new_in_range' => LfgPost::whereBetween('created_at', $dates)->count(),
            ],
            'clans' => [
                'total' => Clan::count(),
                'verified' => Clan::where('is_verified', true)->count(),
                'active' => Clan::count(), // Tüm klanlar aktif sayılıyor (status kolonu yok)
                'new_in_range' => Clan::whereBetween('created_at', $dates)->count(),
            ],
            'guides' => [
                'total' => GuidePost::count(),
                'published' => GuidePost::where('is_published', true)->count(),
                'featured' => GuidePost::where('is_featured', true)->count(),
                'new_in_range' => GuidePost::whereBetween('created_at', $dates)->count(),
            ],
            'community_posts' => [
                'total' => CommunityPost::count(),
                'featured' => CommunityPost::where('is_featured', true)->count(),
                'new_in_range' => CommunityPost::whereBetween('created_at', $dates)->count(),
            ],
        ];
    }

    /**
     * Moderasyon istatistiklerini getir
     */
    private function getModerationStats(string $dateRange, array $customDates): array
    {
        $dates = $this->getDateRange($dateRange, $customDates);
        
        return [
            'reports' => [
                'total' => Report::count(),
                'pending' => Report::where('status', 'pending')->count(),
                'resolved' => Report::where('status', 'resolved')->count(),
                'rejected' => Report::where('status', 'rejected')->count(),
                'new_in_range' => Report::whereBetween('created_at', $dates)->count(),
            ],
            'applications' => [
                'clan_pending' => ClanApplication::where('status', 'pending')->count(),
                'lfg_pending' => LfgApplication::where('status', 'pending')->count(),
                'total_pending' => ClanApplication::where('status', 'pending')->count() + 
                                  LfgApplication::where('status', 'pending')->count(),
            ],
        ];
    }

    /**
     * Sistem istatistiklerini getir
     */
    private function getSystemStats(): array
    {
        return [
            'disk_usage' => $this->getDiskUsage(),
            'cache_size' => $this->getCacheSize(),
            'database_size' => $this->getDatabaseSize(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
    }

    /**
     * Grafik verilerini getir
     * 
     * @param string $type Grafik tipi (registration, content, active_users, popular_hours)
     * @param string $dateRange Tarih aralığı
     * @param array $customDates Özel tarih aralığı
     * @return array
     */
    public function getChartData(string $type, string $dateRange = 'month', array $customDates = []): array
    {
        $cacheKey = CacheService::makeKey("dashboard.chart.{$type}.{$dateRange}", $customDates);
        $ttl = CacheService::getTtl('dashboard.chart');
        
        return Cache::remember($cacheKey, $ttl, function () use ($type, $dateRange, $customDates) {
            return match ($type) {
                'registration' => $this->getRegistrationTrendData($dateRange, $customDates),
                'content' => $this->getContentDistributionData(),
                'active_users' => $this->getActiveUsersTrendData($dateRange, $customDates),
                'popular_hours' => $this->getPopularHoursData(),
                default => [],
            };
        });
    }

    /**
     * Kayıt trendi verilerini getir
     */
    private function getRegistrationTrendData(string $dateRange, array $customDates): array
    {
        $dates = $this->getDateRange($dateRange, $customDates);
        $days = $this->getDaysBetween($dates[0], $dates[1]);
        
        $registrations = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', $dates)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
        
        $labels = [];
        $data = [];
        
        foreach ($days as $day) {
            $labels[] = $day->format('d M');
            $data[] = $registrations[$day->format('Y-m-d')] ?? 0;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Yeni Kayıtlar',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ]
            ]
        ];
    }

    /**
     * İçerik dağılımı verilerini getir
     */
    private function getContentDistributionData(): array
    {
        return [
            'labels' => ['LFG İlanları', 'Klanlar', 'Rehberler', 'Gönderiler'],
            'datasets' => [
                [
                    'data' => [
                        LfgPost::count(),
                        Clan::count(),
                        GuidePost::count(),
                        CommunityPost::count(),
                    ],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                ]
            ]
        ];
    }

    /**
     * Aktif kullanıcı trendi verilerini getir
     */
    private function getActiveUsersTrendData(string $dateRange, array $customDates): array
    {
        $dates = $this->getDateRange($dateRange, $customDates);
        $days = $this->getDaysBetween($dates[0], $dates[1]);
        
        // Son 24 saat içinde aktivite gösteren kullanıcılar
        $activeUsers = User::selectRaw('DATE(updated_at) as date, COUNT(*) as count')
            ->whereBetween('updated_at', $dates)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
        
        $labels = [];
        $data = [];
        
        foreach ($days as $day) {
            $labels[] = $day->format('d M');
            $data[] = $activeUsers[$day->format('Y-m-d')] ?? 0;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Aktif Kullanıcılar',
                    'data' => $data,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ]
            ]
        ];
    }

    /**
     * Popüler saatler verilerini getir
     */
    private function getPopularHoursData(): array
    {
        $hourlyActivity = User::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();
        
        $labels = [];
        $data = [];
        
        for ($i = 0; $i < 24; $i++) {
            $labels[] = sprintf('%02d:00', $i);
            $data[] = $hourlyActivity[$i] ?? 0;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Aktivite',
                    'data' => $data,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                ]
            ]
        ];
    }

    /**
     * Son aktiviteleri getir
     * 
     * @param int $limit Limit
     * @return \Illuminate\Support\Collection
     */
    public function getRecentActivities(int $limit = 10)
    {
        return AdminActivityLog::with('admin:id,name,email')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'admin' => $log->admin ? $log->admin->name : 'Bilinmeyen',
                    'action' => $log->action,
                    'description' => $this->formatActivityDescription($log),
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->diffForHumans(),
                    'created_at_full' => $log->created_at->format('d.m.Y H:i:s'),
                ];
            });
    }

    /**
     * Aktivite açıklamasını formatla
     */
    private function formatActivityDescription(AdminActivityLog $log): string
    {
        $meta = $log->meta ?? [];
        $action = $log->action;
        
        // Route ismine göre açıklama oluştur
        $descriptions = [
            'admin.users.ban' => 'Kullanıcıyı banladı',
            'admin.users.unban' => 'Kullanıcının banını kaldırdı',
            'admin.users.update' => 'Kullanıcı bilgilerini güncelledi',
            'admin.users.delete' => 'Kullanıcıyı sildi',
            'admin.clans.verify' => 'Klanı doğruladı',
            'admin.clans.update' => 'Klan bilgilerini güncelledi',
            'admin.clans.delete' => 'Klanı sildi',
            'admin.reports.resolve' => 'Raporu çözümledi',
            'admin.reports.reject' => 'Raporu reddetti',
            'admin.settings.update' => 'Sistem ayarlarını güncelledi',
        ];
        
        return $descriptions[$action] ?? 'İşlem gerçekleştirdi';
    }

    /**
     * Tarih aralığını hesapla
     */
    private function getDateRange(string $dateRange, array $customDates): array
    {
        return match ($dateRange) {
            'today' => [today()->startOfDay(), today()->endOfDay()],
            'yesterday' => [
                today()->subDay()->startOfDay(),
                today()->subDay()->endOfDay()
            ],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'custom' => [
                Carbon::parse($customDates['start'] ?? today())->startOfDay(),
                Carbon::parse($customDates['end'] ?? today())->endOfDay()
            ],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    /**
     * İki tarih arasındaki günleri getir
     */
    private function getDaysBetween(Carbon $start, Carbon $end): array
    {
        $days = [];
        $current = $start->copy();
        
        while ($current->lte($end)) {
            $days[] = $current->copy();
            $current->addDay();
        }
        
        return $days;
    }

    /**
     * Disk kullanımını getir
     */
    private function getDiskUsage(): array
    {
        $total = disk_total_space(base_path());
        $free = disk_free_space(base_path());
        $used = $total - $free;
        
        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percentage' => round(($used / $total) * 100, 2),
        ];
    }

    /**
     * Cache boyutunu getir
     */
    private function getCacheSize(): string
    {
        // Basit bir tahmin - gerçek cache boyutu için Redis/Memcached API kullanılmalı
        return 'N/A';
    }

    /**
     * Veritabanı boyutunu getir
     */
    private function getDatabaseSize(): string
    {
        try {
            $database = config('database.connections.mysql.database');
            $result = DB::selectOne("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.TABLES
                WHERE table_schema = ?
            ", [$database]);
            
            return $result->size_mb . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Byte'ları okunabilir formata çevir
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
