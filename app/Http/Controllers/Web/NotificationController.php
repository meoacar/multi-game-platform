<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Notification Web Controller
 * Bildirim yönetimi web sayfaları
 */
class NotificationController extends Controller
{
    /**
     * Bildirim listesi sayfası
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'all'); // all, unread, read

        $query = $request->user()->notifications();

        if ($filter === 'unread') {
            $query = $request->user()->unreadNotifications();
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(20);
        $unreadCount = $request->user()->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    /**
     * Bildirimi okundu olarak işaretle
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return redirect()->back()->with('error', 'Bildirim bulunamadı.');
        }

        $notification->markAsRead();

        // Bildirim içindeki URL varsa oraya yönlendir
        $url = $notification->data['url'] ?? route('notifications.index');

        return redirect($url);
    }

    /**
     * Tüm bildirimleri okundu olarak işaretle
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Tüm bildirimler okundu olarak işaretlendi.');
    }

    /**
     * Bildirimi sil
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return redirect()->back()->with('error', 'Bildirim bulunamadı.');
        }

        $notification->delete();

        return redirect()->back()->with('success', 'Bildirim silindi.');
    }

    /**
     * Tüm bildirimleri sil
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyAll(Request $request): RedirectResponse
    {
        $request->user()
            ->notifications()
            ->delete();

        return redirect()->back()->with('success', 'Tüm bildirimler silindi.');
    }
}
