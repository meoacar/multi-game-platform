<?php

namespace App\Services;

use App\Exports\UserAnalyticsExport;
use App\Exports\ContentAnalyticsExport;
use App\Exports\PlatformAnalyticsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

/**
 * Rapor Servisi
 * 
 * PDF ve Excel formatında rapor oluşturma işlemlerini yönetir
 */
class ReportService
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * PDF rapor oluştur
     * 
     * @param string $type Rapor tipi (users, content, platform)
     * @param string $dateRange Tarih aralığı
     * @param array $customDates Özel tarih aralığı
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePdfReport(string $type, string $dateRange = 'month', array $customDates = [])
    {
        // Analitik verilerini al
        $analytics = $this->getAnalyticsData($type, $dateRange, $customDates);
        
        // View'ı seç
        $view = match($type) {
            'users' => 'admin.reports.pdf.users',
            'content' => 'admin.reports.pdf.content',
            'platform' => 'admin.reports.pdf.platform',
            default => 'admin.reports.pdf.users',
        };
        
        // PDF oluştur
        $pdf = Pdf::loadView($view, [
            'analytics' => $analytics,
            'dateRange' => $this->getDateRangeLabel($dateRange),
            'generatedAt' => now()->format('d.m.Y H:i'),
        ]);
        
        // PDF ayarları
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf;
    }

    /**
     * Excel rapor oluştur
     * 
     * @param string $type Rapor tipi (users, content, platform)
     * @param string $dateRange Tarih aralığı
     * @param array $customDates Özel tarih aralığı
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function generateExcelReport(string $type, string $dateRange = 'month', array $customDates = [])
    {
        // Analitik verilerini al
        $analytics = $this->getAnalyticsData($type, $dateRange, $customDates);
        
        // Export sınıfını seç
        $export = match($type) {
            'users' => new UserAnalyticsExport($analytics, $this->getDateRangeLabel($dateRange)),
            'content' => new ContentAnalyticsExport($analytics, $this->getDateRangeLabel($dateRange)),
            'platform' => new PlatformAnalyticsExport($analytics, $this->getDateRangeLabel($dateRange)),
            default => new UserAnalyticsExport($analytics, $this->getDateRangeLabel($dateRange)),
        };
        
        // Dosya adı
        $filename = sprintf(
            '%s_analitik_raporu_%s.xlsx',
            $this->getTypeLabel($type),
            now()->format('Y-m-d_His')
        );
        
        return Excel::download($export, $filename);
    }

    /**
     * Raporu dosya olarak kaydet (zamanlanmış raporlar için)
     * 
     * @param string $type Rapor tipi
     * @param string $format Format (pdf, excel)
     * @param string $dateRange Tarih aralığı
     * @param array $customDates Özel tarih aralığı
     * @return string Dosya yolu
     */
    public function saveReport(string $type, string $format, string $dateRange = 'month', array $customDates = []): string
    {
        $filename = sprintf(
            'reports/%s_%s_%s.%s',
            $type,
            $dateRange,
            now()->format('Y-m-d_His'),
            $format === 'pdf' ? 'pdf' : 'xlsx'
        );
        
        if ($format === 'pdf') {
            $pdf = $this->generatePdfReport($type, $dateRange, $customDates);
            Storage::put($filename, $pdf->output());
        } else {
            $analytics = $this->getAnalyticsData($type, $dateRange, $customDates);
            $export = match($type) {
                'users' => new UserAnalyticsExport($analytics, $this->getDateRangeLabel($dateRange)),
                'content' => new ContentAnalyticsExport($analytics, $this->getDateRangeLabel($dateRange)),
                'platform' => new PlatformAnalyticsExport($analytics, $this->getDateRangeLabel($dateRange)),
                default => new UserAnalyticsExport($analytics, $this->getDateRangeLabel($dateRange)),
            };
            
            Excel::store($export, $filename, 'local');
        }
        
        return $filename;
    }

    /**
     * Analitik verilerini al
     */
    protected function getAnalyticsData(string $type, string $dateRange, array $customDates): array
    {
        return match($type) {
            'users' => $this->analyticsService->getUserAnalytics($dateRange, $customDates),
            'content' => $this->analyticsService->getContentAnalytics($dateRange, $customDates),
            'platform' => $this->analyticsService->getPlatformAnalytics($dateRange, $customDates),
            default => [],
        };
    }

    /**
     * Tarih aralığı etiketini al
     */
    protected function getDateRangeLabel(string $dateRange): string
    {
        return match($dateRange) {
            'today' => 'Bugün',
            'yesterday' => 'Dün',
            'week' => 'Son 7 Gün',
            'month' => 'Son 30 Gün',
            'year' => 'Son 1 Yıl',
            'custom' => 'Özel Tarih Aralığı',
            default => 'Son 30 Gün',
        };
    }

    /**
     * Tip etiketini al
     */
    protected function getTypeLabel(string $type): string
    {
        return match($type) {
            'users' => 'kullanici',
            'content' => 'icerik',
            'platform' => 'platform',
            default => 'genel',
        };
    }
}
