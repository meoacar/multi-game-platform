<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * LogController
 * 
 * Tüm log türlerini yöneten merkezi controller
 * - Admin aktivite logları
 * - Sistem logları (hata, uyarı, bilgi)
 * - Güvenlik logları
 * - Log temizleme işlemleri
 */
class LogController extends Controller
{
    /**
     * Log ana sayfası - Tüm log türlerinin özeti
     */
    public function index()
    {
        // Son 24 saatteki istatistikler
        $stats = [
            'admin_logs' => AdminActivityLog::where('created_at', '>=', now()->subDay())->count(),
            'system_logs' => SystemLog::where('created_at', '>=', now()->subDay())->count(),
            'error_logs' => SystemLog::ofType(SystemLog::TYPE_ERROR)->where('created_at', '>=', now()->subDay())->count(),
            'security_logs' => SystemLog::ofType(SystemLog::TYPE_SECURITY)->where('created_at', '>=', now()->subDay())->count(),
            'critical_logs' => SystemLog::critical()->where('created_at', '>=', now()->subDay())->count(),
        ];

        // Son aktiviteler
        $recentAdminLogs = AdminActivityLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentSystemLogs = SystemLog::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Kritik loglar
        $criticalLogs = SystemLog::critical()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.logs.index', compact('stats', 'recentAdminLogs', 'recentSystemLogs', 'criticalLogs'));
    }

