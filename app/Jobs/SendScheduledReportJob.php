<?php

namespace App\Jobs;

use App\Models\ScheduledReport;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Zamanlanmış Rapor Gönderme Job'ı
 * 
 * Otomatik olarak oluşturulan raporları email ile gönderir
 */
class SendScheduledReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ScheduledReport $scheduledReport;

    /**
     * Job oluştur
     */
    public function __construct(ScheduledReport $scheduledReport)
    {
        $this->scheduledReport = $scheduledReport;
    }

    /**
     * Job'ı çalıştır
     */
    public function handle(ReportService $reportService): void
    {
        try {
            $attachments = [];
            
            // PDF formatı istenmişse
            if (in_array($this->scheduledReport->format, ['pdf', 'both'])) {
                $pdfPath = $reportService->saveReport(
                    $this->scheduledReport->type,
                    'pdf',
                    $this->getDateRangeForFrequency()
                );
                $attachments[] = [
                    'path' => Storage::path($pdfPath),
                    'name' => basename($pdfPath),
                    'mime' => 'application/pdf',
                ];
            }
            
            // Excel formatı istenmişse
            if (in_array($this->scheduledReport->format, ['excel', 'both'])) {
                $excelPath = $reportService->saveReport(
                    $this->scheduledReport->type,
                    'excel',
                    $this->getDateRangeForFrequency()
                );
                $attachments[] = [
                    'path' => Storage::path($excelPath),
                    'name' => basename($excelPath),
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ];
            }
            
            // Email gönder
            $recipients = $this->scheduledReport->getAllRecipients();
            
            foreach ($recipients as $recipient) {
                Mail::send('emails.scheduled-report', [
                    'reportName' => $this->scheduledReport->name,
                    'reportType' => $this->getTypeLabel($this->scheduledReport->type),
                    'frequency' => $this->getFrequencyLabel($this->scheduledReport->frequency),
                ], function ($message) use ($recipient, $attachments) {
                    $message->to($recipient)
                            ->subject('Zamanlanmış Analitik Raporu - ' . $this->scheduledReport->name);
                    
                    foreach ($attachments as $attachment) {
                        $message->attach($attachment['path'], [
                            'as' => $attachment['name'],
                            'mime' => $attachment['mime'],
                        ]);
                    }
                });
            }
            
            // Gönderim zamanını güncelle
            $this->scheduledReport->update([
                'last_sent_at' => now(),
            ]);
            
            // Sonraki gönderim zamanını hesapla
            $this->scheduledReport->calculateNextSendTime();
            
            // Geçici dosyaları temizle
            foreach ($attachments as $attachment) {
                if (file_exists($attachment['path'])) {
                    unlink($attachment['path']);
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Zamanlanmış rapor gönderilirken hata oluştu: ' . $e->getMessage(), [
                'scheduled_report_id' => $this->scheduledReport->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Frekansa göre tarih aralığını al
     */
    protected function getDateRangeForFrequency(): string
    {
        return match($this->scheduledReport->frequency) {
            'daily' => 'yesterday',
            'weekly' => 'week',
            'monthly' => 'month',
            default => 'month',
        };
    }

    /**
     * Tip etiketini al
     */
    protected function getTypeLabel(string $type): string
    {
        return match($type) {
            'users' => 'Kullanıcı Analitiği',
            'content' => 'İçerik Analitiği',
            'platform' => 'Platform Analitiği',
            'all' => 'Tüm Analitikler',
            default => 'Genel Analitik',
        };
    }

    /**
     * Frekans etiketini al
     */
    protected function getFrequencyLabel(string $frequency): string
    {
        return match($frequency) {
            'daily' => 'Günlük',
            'weekly' => 'Haftalık',
            'monthly' => 'Aylık',
            default => 'Periyodik',
        };
    }
}
