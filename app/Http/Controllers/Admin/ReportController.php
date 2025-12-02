<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin Report Controller
 * Şikayet yönetimi - Gelişmiş özellikler ile
 */
class ReportController extends Controller
{
    /**
     * Rapor listesi
     * GET /admin/reports
     */
    public function index(Request $request)
    {
        $query = Report::with(['reporter', 'reportable', 'resolver']);

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tip filtresi
        if ($request->filled('type')) {
            $query->where('reportable_type', $request->type);
        }

        // Öncelik filtresi
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Tarih aralığı filtresi
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Raporlayan kullanıcı filtresi
        if ($request->filled('reporter_id')) {
            $query->where('reporter_id', $request->reporter_id);
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'priority') {
            // Önceliğe göre özel sıralama (high > medium > low)
            $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $reports = $query->paginate(20)->withQueryString();

        // İstatistikler
        $statistics = $this->getStatistics();

        return view('admin.reports.index', compact('reports', 'statistics'));
    }

    /**
     * Rapor detayı
     * GET /admin/reports/{id}
     */
    public function show($id)
    {
        $report = Report::with(['reporter', 'reportable', 'resolver'])
            ->findOrFail($id);

        // Aynı içerik için diğer raporları getir
        $relatedReports = Report::where('reportable_type', $report->reportable_type)
            ->where('reportable_id', $report->reportable_id)
            ->where('id', '!=', $report->id)
            ->with(['reporter'])
            ->latest()
            ->get();

        // Raporlayan kullanıcının geçmiş raporları
        $reporterHistory = Report::where('reporter_id', $report->reporter_id)
            ->where('id', '!=', $report->id)
            ->with(['reportable'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.reports.show', compact('report', 'relatedReports', 'reporterHistory'));
    }

    /**
     * İstatistikler
     * GET /admin/reports/statistics
     */
    public function getStatistics()
    {
        return [
            'total' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'rejected' => Report::where('status', 'rejected')->count(),
            'high_priority' => Report::where('priority', 'high')->where('status', 'pending')->count(),
            'medium_priority' => Report::where('priority', 'medium')->where('status', 'pending')->count(),
            'low_priority' => Report::where('priority', 'low')->where('status', 'pending')->count(),
            'today' => Report::whereDate('created_at', today())->count(),
            'this_week' => Report::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Report::whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Rapor önceliğini güncelle
     * POST /admin/reports/{id}/update-priority
     */
    public function updatePriority(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high',
        ]);

        $report->update([
            'priority' => $validated['priority'],
        ]);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_report_priority',
            'target_type' => 'Report',
            'target_id' => $report->id,
            'details' => "Rapor önceliği güncellendi: {$validated['priority']}",
        ]);

        return back()->with('success', 'Rapor önceliği güncellendi');
    }

    /**
     * Raporu kabul et (içeriği sil)
     * POST /admin/reports/{id}/resolve
     */
    public function resolve(Request $request, $id)
    {
        $report = Report::with('reportable')->findOrFail($id);

        $validated = $request->validate([
            'resolution_note' => 'nullable|string|max:1000',
            'action' => 'required|in:delete_content,ban_user,warn_user,no_action',
        ]);

        DB::beginTransaction();

        try {
            // Raporu çöz
            $report->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolved_by' => auth()->id(),
                'resolution_note' => $validated['resolution_note'] ?? null,
            ]);

            // İşleme göre aksiyon al
            switch ($validated['action']) {
                case 'delete_content':
                    if ($report->reportable) {
                        $report->reportable->delete();
                    }
                    $actionMessage = 'İçerik silindi';
                    break;

                case 'ban_user':
                    if ($report->reportable_type === 'App\Models\User') {
                        $user = $report->reportable;
                        $user->update([
                            'status' => 'banned',
                            'ban_reason' => 'Rapor nedeniyle ban',
                            'banned_at' => now(),
                            'banned_by' => auth()->id(),
                        ]);
                        $actionMessage = 'Kullanıcı banlandı';
                    } else {
                        $actionMessage = 'İçerik sahibi banlanamadı';
                    }
                    break;

                case 'warn_user':
                    // Uyarı sistemi gelecekte eklenebilir
                    $actionMessage = 'Kullanıcı uyarıldı';
                    break;

                case 'no_action':
                    $actionMessage = 'Herhangi bir işlem yapılmadı';
                    break;
            }

            // Admin activity log
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'resolve_report',
                'target_type' => 'Report',
                'target_id' => $report->id,
                'details' => "Rapor çözüldü: {$actionMessage}",
            ]);

            DB::commit();

            return back()->with('success', "Rapor çözüldü. {$actionMessage}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Rapor çözülürken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Raporu reddet
     * POST /admin/reports/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'resolution_note' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status' => 'rejected',
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
            'resolution_note' => $validated['resolution_note'] ?? null,
        ]);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'reject_report',
            'target_type' => 'Report',
            'target_id' => $report->id,
            'details' => "Rapor reddedildi",
        ]);

        return back()->with('success', 'Rapor reddedildi');
    }

    /**
     * Toplu rapor işleme
     * POST /admin/reports/bulk-action
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'report_ids' => 'required|array',
            'report_ids.*' => 'exists:reports,id',
            'action' => 'required|in:resolve,reject,update_priority',
            'priority' => 'required_if:action,update_priority|in:low,medium,high',
            'resolution_note' => 'nullable|string|max:1000',
        ]);

        $successCount = 0;
        $failCount = 0;

        DB::beginTransaction();

        try {
            foreach ($validated['report_ids'] as $reportId) {
                $report = Report::find($reportId);

                if (!$report) {
                    $failCount++;
                    continue;
                }

                switch ($validated['action']) {
                    case 'resolve':
                        $report->update([
                            'status' => 'resolved',
                            'resolved_at' => now(),
                            'resolved_by' => auth()->id(),
                            'resolution_note' => $validated['resolution_note'] ?? null,
                        ]);
                        $successCount++;
                        break;

                    case 'reject':
                        $report->update([
                            'status' => 'rejected',
                            'resolved_at' => now(),
                            'resolved_by' => auth()->id(),
                            'resolution_note' => $validated['resolution_note'] ?? null,
                        ]);
                        $successCount++;
                        break;

                    case 'update_priority':
                        $report->update([
                            'priority' => $validated['priority'],
                        ]);
                        $successCount++;
                        break;
                }
            }

            // Admin activity log
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'bulk_report_action',
                'target_type' => 'Report',
                'target_id' => null,
                'details' => "Toplu işlem: {$validated['action']}, {$successCount} rapor işlendi",
            ]);

            DB::commit();

            $message = "{$successCount} rapor başarıyla işlendi.";
            if ($failCount > 0) {
                $message .= " {$failCount} rapor işlenirken hata oluştu.";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Toplu işlem sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Rapor istatistikleri (API)
     * GET /admin/api/reports/statistics
     */
    public function apiStatistics()
    {
        $statistics = $this->getStatistics();

        // Grafik verileri
        $chartData = [
            'daily' => $this->getDailyReportStats(30),
            'by_type' => $this->getReportsByType(),
            'by_status' => $this->getReportsByStatus(),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
            'charts' => $chartData,
        ]);
    }

    /**
     * Günlük rapor istatistikleri
     */
    private function getDailyReportStats($days = 30)
    {
        $stats = Report::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $stats->map(function ($stat) {
            return [
                'date' => $stat->date,
                'count' => $stat->count,
            ];
        });
    }

    /**
     * Tipe göre rapor dağılımı
     */
    private function getReportsByType()
    {
        $stats = Report::select('reportable_type', DB::raw('COUNT(*) as count'))
            ->groupBy('reportable_type')
            ->get();

        return $stats->map(function ($stat) {
            return [
                'type' => class_basename($stat->reportable_type),
                'count' => $stat->count,
            ];
        });
    }

    /**
     * Duruma göre rapor dağılımı
     */
    private function getReportsByStatus()
    {
        $stats = Report::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return $stats->map(function ($stat) {
            return [
                'status' => $stat->status,
                'count' => $stat->count,
            ];
        });
    }
}
