<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $messages = Message::where('receiver_id', auth()->id())
            ->orWhere('sender_id', auth()->id())
            ->with(['sender.profile', 'receiver.profile'])
            ->latest()
            ->paginate(20);

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        $validated['sender_id'] = auth()->id();

        $message = Message::create($validated);

        return response()->json([
            'message' => 'Mesaj gönderildi',
            'data' => $message->load(['sender.profile', 'receiver.profile']),
        ], 201);
    }

    public function show($userId)
    {
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

        Message::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['data' => $messages]);
    }

    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);

        if ($message->receiver_id !== auth()->id()) {
            return response()->json(['message' => 'Yetkisiz işlem'], 403);
        }

        $message->markAsRead();

        return response()->json(['message' => 'Mesaj okundu olarak işaretlendi']);
    }

    public function destroy(Message $message)
    {
        if ($message->sender_id !== auth()->id() && $message->receiver_id !== auth()->id()) {
            return response()->json(['message' => 'Yetkisiz işlem'], 403);
        }

        $message->delete();

        return response()->json(['message' => 'Mesaj silindi']);
    }
}
