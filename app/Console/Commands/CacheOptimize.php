<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

/**
 * Cache Optimizasyon Komutu
 * 
 * Cache'i optimize eder ve Laravel cache'lerini yeniden oluşturur
 * 
 * Kullanım:
 * php artisan cache:optimize-custom
 */
class CacheOptimize extends Command
{
    /**
     * Komut imzası
     *
     * @var string
     */
    protected $signature = 'cache:optimize-custom';

    /**
     * Komut açıklaması
     *
     * @var string
     */
    protected $description = 'Cache\'i optimize et ve Laravel cache\'lerini yeniden oluştur';

    /**
     * Cache Service
     */
    protected CacheService $cacheService;

    /**
     * Constructor
     */
    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Komutu çalıştır
     */
    public function handle(): int
    {
        $this->info('Cache optimizasyonu başlatılıyor...');
        
        $result = $this->cacheService->optimize();
        
        $this->newLine();
        $this->info('✓ Cache optimizasyonu tamamlandı');
        $this->line('Sonuçlar:');
        $this->line('  - Süresi dolmuş cache\'ler temizlendi: ' . $result['cleared_expired']);
        $this->line('  - Config cache: ' . ($result['config_cached'] ? '✓' : '✗'));
        $this->line('  - Route cache: ' . ($result['route_cached'] ? '✓' : '✗'));
        $this->line('  - View cache: ' . ($result['view_cached'] ? '✓' : '✗'));
        
        if (isset($result['error'])) {
            $this->warn('Uyarı: ' . $result['error']);
        }
        
        return self::SUCCESS;
    }
}
