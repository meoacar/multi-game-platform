<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Platform Analitiği Excel Export
 */
class PlatformAnalyticsExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected array $analytics;
    protected string $dateRange;

    public function __construct(array $analytics, string $dateRange)
    {
        $this->analytics = $analytics;
        $this->dateRange = $dateRange;
    }

    /**
     * Excel'e yazılacak veri
     */
    public function array(): array
    {
        $data = [];
        
        // Genel İstatistikler
        $data[] = ['Genel İstatistikler', ''];
        $data[] = ['Toplam Sayfa Görüntüleme', $this->analytics['total_page_views'] ?? 0];
        $data[] = ['Ortalama Oturum Süresi', ($this->analytics['avg_session_duration'] ?? 0) . ' dakika'];
        $data[] = ['Bounce Rate', ($this->analytics['bounce_rate'] ?? 0) . '%'];
        $data[] = [''];
        
        // Sayfa Görüntüleme Trendi
        if (isset($this->analytics['page_views_trend'])) {
            $data[] = ['Sayfa Görüntüleme Trendi', ''];
            $data[] = ['Tarih', 'Görüntülenme'];
            foreach ($this->analytics['page_views_trend'] as $item) {
                $data[] = [$item['date'], $item['count']];
            }
            $data[] = [''];
        }
        
        // Popüler Sayfalar
        if (isset($this->analytics['popular_pages'])) {
            $data[] = ['Popüler Sayfalar', ''];
            $data[] = ['Sayfa', 'Görüntülenme', 'Yüzde'];
            foreach ($this->analytics['popular_pages'] as $item) {
                $data[] = [
                    $item['page'] ?? 'N/A',
                    $item['views'] ?? 0,
                    ($item['percentage'] ?? 0) . '%'
                ];
            }
            $data[] = [''];
        }
        
        // Cihaz Dağılımı
        if (isset($this->analytics['device_distribution'])) {
            $data[] = ['Cihaz Dağılımı', ''];
            $data[] = ['Cihaz', 'Kullanıcı Sayısı', 'Yüzde'];
            foreach ($this->analytics['device_distribution'] as $item) {
                $data[] = [
                    $item['device'] ?? 'N/A',
                    $item['count'] ?? 0,
                    ($item['percentage'] ?? 0) . '%'
                ];
            }
            $data[] = [''];
        }
        
        // Tarayıcı Dağılımı
        if (isset($this->analytics['browser_distribution'])) {
            $data[] = ['Tarayıcı Dağılımı', ''];
            $data[] = ['Tarayıcı', 'Kullanıcı Sayısı', 'Yüzde'];
            foreach ($this->analytics['browser_distribution'] as $item) {
                $data[] = [
                    $item['browser'] ?? 'N/A',
                    $item['count'] ?? 0,
                    ($item['percentage'] ?? 0) . '%'
                ];
            }
        }
        
        return $data;
    }

    /**
     * Başlıklar
     */
    public function headings(): array
    {
        return ['Platform Analitiği Raporu - ' . $this->dateRange, ''];
    }

    /**
     * Stil ayarları
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            'A' => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Sayfa başlığı
     */
    public function title(): string
    {
        return 'Platform Analitiği';
    }
}
