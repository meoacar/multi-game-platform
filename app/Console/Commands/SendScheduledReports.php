<?php

namespace App\Console\Commands;

use App\Jobs\SendScheduledReportJob;
use App\Models\ScheduledReport;
use Illuminate\Console\Command;

/**
 * Zamanlanmış Raporları Gönder Komutu
 * 
 * Gönderilmesi gereken zamanlanmış raporları kontrol eder ve gönderir
 */
class SendScheduledReports extends Command
{
    /**
     * Komut imzası
     *
     * @var string
     */
    protected $signature = 'reports:send-scheduled';

    /**
     * Komut açıklaması
     *
     * @var string
     */
    protected $description = 'Zamanlanmış analitik raporlarını kontrol eder ve gönderir';

    /**
     * Komutu çalıştır
     */
    public function handle(): int
    {
        $this->info('Zamanlanmış raporlar kontrol ediliyor...');

        // Gönderilmesi gereken raporları al
        $reports = ScheduledReport::where('is_active', true)
            ->where('next_send_at', '<=', now())
            ->get();

        if ($reports->isEmpty()) {
            $this->info('Gönderilecek rapor bulunamadı.');
            return Command::SUCCESS;
        }

        $this->info(sprintf('%d rapor bulundu. Gönderiliyor...', $reports->count()));

        $successCount = 0;
        $failCount = 0;

        foreach ($reports as $report) {
            try {
                $this->line(sprintf('Rapor gönderiliyor: %s', $report->name));
                
                // Job'ı kuyruğa ekle
                SendScheduledReportJob::dispatch($report);
                
                $successCount++;
                $this->info(sprintf('✓ Rapor kuyruğa eklendi: %s', $report->name));
            } catch (\Exception $e) {
                $failCount++;
                $this->error(sprintf('✗ Rapor gönderilemedi: %s - Hata: %s', $report->name, $e->getMessage()));
            }
        }

        $this->newLine();
        $this->info(sprintf(
            'İşlem tamamlandı. Başarılı: %d, Başarısız: %d',
            $successCount,
            $failCount
        ));

        return Command::SUCCESS;
    }
}
