<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ModerationService;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Moderasyon Controller
 * 
 * Moderasyon kuyruğu, rapor işleme ve otomatik moderasyon kuralları yönetimi
 */
class ModerationController extends Controller
{
    protected ModerationService $moderationService;

    public function __construct(ModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    /**
     * Moderasyon kuyruğu ana sayfası
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Cache'den kuyruk verilerini al (5 dakika)
        $queue = Cache::remember('moderation.queue', 300, function () {
            return $this->moderationService->getQueue();
        });

        return view('admin.moderation.index', [
            'queue' => $queue,
            'pageTitle' => 'Moderasyon Kuyruğu',
        ]);
    }

    /**
     * Bekleyen raporlar sayfası
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function reports(Request $request)
    {
        $query = Report::with(['reporter:id,name,email', 'reportable'])
            ->where('status', 'pending');

        // Filtreleme
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('type')) {
            $query->where('reportable_type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'priority');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'priority') {
            $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $reports = $query->paginate(20);

        return view('admin.moderation.reports', [
            'reports' => $reports,
            'pageTitle' => 'Bekleyen Raporlar',
            'filters' => $request->all(),
        ]);
    }

    /**
     * Rapor işleme
     * 
     * @param Request $request
     * @param int $reportId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processReport(Request $request, int $reportId)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'nullable|string|max:1000',
        ]);

        $result = $this->moderationService->processReport(
            $reportId,
            $request->action,
            $request->reason
        );

        // Cache'i temizle
        Cache::forget('moderation.queue');

        if ($result['success']) {
            return redirect()
                ->back()
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    /**
     * Toplu rapor işleme
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkProcess(Request $request)
    {
        $request->validate([
            'report_ids' => 'required|array',
            'report_ids.*' => 'exists:reports,id',
            'action' => 'required|in:approve,reject',
            'reason' => 'nullable|string|max:1000',
        ]);

        $successCount = 0;
        $failCount = 0;

        foreach ($request->report_ids as $reportId) {
            $result = $this->moderationService->processReport(
                $reportId,
                $request->action,
                $request->reason
            );

            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        // Cache'i temizle
        Cache::forget('moderation.queue');

        $message = "{$successCount} rapor başarıyla işlendi.";
        if ($failCount > 0) {
            $message .= " {$failCount} rapor işlenirken hata oluştu.";
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Otomatik moderasyon kuralları sayfası
     * 
     * @return \Illuminate\View\View
     */
    public function rules()
    {
        // Mevcut kuralları getir (şimdilik hardcoded, gelecekte veritabanından)
        $rules = [
            [
                'id' => 1,
                'name' => 'Küfür Filtresi',
                'type' => 'profanity',
                'action' => 'reject',
                'is_active' => true,
                'description' => 'Uygunsuz kelimeler içeren içerikleri otomatik olarak reddeder',
            ],
            [
                'id' => 2,
                'name' => 'Link Spam Kontrolü',
                'type' => 'link_spam',
                'action' => 'review',
                'is_active' => true,
                'description' => '3\'ten fazla link içeren içerikleri incelemeye alır',
            ],
            [
                'id' => 3,
                'name' => 'Tekrarlayan İçerik',
                'type' => 'duplicate',
                'action' => 'review',
                'is_active' => true,
                'description' => 'Aynı kullanıcının kısa sürede benzer içerik paylaşmasını engeller',
            ],
            [
                'id' => 4,
                'name' => 'Otomatik Ban (3 Rapor)',
                'type' => 'auto_ban',
                'action' => 'ban',
                'is_active' => true,
                'description' => '3 onaylı rapor alan kullanıcıları otomatik olarak banlar',
            ],
        ];

        // Küfür kelime listesi (örnek)
        $badWords = [
            'küfür1', 'küfür2', 'hakaret1', 'hakaret2',
            // Gerçek implementasyonda daha kapsamlı liste
        ];

        return view('admin.moderation.rules', [
            'rules' => $rules,
            'badWords' => $badWords,
            'pageTitle' => 'Otomatik Moderasyon Kuralları',
        ]);
    }

    /**
     * Kural durumunu değiştir (aktif/pasif)
     * 
     * @param Request $request
     * @param int $ruleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleRule(Request $request, int $ruleId)
    {
        // Şimdilik basit bir response, gelecekte veritabanı güncellemesi yapılacak
        return response()->json([
            'success' => true,
            'message' => 'Kural durumu güncellendi',
        ]);
    }

    /**
     * Küfür kelimesi ekle
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addBadWord(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:100',
        ]);

        // Şimdilik sadece mesaj döndür, gelecekte veritabanına kaydedilecek
        return redirect()
            ->back()
            ->with('success', 'Kelime kara listeye eklendi');
    }

    /**
     * Küfür kelimesi sil
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeBadWord(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:100',
        ]);

        // Şimdilik sadece mesaj döndür, gelecekte veritabanından silinecek
        return redirect()
            ->back()
            ->with('success', 'Kelime kara listeden çıkarıldı');
    }

    /**
     * İçerik önizleme ve otomatik moderasyon testi
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testContent(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $result = $this->moderationService->autoModerate($request->content);

        return response()->json($result);
    }

    /**
     * Şüpheli aktiviteler sayfası
     * 
     * @return \Illuminate\View\View
     */
    public function suspicious()
    {
        $queue = $this->moderationService->getQueue();
        $activities = $queue['suspicious_activities'];

        return view('admin.moderation.suspicious', [
            'activities' => $activities,
            'pageTitle' => 'Şüpheli Aktiviteler',
        ]);
    }

    /**
     * API: Kuyruk verilerini getir (AJAX için)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiGetQueue()
    {
        $queue = $this->moderationService->getQueue();

        return response()->json([
            'success' => true,
            'data' => $queue,
        ]);
    }

    /**
     * API: Rapor işle (AJAX için)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiProcessReport(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'action' => 'required|in:approve,reject',
            'reason' => 'nullable|string|max:1000',
        ]);

        $result = $this->moderationService->processReport(
            $request->report_id,
            $request->action,
            $request->reason
        );

        // Cache'i temizle
        Cache::forget('moderation.queue');

        return response()->json($result);
    }
}
