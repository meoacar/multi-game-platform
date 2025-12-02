<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Admin Push Notification Controller
 * 
 * Push notification yönetimi ve istatistikleri
 */
class PushNotificationController extends Controller
{
    protected FcmService $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Push notification dashboard
     */
    public function index()
    {
        // İstatistikler
        $statistics = [
            'total_users' => User::count(),
            'users_with_token' => User::whereNotNull('fcm_token')->count(),
            'android_users' => User::where('device_type', 'android')->count(),
            'ios_users' => User::where('device_type', 'ios')->count(),
            'web_users' => User::where('device_type', 'web')->count(),
            'active_users_with_token' => User::whereNotNull('fcm_token')
                ->where('last_login_at', '>=', now()->subDays(7))
                ->count(),
            'recent_tokens' => User::whereNotNull('fcm_token')
                ->where('fcm_token_updated_at', '>=', now()->subDays(7))
                ->count(),
        ];

        // Son token güncellemeleri
        $recentTokens = User::whereNotNull('fcm_token')
            ->orderBy('fcm_token_updated_at', 'desc')
            ->take(10)
            ->get(['id', 'name', 'email', 'device_type', 'fcm_token_updated_at']);

        // Cihaz dağılımı
        $deviceDistribution = User::whereNotNull('fcm_token')
            ->select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->get();

        return view('admin.push-notifications.index', compact(
            'statistics',
            'recentTokens',
            'deviceDistribution'
        ));
    }

    /**
     * Test push notification gönderme formu
     */
    public function testForm()
    {
        $users = User::whereNotNull('fcm_token')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'device_type']);

        return view('admin.push-notifications.test', compact('users'));
    }

    /**
     * Test push notification gönder
     */
    public function sendTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
            'image' => 'nullable|url',
            'click_action' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::findOrFail($request->user_id);

            if (!$user->fcm_token) {
                return back()->with('error', 'Bu kullanıcının FCM token\'ı yok!');
            }

            // Push notification gönder
            $sent = $this->fcmService->sendToUser(
                $user,
                $request->title,
                $request->body,
                [
                    'type' => 'test',
                    'sent_by' => auth()->user()->name,
                    'timestamp' => now()->toIso8601String(),
                ],
                $request->image,
                $request->click_action
            );

            if ($sent) {
                // Admin aktivitesini logla
                \App\Models\AdminActivityLog::create([
                    'admin_id' => auth()->id(),
                    'action' => 'send_test_push_notification',
                    'target_type' => 'User',
                    'target_id' => $user->id,
                    'details' => "Test push notification gönderildi: {$request->title}",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return back()->with('success', "Test bildirimi {$user->name} kullanıcısına gönderildi! 🚀");
            } else {
                return back()->with('error', 'Bildirim gönderilemedi!');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    /**
     * Kullanıcı token listesi
     */
    public function tokens(Request $request)
    {
        $query = User::whereNotNull('fcm_token');

        // Filtreler
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('active_only')) {
            $query->where('last_login_at', '>=', now()->subDays(7));
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'fcm_token_updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(20)->withQueryString();

        return view('admin.push-notifications.tokens', compact('users'));
    }

    /**
     * Token silme
     */
    public function deleteToken($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $userName = $user->name;

            $user->update([
                'fcm_token' => null,
                'device_type' => null,
                'fcm_token_updated_at' => null,
            ]);

            // Admin aktivitesini logla
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'delete_fcm_token',
                'target_type' => 'User',
                'target_id' => $user->id,
                'details' => "FCM token silindi: {$userName}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return back()->with('success', "{$userName} kullanıcısının FCM token'ı silindi! 🗑️");
        } catch (\Exception $e) {
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    /**
     * Toplu token temizleme
     */
    public function cleanupTokens(Request $request)
    {
        try {
            $days = $request->input('days', 90);

            // X gün önce güncellenmeyen token'ları temizle
            $count = User::whereNotNull('fcm_token')
                ->where('fcm_token_updated_at', '<', now()->subDays($days))
                ->update([
                    'fcm_token' => null,
                    'device_type' => null,
                    'fcm_token_updated_at' => null,
                ]);

            // Admin aktivitesini logla
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'cleanup_fcm_tokens',
                'target_type' => null,
                'target_id' => null,
                'details' => "{$count} adet eski FCM token temizlendi ({$days} gün)",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->with('success', "{$count} adet eski token temizlendi! 🧹");
        } catch (\Exception $e) {
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    /**
     * İstatistikler (AJAX)
     */
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'users_with_token' => User::whereNotNull('fcm_token')->count(),
            'android_users' => User::where('device_type', 'android')->count(),
            'ios_users' => User::where('device_type', 'ios')->count(),
            'web_users' => User::where('device_type', 'web')->count(),
            'active_users_with_token' => User::whereNotNull('fcm_token')
                ->where('last_login_at', '>=', now()->subDays(7))
                ->count(),
            'recent_tokens' => User::whereNotNull('fcm_token')
                ->where('fcm_token_updated_at', '>=', now()->subDays(7))
                ->count(),
            'token_coverage' => 0,
        ];

        // Token coverage hesapla
        if ($stats['total_users'] > 0) {
            $stats['token_coverage'] = round(
                ($stats['users_with_token'] / $stats['total_users']) * 100,
                2
            );
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
