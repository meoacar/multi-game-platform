<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Friendship;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $friendships = Friendship::where('user_id', auth()->id())
            ->where('status', 'accepted')
            ->with(['friend.profile'])
            ->get();

        return response()->json(['data' => $friendships]);
    }

    public function requests()
    {
        $requests = Friendship::where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->with(['user.profile'])
            ->get();

        return response()->json(['data' => $requests]);
    }

    public function sendRequest(Request $request)
    {
        $validated = $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        if ($validated['friend_id'] == auth()->id()) {
            return response()->json(['message' => 'Kendinize arkadaşlık isteği gönderemezsiniz'], 400);
        }

        $existing = Friendship::where('user_id', auth()->id())
            ->where('friend_id', $validated['friend_id'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Zaten bir istek var'], 400);
        }

        $friendship = Friendship::create([
            'user_id' => auth()->id(),
            'friend_id' => $validated['friend_id'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Arkadaşlık isteği gönderildi',
            'data' => $friendship,
        ], 201);
    }

    public function accept($id)
    {
        $friendship = Friendship::findOrFail($id);

        if ($friendship->friend_id !== auth()->id()) {
            return response()->json(['message' => 'Yetkisiz işlem'], 403);
        }

        $friendship->accept();

        return response()->json(['message' => 'Arkadaşlık isteği kabul edildi']);
    }

    public function reject($id)
    {
        $friendship = Friendship::findOrFail($id);

        if ($friendship->friend_id !== auth()->id()) {
            return response()->json(['message' => 'Yetkisiz işlem'], 403);
        }

        $friendship->reject();

        return response()->json(['message' => 'Arkadaşlık isteği reddedildi']);
    }

    public function destroy($friendId)
    {
        $friendship = Friendship::where('user_id', auth()->id())
            ->where('friend_id', $friendId)
            ->orWhere(function ($query) use ($friendId) {
                $query->where('user_id', $friendId)
                    ->where('friend_id', auth()->id());
            })
            ->first();

        if (!$friendship) {
            return response()->json(['message' => 'Arkadaşlık bulunamadı'], 404);
        }

        $friendship->delete();

        return response()->json(['message' => 'Arkadaşlık silindi']);
    }
}
