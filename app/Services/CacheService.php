<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Cache Service
 * 
 * Merkezi cache yönetimi ve cache stratejileri
 */
class CacheService
{
    /**
     * Cache anahtarları ve süreleri (saniye)
     */
    const CACHE_KEYS = [
        // Dashboard cache (5 dakika)
        'dashboard.stats' => 300,
        'dashboard.chart' => 600,
        'dashboard.activities' => 180,
        
        // Analytics cache (10 dakika)
        'analytics.users' => 600,
        'analytics.content' => 600,
        'analytics.platform' => 600,
        
        // İstatistik cache (1 saat)
        'stats.users.total' => 3600,
        'stats.users.active' => 1800,
        'stats.content.total' => 3600,
        'stats.moderation.pending' => 300,
        
        // Query cache (30 dakika)
        'query.popular.guides' => 1800,
        'query.popular.clans' => 1800,
        'query.top.users' => 1800,
        'query.recent.users' => 300,
    ];

    /**
     * Cache'i temizle
     * 
     * @param string|null $pattern Cache anahtarı pattern'i (null ise tümü)
     * @return bool
     */
    public function clear(?string $pattern = null): bool
    {
        if ($pattern === null) {
            // Tüm cache'i temizle
            return Cache::flush();
        }

        // Pattern'e göre cache'i temizle
        $keys = $this->getKeysByPattern($pattern);
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        return true;
    }

    /**
     * Dashboard cache'ini temizle
     * 
     * @return bool
     */
    public function clearDashboard(): bool
    {
        return $this->clear('dashboard.*');
    }

    /**
     * Analytics cache'ini temizle
     * 
     * @return bool
     */
    public function clearAnalytics(): bool
    {
        return $this->clear('analytics.*');
    }

    /**
     * İstatistik cache'ini temizle
     * 
     * @return bool
     */
    public function clearStats(): bool
    {
        return $this->clear('stats.*');
    }

    /**
     * Query cache'ini temizle
     * 
     * @return bool
     */
    public function clearQueries(): bool
    {
        return $this->clear('query.*');
    }

