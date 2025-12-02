<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\ReportService;
use Illuminate\Http\Request;

/**
 * Analytics Controller
 * 
 * Kullanıcı, içerik ve platform analitiği yönetimi
 */
class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;
    protected ReportService $reportService;

    public function __construct(AnalyticsService $analyticsService, ReportService $reportService)
    {
        $this->analyticsService = $analyticsService;
        $this->reportService = $reportService;
    }

    /**
     * Analitik ana sayfası
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.analytics.index', [
            'pageTitle' => 'Analitik',
        ]);
    }

    /**
     * Kullanıcı analitiği sayfası
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function users(Request $request)
    {
        $dateRange = $request->get('date_range', 'month');
        $customDates = [];

        if ($dateRange === 'custom') {
            $customDates = [
                'start' => $request->get('start_date'),
                'end' => $request->get('end_date'),
            ];
        }

        $analytics = $this->analyticsService->getUserAnalytics($dateRange, $customDates);

        return view('admin.analytics.users', [
            'pageTitle' => 'Kullanıcı Analitiği',
            'analytics' => $analytics,
            'dateRange' => $dateRange,
            'customDates' => $customDates,
        ]);
    }

    /**
     * İçerik analitiği sayfası
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function content(Request $request)
    {
        $dateRange = $request->get('date_range', 'month');
        $customDates = [];

        if ($dateRange === 'custom') {
            $customDates = [
                'start' => $request->get('start_date'),
                'end' => $request->get('end_date'),
            ];
        }

        $analytics = $this->analyticsService->getContentAnalytics($dateRange, $customDates);

        return view('admin.analytics.content', [
            'pageTitle' => 'İçerik Analitiği',
            'analytics' => $analytics,
            'dateRange' => $dateRange,
            'customDates' => $customDates,
        ]);
    }

    /**
     * Platform analitiği sayfası
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function platform(Request $request)
    {
        $dateRange = $request->get('date_range', 'month');
        $customDates = [];

        if ($dateRange === 'custom') {
            $customDates = [
                'start' => $request->get('start_date'),
                'end' => $request->get('end_date'),
            ];
        }

        $analytics = $this->analyticsService->getPlatformAnalytics($dateRange, $customDates);

        return view('admin.analytics.platform', [
            'pageTitle' => 'Platform Analitiği',
            'analytics' => $analytics,
            'dateRange' => $dateRange,
            'customDates' => $customDates,
        ]);
    }

    /**
     * API: Kullanıcı analitiği verilerini getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiUserAnalytics(Request $request)
    {
        $dateRange = $request->get('date_range', 'month');
        $customDates = [];

        if ($dateRange === 'custom') {
            $customDates = [
                'start' => $request->get('start_date'),
                'end' => $request->get('end_date'),
            ];
        }

        $analytics = $this->analyticsService->getUserAnalytics($dateRange, $customDates);

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * API: İçerik analitiği verilerini getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiContentAnalytics(Request $request)
    {
        $dateRange = $request->get('date_range', 'month');
        $customDates = [];

        if ($dateRange === 'custom') {
            $customDates = [
                'start' => $request->get('start_date'),
                'end' => $request->get('end_date'),
            ];
        }

        $analytics = $this->analyticsService->getContentAnalytics($dateRange, $customDates);

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * API: Platform analitiği verilerini getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiPlatformAnalytics(Request $request)
    {
        $dateRange = $request->get('date_range', 'month');
        $customDates = [];

        if ($dateRange === 'custom') {
            $customDates = [
                'start' => $request->get('start_date'),
                'end' => $request->get('end_date'),
            ];
        }

        $analytics = $this->analyticsService->getPlatformAnalytics($dateRange, $customDates);

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Rapor export (PDF)
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:users,content,platform',
            'date_range' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $type = $validated['type'];
        $dateRange = $validated['date_range'] ?? 'month';
        $customDates = [];

        if ($dateRange === 'custom' && isset($validated['start_date']) && isset($validated['end_date'])) {
            $customDates = [
                'start' => $validated['start_date'],
                'end' => $validated['end_date'],
            ];
        }

        try {
            $pdf = $this->reportService->generatePdfReport($type, $dateRange, $customDates);
            
            $filename = sprintf(
                '%s_analitik_raporu_%s.pdf',
                $this->getTypeLabel($type),
                now()->format('Y-m-d_His')
            );
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'PDF oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Rapor export (Excel)
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportExcel(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:users,content,platform',
            'date_range' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $type = $validated['type'];
        $dateRange = $validated['date_range'] ?? 'month';
        $customDates = [];

        if ($dateRange === 'custom' && isset($validated['start_date']) && isset($validated['end_date'])) {
            $customDates = [
                'start' => $validated['start_date'],
                'end' => $validated['end_date'],
            ];
        }

        try {
            return $this->reportService->generateExcelReport($type, $dateRange, $customDates);
        } catch (\Exception $e) {
            return back()->with('error', 'Excel oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Zamanlanmış rapor oluştur
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function scheduleReport(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:users,content,platform,all',
            'format' => 'required|in:pdf,excel,both',
            'frequency' => 'required|in:daily,weekly,monthly',
            'email' => 'required|email',
            'recipients' => 'nullable|array',
            'recipients.*' => 'email',
        ]);

        try {
            $scheduledReport = \App\Models\ScheduledReport::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'format' => $validated['format'],
                'frequency' => $validated['frequency'],
                'email' => $validated['email'],
                'recipients' => $validated['recipients'] ?? null,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            // İlk gönderim zamanını hesapla
            $scheduledReport->calculateNextSendTime();

            return back()->with('success', 'Zamanlanmış rapor başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            return back()->with('error', 'Zamanlanmış rapor oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
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
