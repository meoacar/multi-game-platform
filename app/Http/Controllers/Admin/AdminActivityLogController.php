<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    /**
     * Admin aktivite loglarını listele
     */
    public function index(Request $request)
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

        return view('admin.logs.index', compact('logs', 'admins', 'targetTypes'));
    }

    /**
     * Log detayını göster
     */
    public function show(AdminActivityLog $log)
    {
        $log->load('admin');

        return view('admin.logs.show', compact('log'));
    }

    /**
     * Eski logları temizle (30 günden eski)
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 30);

        $deleted = AdminActivityLog::where('created_at', '<', now()->subDays($days))
            ->delete();

        AdminActivityLog::log('cleanup_logs', null, null, [
            'days' => $days,
            'deleted_count' => $deleted,
        ]);

        return redirect()
            ->route('admin.logs.index')
            ->with('success', "{$deleted} adet eski log kaydı temizlendi.");
    }
}
