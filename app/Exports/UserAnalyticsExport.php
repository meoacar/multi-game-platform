<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Kullanıcı Analitiği Excel Export
 */
class UserAnalyticsExport implements FromArray, WithHeadings, WithStyles, WithTitle
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
        $data[] = ['Toplam Kullanıcı', $this->analytics['total_users'] ?? 0];
        $data[] = ['Aktif Kullanıcı', $this->analytics['active_users'] ?? 0];
        $data[] = ['Yeni Kayıtlar', $this->analytics['new_registrations'] ?? 0];
        $data[] = ['Banlı Kullanıcı', $this->analytics['banned_users'] ?? 0];
        $data[] = [''];
        
        // Kayıt Trendi
        if (isset($this->analytics['registration_trend'])) {
            $data[] = ['Kayıt Trendi', ''];
            $data[] = ['Tarih', 'Kayıt Sayısı'];
            foreach ($this->analytics['registration_trend'] as $item) {
                $data[] = [$item['date'], $item['count']];
            }
            $data[] = [''];
        }
        
        // Aktif Kullanıcı Trendi
        if (isset($this->analytics['active_users_trend'])) {
            $data[] = ['Aktif Kullanıcı Trendi', ''];
            $data[] = ['Tarih', 'Aktif Kullanıcı'];
            foreach ($this->analytics['active_users_trend'] as $item) {
                $data[] = [$item['date'], $item['count']];
            }
            $data[] = [''];
        }
        
        // Kullanıcı Segmentasyonu
        if (isset($this->analytics['segmentation'])) {
            $data[] = ['Kullanıcı Segmentasyonu', ''];
            $data[] = ['Segment', 'Kullanıcı Sayısı'];
            foreach ($this->analytics['segmentation'] as $segment => $count) {
                $data[] = [ucfirst($segment), $count];
            }
        }
        
        return $data;
    }

    /**
     * Başlıklar
     */
    public function headings(): array
    {
        return ['Kullanıcı Analitiği Raporu - ' . $this->dateRange, ''];
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
        return 'Kullanıcı Analitiği';
    }
}