    /**
     * Admin aktivite logları
     */
    public function adminLogs(Request $request)
    {
        $query = AdminActivityLog::with('admin')
            ->orderBy('created_at', 'desc');

        // Admin filtresi
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Aksiyon filtresi
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        // Target type filtresi
        if ($request->filled('target_type')) {
            $query->where('target_type', $request->target_type);
        }

        // Tarih aralığı filtresi
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // IP filtresi
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        $logs = $query->paginate(50);

        // Admin listesi (filtre için)
        $admins = \App\Models\User::where('is_admin', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Target type'lar (filtre için)
        $targetTypes = AdminActivityLog::distinct()
            ->whereNotNull('target_type')
            ->pluck('target_type')
            ->sort();

        return view('admin.logs.admin', compact('logs', 'admins', 'targetTypes'));
    }

    /**
     * Sistem logları
     */
    public function systemLogs(Request $request)
    {
        $query = SystemLog::orderBy('created_at', 'desc');

        // Tür filtresi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Seviye filtresi
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Mesaj araması
        if ($request->filled('message')) {
            $query->where('message', 'like', '%' . $request->message . '%');
        }

        // Tarih aralığı filtresi
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // IP filtresi
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        // Sadece kritik loglar
        if ($request->boolean('critical_only')) {
            $query->critical();
        }

        $logs = $query->paginate(50);

        // İstatistikler
        $stats = [
            'total' => SystemLog::count(),
            'errors' => SystemLog::ofType(SystemLog::TYPE_ERROR)->count(),
            'warnings' => SystemLog::ofType(SystemLog::TYPE_WARNING)->count(),
            'info' => SystemLog::ofType(SystemLog::TYPE_INFO)->count(),
            'security' => SystemLog::ofType(SystemLog::TYPE_SECURITY)->count(),
            'performance' => SystemLog::ofType(SystemLog::TYPE_PERFORMANCE)->count(),
            'critical' => SystemLog::critical()->count(),
        ];

        return view('admin.logs.system', compact('logs', 'stats'));
    }

    /**
     * Güvenlik logları
     */
    public function securityLogs(Request $request)
    {
        $query = SystemLog::security()
            ->orderBy('created_at', 'desc');

        // Seviye filtresi
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Mesaj araması
        if ($request->filled('message')) {
            $query->where('message', 'like', '%' . $request->message . '%');
        }

        // Tarih aralığı filtresi
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // IP filtresi
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        $logs = $query->paginate(50);

        // Şüpheli IP'ler (5'ten fazla güvenlik logu olanlar)
        $suspiciousIps = SystemLog::security()
            ->select('ip_address', DB::raw('count(*) as count'))
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->having('count', '>', 5)
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Son 24 saatteki güvenlik olayları
        $recentCount = SystemLog::security()
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return view('admin.logs.security', compact('logs', 'suspiciousIps', 'recentCount'));
    }

    /**
     * Log detayını göster
     */
    public function show(Request $request, $type, $id)
    {
        if ($type === 'admin') {
            $log = AdminActivityLog::with('admin')->findOrFail($id);
            return view('admin.logs.show-admin', compact('log'));
        } elseif ($type === 'system') {
            $log = SystemLog::findOrFail($id);
            return view('admin.logs.show-system', compact('log'));
        }

        abort(404);
    }

    /**
     * Log temizleme sayfası
     */
    public function cleanupPage()
    {
        // Veritabanı boyutları
        $sizes = [
            'admin_logs' => $this->getTableSize('admin_activity_logs'),
            'system_logs' => $this->getTableSize('system_logs'),
        ];

        // Log sayıları (yaşa göre)
        $counts = [
            'admin' => [
                'total' => AdminActivityLog::count(),
                'last_7_days' => AdminActivityLog::where('created_at', '>=', now()->subDays(7))->count(),
                'last_30_days' => AdminActivityLog::where('created_at', '>=', now()->subDays(30))->count(),
                'last_90_days' => AdminActivityLog::where('created_at', '>=', now()->subDays(90))->count(),
                'older' => AdminActivityLog::where('created_at', '<', now()->subDays(90))->count(),
            ],
            'system' => [
                'total' => SystemLog::count(),
                'last_7_days' => SystemLog::where('created_at', '>=', now()->subDays(7))->count(),
                'last_30_days' => SystemLog::where('created_at', '>=', now()->subDays(30))->count(),
                'last_90_days' => SystemLog::where('created_at', '>=', now()->subDays(90))->count(),
                'older' => SystemLog::where('created_at', '<', now()->subDays(90))->count(),
            ],
        ];

        return view('admin.logs.cleanup', compact('sizes', 'counts'));
    }

    /**
     * Log temizleme işlemi
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'log_type' => 'required|in:admin,system,all',
            'days' => 'required|integer|min:1|max:365',
            'confirm' => 'required|accepted',
        ]);

        $days = $request->input('days');
        $logType = $request->input('log_type');
        $deletedCounts = [];

        DB::beginTransaction();
        try {
            // Admin logları temizle
            if ($logType === 'admin' || $logType === 'all') {
                $deletedCounts['admin'] = AdminActivityLog::where('created_at', '<', now()->subDays($days))
                    ->delete();
            }

            // Sistem logları temizle
            if ($logType === 'system' || $logType === 'all') {
                $deletedCounts['system'] = SystemLog::where('created_at', '<', now()->subDays($days))
                    ->delete();
            }

            // Temizleme işlemini logla
            AdminActivityLog::log('cleanup_logs', null, null, [
                'log_type' => $logType,
                'days' => $days,
                'deleted_counts' => $deletedCounts,
            ]);

            DB::commit();

            $totalDeleted = array_sum($deletedCounts);
            return redirect()
                ->route('admin.logs.cleanup')
                ->with('success', "{$totalDeleted} adet log kaydı başarıyla temizlendi.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            SystemLog::logError('Log temizleme hatası', [
                'error' => $e->getMessage(),
                'log_type' => $logType,
                'days' => $days,
            ], $e->getTraceAsString());

            return redirect()
                ->route('admin.logs.cleanup')
                ->with('error', 'Log temizleme sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Toplu log temizleme (kritik olmayan eski loglar)
     */
    public function bulkCleanup(Request $request)
    {
        $request->validate([
            'confirm' => 'required|accepted',
        ]);

        DB::beginTransaction();
        try {
            $deletedCounts = [];

            // 90 günden eski admin logları
            $deletedCounts['admin_90'] = AdminActivityLog::where('created_at', '<', now()->subDays(90))
                ->delete();

            // 30 günden eski info ve performance logları
            $deletedCounts['system_info'] = SystemLog::whereIn('type', [SystemLog::TYPE_INFO, SystemLog::TYPE_PERFORMANCE])
                ->where('created_at', '<', now()->subDays(30))
                ->delete();

            // 60 günden eski warning logları
            $deletedCounts['system_warning'] = SystemLog::where('type', SystemLog::TYPE_WARNING)
                ->where('created_at', '<', now()->subDays(60))
                ->delete();

            // Kritik loglar korunur (error, security, critical)

            // İşlemi logla
            AdminActivityLog::log('bulk_cleanup_logs', null, null, [
                'deleted_counts' => $deletedCounts,
                'rules' => [
                    'admin_logs' => '90+ gün',
                    'info_performance' => '30+ gün',
                    'warnings' => '60+ gün',
                    'critical_preserved' => 'Korundu',
                ],
            ]);

            DB::commit();

            $totalDeleted = array_sum($deletedCounts);
            return redirect()
                ->route('admin.logs.cleanup')
                ->with('success', "Toplu temizleme tamamlandı. {$totalDeleted} adet log kaydı temizlendi. Kritik loglar korundu.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            SystemLog::logError('Toplu log temizleme hatası', [
                'error' => $e->getMessage(),
            ], $e->getTraceAsString());

            return redirect()
                ->route('admin.logs.cleanup')
                ->with('error', 'Toplu temizleme sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Log export (CSV)
     */
    public function export(Request $request, $type)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if ($type === 'admin') {
            return $this->exportAdminLogs($request);
        } elseif ($type === 'system') {
            return $this->exportSystemLogs($request);
        }

        abort(404);
    }

    /**
     * Admin loglarını export et
     */
    private function exportAdminLogs(Request $request)
    {
        $query = AdminActivityLog::with('admin');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'admin_logs_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM (Excel için)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Başlıklar
            fputcsv($file, ['Tarih', 'Admin', 'Aksiyon', 'Hedef Tipi', 'Hedef ID', 'IP Adresi', 'Detaylar']);

            // Veriler
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->admin->name ?? 'Bilinmiyor',
                    $log->action,
                    $log->target_type ? class_basename($log->target_type) : '',
                    $log->target_id ?? '',
                    $log->ip_address,
                    json_encode($log->details, JSON_UNESCAPED_UNICODE),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sistem loglarını export et
     */
    private function exportSystemLogs(Request $request)
    {
        $query = SystemLog::query();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'system_logs_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM (Excel için)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Başlıklar
            fputcsv($file, ['Tarih', 'Tür', 'Seviye', 'Mesaj', 'IP Adresi', 'URL', 'Context']);

            // Veriler
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->type_name,
                    $log->level_name,
                    $log->message,
                    $log->ip_address,
                    $log->url,
                    json_encode($log->context, JSON_UNESCAPED_UNICODE),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tablo boyutunu hesapla (MB)
     */
    private function getTableSize($tableName)
    {
        $result = DB::select("
            SELECT 
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
            FROM information_schema.TABLES 
            WHERE table_schema = DATABASE()
            AND table_name = ?
        ", [$tableName]);

        return $result[0]->size_mb ?? 0;
    }
}
