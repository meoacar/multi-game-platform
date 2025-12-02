<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Models\User;
use App\Models\GuidePost;
use App\Models\Clan;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Admin Dashboard Controller
 * Admin panel ana sayfa ve istatistikler
 */
class DashboardController extends Controller
{
    /**
     * Dashboard Service
     */
    protected DashboardService $dashboardService;

    /**
     * Constructor
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Admin dashboard ana sayfa
     * GET /admin
     */
    public function index(Request $request)
    {
        // Tarih aralığı parametresi (today, yesterday, week, month, custom)
        $dateRange = $request->get('date_range', 'today');
        $customDates = [];
        
        if ($dateRange === 'custom') {
            $customDates = [
                'start' => $request->get('start_date', today()->format('Y-m-d')),
                'end' => $request->get('end_date', today()->format('Y-m-d')),
            ];
        }

        // İstatistikleri service'den al
        $statistics = $this->dashboardService->getStatistics($dateRange, $customDates);

        // Grafik verilerini hazırla
        $charts = [
            'registration' => $this->dashboardService->getChartData('registration', 'month'),
            'content' => $this->dashboardService->getChartData('content'),
            'active_users' => $this->dashboardService->getChartData('active_users', 'month'),
            'popular_hours' => $this->dashboardService->getChartData('popular_hours'),
        ];

        // Son aktiviteleri al
        $recent_activities = $this->dashboardService->getRecentActivities(10);

        // Ek veriler (popüler içerikler, kullanıcılar vb.)
        // Eager loading ile N+1 problemini önle
        $top_users = User::with('profile:id,user_id,pubg_id,rank,avatar_path')
            ->select('id', 'name', 'email', 'xp_total', 'status')
            ->where('status', 'active')
            ->orderBy('xp_total', 'desc')
            ->take(5)
            ->get();

        $recent_users = User::with('profile:id,user_id,pubg_id,avatar_path')
            ->select('id', 'name', 'email', 'status', 'created_at')
            ->latest()
            ->take(8)
            ->get();

        $recent_reports = Report::with([
                'reporter:id,name,email',
                'reportable' => function ($query) {
                    // Polymorphic ilişki için sadece gerekli kolonları seç
                    $query->select('id', 'title');
                }
            ])
            ->select('id', 'reporter_id', 'reportable_type', 'reportable_id', 'reason', 'status', 'created_at')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $popular_guides = GuidePost::with('user:id,name,email')
            ->select('id', 'user_id', 'title', 'views_count', 'is_published')
            ->where('is_published', true)
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        $popular_clans = Clan::withCount('members')
            ->select('id', 'name', 'tag', 'user_id')
            ->orderBy('member_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'statistics',
            'charts',
            'recent_activities',
            'top_users',
            'recent_users',
            'recent_reports',
            'popular_guides',
            'popular_clans',
            'dateRange'
        ));
    }

    /**
     * Dashboard istatistiklerini JSON olarak döndür (AJAX için)
     * GET /admin/api/dashboard/stats
     */
    public function getStats(Request $request): JsonResponse
    {
        $dateRange = $request->get('date_range', 'today');
        $customDates = [];
        
        if ($dateRange === 'custom') {
            $customDates = [
                'start' => $request->get('start_date'),
                'end' => $request->get('end_date'),
            ];
        }

        $statistics = $this->dashboardService->getStatistics($dateRange, $customDates);

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Grafik verilerini JSON olarak döndür (AJAX için)
     * GET /admin/api/dashboard/chart/{type}
     */
    public function getChartData(Request $request, string $type): JsonResponse
    {
        $dateRange = $request->get('date_range', 'month');
        $customDates = [];
        
        if ($dateRange === 'custom') {
            $customDates = [
                'start' => $request->get('start_date'),
                'end' => $request->get('end_date'),
            ];
        }

        $chartData = $this->dashboardService->getChartData($type, $dateRange, $customDates);

        return response()->json([
            'success' => true,
            'data' => $chartData,
        ]);
    }

    /**
     * Son aktiviteleri JSON olarak döndür (AJAX için)
     * GET /admin/api/dashboard/activities
     */
    public function getActivities(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $activities = $this->dashboardService->getRecentActivities($limit);

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }
}
