<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $query = CommunityPost::with(['user.profile', 'comments']);

        // Tip filtreleme (intro, general, question, achievement)
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Arama
        if ($request->has('search') && $request->search) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }

        // Sıralama
        switch ($request->get('sort', 'latest')) {
            case 'popular':
                $query->orderBy('likes_count', 'desc');
                break;
            case 'most_commented':
                $query->withCount('comments')->orderBy('comments_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $posts = $query->paginate(10);

        // İstatistikler
        $todayPosts = CommunityPost::whereDate('created_at', today())->count();
        $activeUsers = CommunityPost::distinct('user_id')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->count('user_id');

        return view('community.index', compact('posts', 'todayPosts', 'activeUsers'));
    }

    public function show(CommunityPost $post)
    {
        $post->load(['user.profile', 'comments.user.profile']);

        return view('community.show', compact('post'));
    }

    public function create()
    {
        return view('community.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:intro,general,question,achievement',
            'content' => 'required|string|min:10|max:1000',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['likes_count'] = 0;

        $post = CommunityPost::create($validated);

        // XP kazandır (opsiyonel - XP servisi varsa)
        // auth()->user()->addXp(10, 'created_community_post');

        return redirect()
            ->route('community.show', $post->id)
            ->with('success', 'Gönderi başarıyla oluşturuldu! +10 XP kazandın! 🎉');
    }

    public function destroy(CommunityPost $post)
    {
        if ($post->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Yetkisiz işlem');
        }

        $post->delete();

        return redirect()
            ->route('community.index')
            ->with('success', 'Gönderi silindi!');
    }
}
