<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * System Controller
 * Sistem sağlık durumu, cache yönetimi ve performans izleme
 */
class SystemController extends Controller
{
    /**
     * Cache Service
     */
    protected CacheService $cacheService;

    /**
     * Constructor
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Sistem yönetimi ana sayfası
     * GET /admin/system
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Sistem sağlık durumu
        $health = $this->getHealthStatus();
        
        // Sistem bilgileri
        $systemInfo = $this->getSystemInfo();
        
        // Cache istatistikleri
        $cacheStats = $this->cacheService->getStatistics();
        
        // Performans metrikleri
        $performance = $this->getPerformanceMetrics();
        
        return view('admin.system.index', compact(
            'health',
            'systemInfo',
            'cacheStats',
            'performance'
        ));
    }

    /**
     * Sistem sağlık durumu kontrolü
     * GET /admin/system/health
     * 
     * @return JsonResponse
     */
    public function health(): JsonResponse
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'checks' => [],
        ];

        // Veritabanı kontrolü
        try {
            DB::connection()->getPdo();
            $health['checks']['database'] = [
                'status' => 'up',
                'message' => 'Veritabanı bağlantısı başarılı',
            ];
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['checks']['database'] = [
                'status' => 'down',
                'message' => 'Veritabanı bağlantısı başarısız: ' . $e->getMessage(),
            ];
        }

        // Cache kontrolü
        try {
            Cache::put('health_check', true, 10);
            $cacheWorks = Cache::get('health_check') === true;
            Cache::forget('health_check');
            
            $health['checks']['cache'] = [
                'status' => $cacheWorks ? 'up' : 'down',
                'message' => $cacheWorks ? 'Cache sistemi çalışıyor' : 'Cache sistemi çalışmıyor',
                'driver' => config('cache.default'),
            ];
            
            if (!$cacheWorks) {
                $health['status'] = 'degraded';
            }
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['checks']['cache'] = [
                'status' => 'down',
                'message' => 'Cache hatası: ' . $e->getMessage(),
            ];
        }

        // Disk alanı kontrolü
        $diskFree = disk_free_space('/');
        $diskTotal = disk_total_space('/');
        $diskUsedPercent = (($diskTotal - $diskFree) / $diskTotal) * 100;
        
        $health['checks']['disk'] = [
            'status' => $diskUsedPercent < 90 ? 'up' : 'warning',
            'message' => sprintf('Disk kullanımı: %.2f%%', $diskUsedPercent),
            'free' => $this->formatBytes($diskFree),
            'total' => $this->formatBytes($diskTotal),
            'used_percent' => round($diskUsedPercent, 2),
        ];
        
        if ($diskUsedPercent >= 90) {
            $health['status'] = 'degraded';
        }

        // Memory kontrolü
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        
        $health['checks']['memory'] = [
            'status' => 'up',
            'limit' => $memoryLimit,
            'current' => $this->formatBytes($memoryUsage),
            'peak' => $this->formatBytes($memoryPeak),
        ];

        return response()->json($health);
    }

    /**
     * Sistem bilgilerini getir
     * GET /admin/system/info
     * 
     * @return JsonResponse
     */
    public function info(): JsonResponse
    {
        $info = [
            'server' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'os' => PHP_OS,
            ],
            'database' => [
                'driver' => config('database.default'),
                'version' => $this->getDatabaseVersion(),
            ],
            'cache' => [
                'driver' => config('cache.default'),
                'prefix' => config('cache.prefix'),
            ],
            'queue' => [
                'driver' => config('queue.default'),
            ],
            'session' => [
                'driver' => config('session.driver'),
                'lifetime' => config('session.lifetime'),
            ],
        ];

        return response()->json($info);
    }

    /**
     * Cache istatistiklerini getir
     * GET /admin/system/cache/stats
     * 
     * @return JsonResponse
     */
    public function cacheStats(): JsonResponse
    {
        $stats = $this->cacheService->getStatistics();
        
        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Cache'i temizle
     * POST /admin/system/cache/clear
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function clearCache(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'nullable|string|in:all,dashboard,analytics,stats,queries,config,route,view',
        ]);

        $type = $request->input('type', 'all');
        $result = ['success' => true, 'message' => ''];

        try {
            switch ($type) {
                case 'dashboard':
                    $this->cacheService->clearDashboard();
                    $result['message'] = 'Dashboard cache temizlendi';
                    break;
                    
                case 'analytics':
                    $this->cacheService->clearAnalytics();
                    $result['message'] = 'Analytics cache temizlendi';
                    break;
                    
                case 'stats':
                    $this->cacheService->clearStats();
                    $result['message'] = 'İstatistik cache temizlendi';
                    break;
                    
                case 'queries':
                    $this->cacheService->clearQueries();
                    $result['message'] = 'Query cache temizlendi';
                    break;
                    
                case 'config':
                    Artisan::call('config:clear');
                    $result['message'] = 'Config cache temizlendi';
                    break;
                    
                case 'route':
                    Artisan::call('route:clear');
                    $result['message'] = 'Route cache temizlendi';
                    break;
                    
                case 'view':
                    Artisan::call('view:clear');
                    $result['message'] = 'View cache temizlendi';
                    break;
                    
                case 'all':
                default:
                    $this->cacheService->clear();
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    $result['message'] = 'Tüm cache temizlendi';
                    break;
            }
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['message'] = 'Cache temizleme hatası: ' . $e->getMessage();
        }

        return response()->json($result);
    }

    /**
     * Cache'i ısıt (warm up)
     * POST /admin/system/cache/warm-up
     * 
     * @return JsonResponse
     */
    public function warmUpCache(): JsonResponse
    {
        try {
            $warmed = $this->cacheService->warmUp();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache ısıtma tamamlandı',
                'warmed_keys' => $warmed,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache ısıtma hatası: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cache'i optimize et
     * POST /admin/system/cache/optimize
     * 
     * @return JsonResponse
     */
    public function optimizeCache(): JsonResponse
    {
        try {
            $result = $this->cacheService->optimize();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache optimizasyonu tamamlandı',
                'details' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache optimizasyon hatası: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Performans metriklerini getir
     * GET /admin/system/performance
     * 
     * @return JsonResponse
     */
    public function performance(): JsonResponse
    {
        $metrics = [
            'memory' => [
                'current' => memory_get_usage(true),
                'current_formatted' => $this->formatBytes(memory_get_usage(true)),
                'peak' => memory_get_peak_usage(true),
                'peak_formatted' => $this->formatBytes(memory_get_peak_usage(true)),
                'limit' => ini_get('memory_limit'),
            ],
            'database' => [
                'size' => $this->getDatabaseSize(),
                'table_count' => $this->getTableCount(),
            ],
            'cache' => $this->cacheService->getStatistics(),
            'disk' => [
                'free' => disk_free_space('/'),
                'free_formatted' => $this->formatBytes(disk_free_space('/')),
                'total' => disk_total_space('/'),
                'total_formatted' => $this->formatBytes(disk_total_space('/')),
                'used_percent' => round(((disk_total_space('/') - disk_free_space('/')) / disk_total_space('/')) * 100, 2),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ]);
    }

    /**
     * Slow query'leri getir
     * GET /admin/system/slow-queries
     * 
     * @return JsonResponse
     */
    public function slowQueries(): JsonResponse
    {
        // Not: Bu özellik için MySQL slow query log aktif olmalı
        // Şimdilik boş array döndürüyoruz, ileride log dosyasından okuyabiliriz
        
        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Slow query log özelliği henüz aktif değil',
        ]);
    }

    /**
     * Sağlık durumunu getir (view için)
     * 
     * @return array
     */
    private function getHealthStatus(): array
    {
        $health = [
            'overall' => 'healthy',
            'checks' => [],
        ];

        // Veritabanı kontrolü
        try {
            DB::connection()->getPdo();
            $health['checks']['database'] = [
                'status' => 'up',
                'message' => 'Veritabanı bağlantısı başarılı',
            ];
        } catch (\Exception $e) {
            $health['overall'] = 'unhealthy';
            $health['checks']['database'] = [
                'status' => 'down',
                'message' => 'Veritabanı bağlantısı başarısız',
            ];
        }

        // Cache kontrolü
        try {
            Cache::put('health_check', true, 10);
            $cacheWorks = Cache::get('health_check') === true;
            Cache::forget('health_check');
            
            $health['checks']['cache'] = [
                'status' => $cacheWorks ? 'up' : 'down',
                'message' => $cacheWorks ? 'Cache sistemi çalışıyor' : 'Cache sistemi çalışmıyor',
            ];
            
            if (!$cacheWorks) {
                $health['overall'] = 'degraded';
            }
        } catch (\Exception $e) {
            $health['overall'] = 'degraded';
            $health['checks']['cache'] = [
                'status' => 'down',
                'message' => 'Cache hatası',
            ];
        }

        // Disk alanı kontrolü
        $diskFree = disk_free_space('/');
        $diskTotal = disk_total_space('/');
        $diskUsedPercent = (($diskTotal - $diskFree) / $diskTotal) * 100;
        
        $health['checks']['disk'] = [
            'status' => $diskUsedPercent < 90 ? 'up' : 'warning',
            'message' => sprintf('Disk kullanımı: %.2f%%', $diskUsedPercent),
            'used_percent' => round($diskUsedPercent, 2),
        ];
        
        if ($diskUsedPercent >= 90) {
            $health['overall'] = 'degraded';
        }

        return $health;
    }

    /**
     * Sistem bilgilerini getir (view için)
     * 
     * @return array
     */
    private function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'os' => PHP_OS,
            'database_driver' => config('database.default'),
            'database_version' => $this->getDatabaseVersion(),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
        ];
    }

    /**
     * Performans metriklerini getir (view için)
     * 
     * @return array
     */
    private function getPerformanceMetrics(): array
    {
        return [
            'memory' => [
                'current' => $this->formatBytes(memory_get_usage(true)),
                'peak' => $this->formatBytes(memory_get_peak_usage(true)),
                'limit' => ini_get('memory_limit'),
            ],
            'database' => [
                'size' => $this->getDatabaseSize(),
                'table_count' => $this->getTableCount(),
            ],
            'disk' => [
                'free' => $this->formatBytes(disk_free_space('/')),
                'total' => $this->formatBytes(disk_total_space('/')),
                'used_percent' => round(((disk_total_space('/') - disk_free_space('/')) / disk_total_space('/')) * 100, 2),
            ],
        ];
    }

    /**
     * Veritabanı versiyonunu getir
     * 
     * @return string
     */
    private function getDatabaseVersion(): string
    {
        try {
            $result = DB::select('SELECT VERSION() as version');
            return $result[0]->version ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Veritabanı boyutunu getir
     * 
     * @return string
     */
    private function getDatabaseSize(): string
    {
        try {
            $database = config('database.connections.' . config('database.default') . '.database');
            
            $result = DB::select("
                SELECT 
                    SUM(data_length + index_length) as size
                FROM information_schema.TABLES 
                WHERE table_schema = ?
            ", [$database]);
            
            $bytes = $result[0]->size ?? 0;
            return $this->formatBytes($bytes);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Tablo sayısını getir
     * 
     * @return int
     */
    private function getTableCount(): int
    {
        try {
            $database = config('database.connections.' . config('database.default') . '.database');
            
            $result = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.TABLES 
                WHERE table_schema = ?
            ", [$database]);
            
            return $result[0]->count ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
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
}
