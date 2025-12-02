<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduledReport;
use Illuminate\Http\Request;

/**
 * Zamanlanmış Rapor Controller
 * 
 * Zamanlanmış raporların yönetimi
 */
class ScheduledReportController extends Controller
{
    /**
     * Zamanlanmış raporları listele
     */
    public function index()
    {
        $scheduledReports = ScheduledReport::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.analytics.scheduled-reports', [
            'pageTitle' => 'Zamanlanmış Raporlar',
            'scheduledReports' => $scheduledReports,
        ]);
    }

    /**
     * Zamanlanmış rapor durumunu değiştir (aktif/pasif)
     */
    public function toggle(ScheduledReport $scheduledReport)
    {
        $scheduledReport->update([
            'is_active' => !$scheduledReport->is_active,
        ]);

        $status = $scheduledReport->is_active ? 'aktif' : 'pasif';
        
        return back()->with('success', "Rapor başarıyla {$status} hale getirildi.");
    }

    /**
     * Zamanlanmış raporu sil
     */
    public function destroy(ScheduledReport $scheduledReport)
    {
        $scheduledReport->delete();

        return back()->with('success', 'Zamanlanmış rapor başarıyla silindi.');
    }
}
