<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    // Mesaj listesi (konuşmalar)
    public function index()
    {
        $userId = auth()->id();

        // Kullanıcının tüm konuşmalarını getir
        $conversations = Message::select('sender_id', 'receiver_id', DB::raw('MAX(created_at) as last_message_at'))
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->groupBy('sender_id', 'receiver_id')
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($item) use ($userId) {
                $otherUserId = $item->sender_id == $userId ? $item->receiver_id : $item->sender_id;
                return [
                    'user_id' => $otherUserId,
                    'last_message_at' => $item->last_message_at,
                ];
            })
            ->unique('user_id')
            ->values();

        // Kullanıcı bilgilerini yükle
        $userIds = $conversations->pluck('user_id');
        $users = User::with('profile')->whereIn('id', $userIds)->get()->keyBy('id');

        $conversations = $conversations->map(function ($conv) use ($users, $userId) {
            $user = $users->get($conv['user_id']);
            
            // Okunmamış mesaj sayısı
            $unreadCount = Message::where('sender_id', $conv['user_id'])
                ->where('receiver_id', $userId)
                ->where('is_read', false)
                ->count();

            return [
                'user' => $user,
                'last_message_at' => $conv['last_message_at'],
                'unread_count' => $unreadCount,
            ];
        });

        return view('messages.index', compact('conversations'));
    }

    // Belirli bir kullanıcıyla konuşma
    public function show($userId)
    {
        $otherUser = User::with('profile')->findOrFail($userId);
        
        $messages = Message::where(function ($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', auth()->id());
        })
            ->with(['sender.profile', 'receiver.profile'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Okunmamış mesajları okundu olarak işaretle
        Message::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('messages.show', compact('otherUser', 'messages'));
    }

    // Mesaj gönderme
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        $validated['sender_id'] = auth()->id();

        Message::create($validated);

        return redirect()
            ->route('messages.show', $validated['receiver_id'])
            ->with('success', 'Mesaj gönderildi!');
    }
}