    /**
     * Cache istatistiklerini getir
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        $driver = config('cache.default');
        
        $stats = [
            'driver' => $driver,
            'keys_count' => 0,
            'size' => 'N/A',
            'hit_rate' => 0, // Float olarak döndür
        ];

        // Driver'a göre istatistikleri al
        if ($driver === 'redis') {
            $stats = array_merge($stats, $this->getRedisStats());
        } elseif ($driver === 'database') {
            $stats = array_merge($stats, $this->getDatabaseCacheStats());
        }

        return $stats;
    }

    /**
     * Redis cache istatistiklerini getir
     * 
     * @return array
     */
    private function getRedisStats(): array
    {
        try {
            $redis = Cache::getStore()->getRedis();
            $info = $redis->info();
            
            return [
                'keys_count' => $redis->dbSize(),
                'size' => $this->formatBytes($info['used_memory'] ?? 0),
                'hit_rate' => isset($info['keyspace_hits'], $info['keyspace_misses']) 
                    ? round(($info['keyspace_hits'] / ($info['keyspace_hits'] + $info['keyspace_misses'])) * 100, 2)
                    : 0,
            ];
        } catch (\Exception $e) {
            return [
                'keys_count' => 0,
                'size' => 'N/A',
                'hit_rate' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Database cache istatistiklerini getir
     * 
     * @return array
     */
    private function getDatabaseCacheStats(): array
    {
        try {
            $table = config('cache.stores.database.table', 'cache');
            $count = DB::table($table)->count();
            
            // Yaklaşık boyut hesaplama
            $size = DB::table($table)
                ->selectRaw('SUM(LENGTH(value)) as total_size')
                ->first();
            
            return [
                'keys_count' => $count,
                'size' => $this->formatBytes($size->total_size ?? 0),
            ];
        } catch (\Exception $e) {
            return [
                'keys_count' => 0,
                'size' => 'N/A',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Pattern'e göre cache anahtarlarını getir
     * 
     * @param string $pattern
     * @return array
     */
    private function getKeysByPattern(string $pattern): array
    {
        $driver = config('cache.default');
        $prefix = config('cache.prefix');
        
        // Pattern'i wildcard'a çevir
        $pattern = str_replace('*', '.*', $pattern);
        
        $keys = [];
        
        // Driver'a göre anahtarları al
        if ($driver === 'redis') {
            try {
                $redis = Cache::getStore()->getRedis();
                $allKeys = $redis->keys($prefix . '*');
                
                foreach ($allKeys as $key) {
                    $cleanKey = str_replace($prefix, '', $key);
                    if (preg_match('/^' . $pattern . '$/', $cleanKey)) {
                        $keys[] = $cleanKey;
                    }
                }
            } catch (\Exception $e) {
                // Redis hatası
            }
        } elseif ($driver === 'database') {
            try {
                $table = config('cache.stores.database.table', 'cache');
                $dbKeys = DB::table($table)->pluck('key');
                
                foreach ($dbKeys as $key) {
                    $cleanKey = str_replace($prefix, '', $key);
                    if (preg_match('/^' . $pattern . '$/', $cleanKey)) {
                        $keys[] = $cleanKey;
                    }
                }
            } catch (\Exception $e) {
                // Database hatası
            }
        }
        
        return $keys;
    }

    /**
     * Cache'i ısıt (warm up)
     * Sık kullanılan verileri önceden cache'e yükle
     * 
     * @return array
     */
    public function warmUp(): array
    {
        $warmed = [];
        
        try {
            // Dashboard istatistiklerini cache'e al
            app(DashboardService::class)->getStatistics('today');
            $warmed[] = 'dashboard.stats.today';
            
            // Grafik verilerini cache'e al
            app(DashboardService::class)->getChartData('registration', 'month');
            $warmed[] = 'dashboard.chart.registration';
            
            app(DashboardService::class)->getChartData('content');
            $warmed[] = 'dashboard.chart.content';
            
            // Analytics verilerini cache'e al
            app(AnalyticsService::class)->getUserAnalytics('month');
            $warmed[] = 'analytics.users.month';
            
            app(AnalyticsService::class)->getContentAnalytics('month');
            $warmed[] = 'analytics.content.month';
            
        } catch (\Exception $e) {
            $warmed[] = 'error: ' . $e->getMessage();
        }
        
        return $warmed;
    }

    /**
     * Süresi dolmuş cache'leri temizle
     * 
     * @return int Temizlenen kayıt sayısı
     */
    public function clearExpired(): int
    {
        $driver = config('cache.default');
        
        if ($driver === 'database') {
            try {
                $table = config('cache.stores.database.table', 'cache');
                return DB::table($table)
                    ->where('expiration', '<', time())
                    ->delete();
            } catch (\Exception $e) {
                return 0;
            }
        }
        
        // Redis ve diğer driver'lar otomatik olarak süresi dolmuş cache'leri temizler
        return 0;
    }

    /**
     * Cache boyutunu optimize et
     * 
     * @return array
     */
    public function optimize(): array
    {
        $result = [
            'cleared_expired' => $this->clearExpired(),
            'config_cached' => false,
            'route_cached' => false,
            'view_cached' => false,
        ];
        
        try {
            // Laravel cache'lerini optimize et
            Artisan::call('config:cache');
            $result['config_cached'] = true;
            
            Artisan::call('route:cache');
            $result['route_cached'] = true;
            
            Artisan::call('view:cache');
            $result['view_cached'] = true;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }
        
        return $result;
    }

    /**
     * Byte'ları okunabilir formata çevir
     * 
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Cache anahtarı oluştur
     * 
     * @param string $prefix
     * @param array $params
     * @return string
     */
    public static function makeKey(string $prefix, array $params = []): string
    {
        if (empty($params)) {
            return $prefix;
        }
        
        return $prefix . '.' . md5(json_encode($params));
    }

    /**
     * Cache süresini getir
     * 
     * @param string $key
     * @return int Saniye cinsinden süre
     */
    public static function getTtl(string $key): int
    {
        // Tam eşleşme ara
        if (isset(self::CACHE_KEYS[$key])) {
            return self::CACHE_KEYS[$key];
        }
        
        // Pattern eşleşmesi ara
        foreach (self::CACHE_KEYS as $pattern => $ttl) {
            if (str_starts_with($key, $pattern)) {
                return $ttl;
            }
        }
        
        // Varsayılan: 5 dakika
        return 300;
    }
}
