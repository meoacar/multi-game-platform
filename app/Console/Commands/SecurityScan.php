<?php

namespace App\Console\Commands;

use App\Services\SecurityScanService;
use Illuminate\Console\Command;

/**
 * Güvenlik tarama komutu
 * SQL injection, XSS ve CSRF kontrollerini yapar
 */
class SecurityScan extends Command
{
    protected $signature = 'security:scan 
                            {--type= : Tarama tipi (sql, xss, csrf, all)}
                            {--json : JSON formatında çıktı}';

    protected $description = 'Uygulama güvenlik taraması yapar (SQL Injection, XSS, CSRF)';

    protected SecurityScanService $securityService;

    public function __construct(SecurityScanService $securityService)
    {
        parent::__construct();
        $this->securityService = $securityService;
    }

    public function handle(): int
    {
        $type = $this->option('type') ?? 'all';
        $jsonOutput = $this->option('json');

        $this->info('🔒 Güvenlik Taraması Başlatılıyor...');
        $this->newLine();

        $results = match($type) {
            'sql' => ['sql_injection' => $this->securityService->scanSqlInjection()],
            'xss' => ['xss' => $this->securityService->scanXss()],
            'csrf' => ['csrf' => $this->securityService->scanCsrf()],
            default => $this->securityService->runFullScan(),
        };

        if ($jsonOutput) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return Command::SUCCESS;
        }

        $this->displayResults($results);

        return Command::SUCCESS;
    }

    protected function displayResults(array $results): void
    {
        // Özet bilgisi
        if (isset($results['summary'])) {
            $summary = $results['summary'];
            $this->info('📊 TARAMA ÖZETİ');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            
            $statusColor = match($summary['status']) {
                'safe' => 'green',
                'warning' => 'yellow',
                'critical' => 'red',
                default => 'white',
            };
            
            $statusText = match($summary['status']) {
                'safe' => '✅ GÜVENLİ',
                'warning' => '⚠️  UYARI',
                'critical' => '🚨 KRİTİK',
                default => 'BİLİNMİYOR',
            };
            
            $this->line("<fg={$statusColor}>Durum: {$statusText}</>");
            $this->line("Toplam Güvenlik Açığı: {$summary['total_vulnerabilities']}");
            $this->line("Kritik: {$summary['critical']}");
            $this->line("Uyarı: {$summary['warning']}");
            $this->newLine();
        }

        // SQL Injection sonuçları
        if (isset($results['sql_injection'])) {
            $this->displayScanResults('SQL INJECTION', $results['sql_injection']);
        }

        // XSS sonuçları
        if (isset($results['xss'])) {
            $this->displayScanResults('XSS (CROSS-SITE SCRIPTING)', $results['xss']);
        }

        // CSRF sonuçları
        if (isset($results['csrf'])) {
            $this->displayScanResults('CSRF (CROSS-SITE REQUEST FORGERY)', $results['csrf']);
        }

        // Güvenlik önerileri
        $this->newLine();
        $this->displayRecommendations();
    }

    protected function displayScanResults(string $title, array $scanResult): void
    {
        $this->info("🔍 {$title} TARAMASI");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $statusColor = $scanResult['status'] === 'safe' ? 'green' : 'red';
        $statusText = $scanResult['status'] === 'safe' ? '✅ Güvenli' : '⚠️  Güvenlik açığı bulundu';
        
        $this->line("<fg={$statusColor}>{$statusText}</>");
        $this->line("Bulunan Açık Sayısı: {$scanResult['total']}");
        $this->newLine();

        if ($scanResult['total'] > 0) {
            foreach ($scanResult['vulnerabilities'] as $index => $vuln) {
                $severityColor = $vuln['severity'] === 'critical' ? 'red' : 'yellow';
                $severityText = $vuln['severity'] === 'critical' ? 'KRİTİK' : 'UYARI';
                $number = $index + 1;
                
                $this->line("<fg={$severityColor}>#{$number} [{$severityText}]</>");
                
                if (isset($vuln['file'])) {
                    $this->line("  📄 Dosya: {$vuln['file']}");
                    $this->line("  📍 Satır: {$vuln['line']}");
                } elseif (isset($vuln['route'])) {
                    $this->line("  🛣️  Route: {$vuln['route']}");
                    $this->line("  📝 Method: {$vuln['methods']}");
                }
                
                $this->line("  ⚠️  Açıklama: {$vuln['description']}");
                $this->line("  💡 Öneri: {$vuln['recommendation']}");
                $this->newLine();
            }
        }
    }

    protected function displayRecommendations(): void
    {
        $recommendations = $this->securityService->getSecurityRecommendations();
        
        $this->info('💡 GÜVENLİK ÖNERİLERİ');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        foreach ($recommendations as $category => $data) {
            $this->line("<fg=cyan>{$data['title']}</>");
            foreach ($data['recommendations'] as $index => $recommendation) {
                $this->line("  " . ($index + 1) . ". {$recommendation}");
            }
            $this->newLine();
        }
    }
}
