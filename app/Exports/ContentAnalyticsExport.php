<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * İçerik Analitiği Excel Export
 */
class ContentAnalyticsExport implements FromArray, WithHeadings, WithStyles, WithTitle
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
        $data[] = ['Toplam İçerik', $this->analytics['total_content'] ?? 0];
        $data[] = ['LFG İlanları', $this->analytics['lfg_posts'] ?? 0];
        $data[] = ['Klanlar', $this->analytics['clans'] ?? 0];
        $data[] = ['Rehberler', $this->analytics['guides'] ?? 0];
        $data[] = ['Topluluk Gönderileri', $this->analytics['community_posts'] ?? 0];
        $data[] = ['Yorumlar', $this->analytics['comments'] ?? 0];
        $data[] = [''];
        
        // İçerik Oluşturma Trendi
        if (isset($this->analytics['content_creation_trend'])) {
            $data[] = ['İçerik Oluşturma Trendi', ''];
            $data[] = ['Tarih', 'İçerik Sayısı'];
            foreach ($this->analytics['content_creation_trend'] as $item) {
                $data[] = [$item['date'], $item['count']];
            }
            $data[] = [''];
        }
        
        // İçerik Türü Dağılımı
        if (isset($this->analytics['content_type_distribution'])) {
            $data[] = ['İçerik Türü Dağılımı', ''];
            $data[] = ['Tür', 'Sayı', 'Yüzde'];
            foreach ($this->analytics['content_type_distribution'] as $item) {
                $data[] = [$item['type'], $item['count'], $item['percentage'] . '%'];
            }
            $data[] = [''];
        }
        
        // Popüler İçerikler
        if (isset($this->analytics['popular_content'])) {
            $data[] = ['Popüler İçerikler', ''];
            $data[] = ['Başlık', 'Tür', 'Görüntülenme', 'Beğeni'];
            foreach ($this->analytics['popular_content'] as $item) {
                $data[] = [
                    $item['title'] ?? 'N/A',
                    $item['type'] ?? 'N/A',
                    $item['views'] ?? 0,
                    $item['likes'] ?? 0
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
        return ['İçerik Analitiği Raporu - ' . $this->dateRange, ''];
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
        return 'İçerik Analitiği';
    }
}
