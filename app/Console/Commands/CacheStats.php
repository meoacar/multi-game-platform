<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

/**
 * Cache İstatistikleri Komutu
 * 
 * Cache kullanım istatistiklerini gösterir
 * 
 * Kullanım:
 * php artisan cache:stats
 */
class CacheStats extends Command
{
    /**
     * Komut imzası
     *
     * @var string
     */
    protected $signature = 'cache:stats';

    /**
     * Komut açıklaması
     *
     * @var string
     */
    protected $description = 'Cache kullanım istatistiklerini göster';

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
        $this->info('Cache İstatistikleri');
        $this->line('─────────────────────────────────────');
        
        $stats = $this->cacheService->getStatistics();
        
        $this->table(
            ['Metrik', 'Değer'],
            [
                ['Driver', $stats['driver']],
                ['Toplam Anahtar', $stats['keys_count']],
                ['Boyut', $stats['size']],
                ['Hit Rate', $stats['hit_rate']],
            ]
        );
        
        if (isset($stats['error'])) {
            $this->warn('Uyarı: ' . $stats['error']);
        }
        
        return self::SUCCESS;
    }
}
