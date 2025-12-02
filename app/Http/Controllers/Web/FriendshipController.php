<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Friendship Web Controller
 * Arkadaşlık sistemi web sayfaları
 */
class FriendshipController extends Controller
{
    /**
     * Arkadaş listesi sayfası
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Kabul edilmiş arkadaşlıklar (iki yönlü)
        $friendships = Friendship::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('friend_id', $user->id);
        })
        ->where('status', 'accepted')
        ->with(['user.profile', 'friend.profile'])
        ->get();

        // Arkadaş listesini düzenle (her iki yönden de)
        $friends = $friendships->map(function ($friendship) use ($user) {
            return $friendship->user_id === $user->id 
                ? $friendship->friend 
                : $friendship->user;
        });

        return view('friends.index', compact('friends'));
    }

    /**
     * Arkadaşlık istekleri sayfası
     *
     * @param Request $request
     * @return View
     */
    public function requests(Request $request): View
    {
        $user = $request->user();

        // Gelen istekler
        $incomingRequests = Friendship::where('friend_id', $user->id)
            ->where('status', 'pending')
            ->with(['user.profile'])
            ->get();

        // Giden istekler
        $outgoingRequests = Friendship::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['friend.profile'])
            ->get();

        return view('friends.requests', compact('incomingRequests', 'outgoingRequests'));
    }

    /**
     * Arkadaşlık isteği gönder
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function sendRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $user = $request->user();

        // Kendine istek gönderme kontrolü
        if ($validated['friend_id'] == $user->id) {
            return redirect()->back()->with('error', 'Kendinize arkadaşlık isteği gönderemezsiniz.');
        }

        // Mevcut istek kontrolü (iki yönlü)
        $existing = Friendship::where(function ($query) use ($user, $validated) {
            $query->where('user_id', $user->id)
                  ->where('friend_id', $validated['friend_id']);
        })
        ->orWhere(function ($query) use ($user, $validated) {
            $query->where('user_id', $validated['friend_id'])
                  ->where('friend_id', $user->id);
        })
        ->first();

        if ($existing) {
            if ($existing->status === 'accepted') {
                return redirect()->back()->with('error', 'Bu kullanıcı zaten arkadaşınız.');
            }
            return redirect()->back()->with('error', 'Zaten bir arkadaşlık isteği mevcut.');
        }

        // Arkadaşlık isteği oluştur
        Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $validated['friend_id'],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Arkadaşlık isteği gönderildi.');
    }

    /**
     * Arkadaşlık isteğini kabul et
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function accept(Request $request, int $id): RedirectResponse
    {
        $friendship = Friendship::findOrFail($id);

        // Yetki kontrolü
        if ($friendship->friend_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Bu isteği kabul etme yetkiniz yok.');
        }

        $friendship->accept();

        return redirect()->back()->with('success', 'Arkadaşlık isteği kabul edildi.');
    }

    /**
     * Arkadaşlık isteğini reddet
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function reject(Request $request, int $id): RedirectResponse
    {
        $friendship = Friendship::findOrFail($id);

        // Yetki kontrolü
        if ($friendship->friend_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Bu isteği reddetme yetkiniz yok.');
        }

        $friendship->reject();

        return redirect()->back()->with('success', 'Arkadaşlık isteği reddedildi.');
    }

    /**
     * Arkadaşlığı sil
     *
     * @param Request $request
     * @param int $friendId
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $friendId): RedirectResponse
    {
        $user = $request->user();

        // Arkadaşlığı bul (iki yönlü)
        $friendship = Friendship::where(function ($query) use ($user, $friendId) {
            $query->where('user_id', $user->id)
                  ->where('friend_id', $friendId);
        })
        ->orWhere(function ($query) use ($user, $friendId) {
            $query->where('user_id', $friendId)
                  ->where('friend_id', $user->id);
        })
        ->first();

        if (!$friendship) {
            return redirect()->back()->with('error', 'Arkadaşlık bulunamadı.');
        }

        $friendship->delete();

        return redirect()->back()->with('success', 'Arkadaşlık silindi.');
    }

    /**
     * Kullanıcıyı engelle
     *
     * @param Request $request
     * @param int $friendId
     * @return RedirectResponse
     */
    public function block(Request $request, int $friendId): RedirectResponse
    {
        $user = $request->user();

        // Mevcut arkadaşlığı bul veya oluştur
        $friendship = Friendship::firstOrCreate(
            [
                'user_id' => $user->id,
                'friend_id' => $friendId,
            ],
            [
                'status' => 'blocked',
            ]
        );

        if ($friendship->status !== 'blocked') {
            $friendship->block();
        }

        return redirect()->back()->with('success', 'Kullanıcı engellendi.');
    }
}
