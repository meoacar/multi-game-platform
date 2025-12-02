<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

/**
 * Cache Temizleme Komutu
 * 
 * Kullanım:
 * php artisan cache:clear-custom
 * php artisan cache:clear-custom --dashboard
 * php artisan cache:clear-custom --analytics
 * php artisan cache:clear-custom --stats
 * php artisan cache:clear-custom --queries
 */
class CacheClear extends Command
{
    /**
     * Komut imzası
     *
     * @var string
     */
    protected $signature = 'cache:clear-custom
                            {--dashboard : Dashboard cache\'ini temizle}
                            {--analytics : Analytics cache\'ini temizle}
                            {--stats : İstatistik cache\'ini temizle}
                            {--queries : Query cache\'ini temizle}
                            {--all : Tüm cache\'i temizle}';

    /**
     * Komut açıklaması
     *
     * @var string
     */
    protected $description = 'Özel cache kategorilerini temizle';

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
        $this->info('Cache temizleme başlatılıyor...');
        
        $cleared = [];
        
        // Tüm cache'i temizle
        if ($this->option('all')) {
            $this->cacheService->clear();
            $this->info('✓ Tüm cache temizlendi');
            return self::SUCCESS;
        }
        
        // Dashboard cache
        if ($this->option('dashboard')) {
            $this->cacheService->clearDashboard();
            $cleared[] = 'Dashboard';
        }
        
        // Analytics cache
        if ($this->option('analytics')) {
            $this->cacheService->clearAnalytics();
            $cleared[] = 'Analytics';
        }
        
        // Stats cache
        if ($this->option('stats')) {
            $this->cacheService->clearStats();
            $cleared[] = 'İstatistikler';
        }
        
        // Query cache
        if ($this->option('queries')) {
            $this->cacheService->clearQueries();
            $cleared[] = 'Query\'ler';
        }
        
        // Hiçbir seçenek belirtilmemişse
        if (empty($cleared)) {
            $this->warn('Lütfen temizlenecek cache kategorisini belirtin:');
            $this->line('  --dashboard  : Dashboard cache');
            $this->line('  --analytics  : Analytics cache');
            $this->line('  --stats      : İstatistik cache');
            $this->line('  --queries    : Query cache');
            $this->line('  --all        : Tüm cache');
            return self::FAILURE;
        }
        
        $this->info('✓ Temizlenen cache kategorileri: ' . implode(', ', $cleared));
        
        return self::SUCCESS;
    }
}
