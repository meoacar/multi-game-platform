<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

/**
 * Cache Warm Up Komutu
 * 
 * Sık kullanılan verileri önceden cache'e yükler
 * 
 * Kullanım:
 * php artisan cache:warm-up
 */
class CacheWarmUp extends Command
{
    /**
     * Komut imzası
     *
     * @var string
     */
    protected $signature = 'cache:warm-up';

    /**
     * Komut açıklaması
     *
     * @var string
     */
    protected $description = 'Sık kullanılan verileri cache\'e yükle (warm up)';

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
        $this->info('Cache warm up başlatılıyor...');
        
        $bar = $this->output->createProgressBar(5);
        $bar->start();
        
        $warmed = $this->cacheService->warmUp();
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info('✓ Cache warm up tamamlandı');
        $this->line('Yüklenen cache\'ler:');
        
        foreach ($warmed as $key) {
            $this->line('  - ' . $key);
        }
        
        return self::SUCCESS;
    }
}
