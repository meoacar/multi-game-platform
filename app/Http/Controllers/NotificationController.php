<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Bildirim Controller
 * 
 * Unified notification center - Tüm oyunlardan bildirimleri gösterir
 */
class NotificationController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Bildirim listesi (Unified - tüm oyunlardan)
     * 
     * Cross-game özellik: Kullanıcının tüm oyunlardaki bildirimlerini gösterir
     * 
     * Requirement 14.4: Unified notification system across all games
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Filter parametresi
        $filter = $request->get('filter', 'all');
        
        // Tüm bildirimleri al (game_id'ye bakmaksızın - cross-game)
        $query = $user->notifications();
        
        // Filter uygula
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }
        
        // Oyuna göre filtrele (opsiyonel)
        $gameFilter = $request->get('game');
        if ($gameFilter && $gameFilter !== 'all') {
            if ($gameFilter === 'platform') {
                // Platform geneli bildirimler (game_id null olanlar)
                $query->whereNull('game_id');
            } else {
                // Belirli bir oyunun bildirimleri
                $query->where('game_id', $gameFilter);
            }
        }
        
        // Sayfalama ile al
        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Okunmamış sayısı
        $unreadCount = $user->unreadNotifications()->count();
        
        // Oyun bazlı istatistikler
        $gameStats = DB::table('notifications')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->selectRaw('
                game_id, 
                COUNT(*) as count, 
                SUM(CASE WHEN read_at IS NULL THEN 1 ELSE 0 END) as unread_count
            ')
            ->groupBy('game_id')
            ->get()
            ->keyBy('game_id');
        
        // Aktif oyunları al (filtreleme için)
        $games = \App\Models\Game::active()->get();
        
        return view('notifications.index', compact(
            'notifications',
            'unreadCount',
            'gameStats',
            'filter',
            'games',
            'gameFilter'
        ));
    }

    /**
     * Bildirimi okundu olarak işaretle
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Bildirim okundu olarak işaretlendi'
        ]);
    }

    /**
     * Tüm bildirimleri okundu olarak işaretle
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Tüm bildirimler okundu olarak işaretlendi'
        ]);
    }

    /**
     * Bildirimi sil
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Bildirim silindi'
        ]);
    }

    /**
     * Tüm bildirimleri sil
     */
    public function destroyAll()
    {
        $user = Auth::user();
        $user->notifications()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Tüm bildirimler silindi'
        ]);
    }

    /**
     * Oyuna özel bildirimleri al (API endpoint)
     */
    public function getByGame($gameId)
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->where('game_id', $gameId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }
}
