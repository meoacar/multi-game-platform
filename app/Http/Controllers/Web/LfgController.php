<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LfgPost;
use App\Models\Game;
use Illuminate\Http\Request;

/**
 * Web LFG Controller
 * Takım arama ilanları (Web arayüzü)
 */
class LfgController extends Controller
{
    /**
     * İlan listesi
     * GET /ilanlar
     */
    public function index(Request $request)
    {
        $query = LfgPost::with(['user.profile', 'game'])
            ->open();

        // Filtreler
        if ($request->has('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('play_style')) {
            $query->where('play_style_tag', $request->play_style);
        }

        if ($request->has('microphone_required')) {
            $query->where('microphone_required', true);
        }

        $posts = $query->latest()->paginate(20);

        // Filtre için veriler
        $games = Game::active()->ordered()->get();
        $cities = LfgPost::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city');

        return view('lfg.index', compact('posts', 'games', 'cities'));
    }

    /**
     * İlan detayı
     * GET /ilanlar/{id}
     */
    public function show($id)
    {
        $post = LfgPost::with(['user.profile', 'game', 'applications.user.profile'])
            ->findOrFail($id);

        // Görüntülenme sayısını artır
        $post->incrementViews();

        // Kullanıcının başvurusu var mı?
        $userApplication = null;
        if (auth()->check()) {
            $userApplication = $post->applications()
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('lfg.show', compact('post', 'userApplication'));
    }

    /**
     * Yeni ilan oluşturma formu
     * GET /ilanlar/yeni
     */
    public function create()
    {
        $games = Game::active()->ordered()->get();
        
        return view('lfg.create', compact('games'));
    }

    /**
     * Yeni ilan kaydet
     * POST /ilanlar
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'min_rank' => 'nullable|string',
            'max_rank' => 'nullable|string',
            'mode' => 'nullable|string|max:100',
            'microphone_required' => 'nullable|boolean',
            'min_age_range' => 'nullable|string|max:50',
            'max_age_range' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'play_style_tag' => 'nullable|string|in:try-hard,chill,fun-first,competitive,casual',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Checkbox değerini düzelt
        $validated['microphone_required'] = $request->has('microphone_required') ? true : false;

        $post = $request->user()->lfgPosts()->create($validated);

        // XP kazandır
        $request->user()->addXp('lfg_post_create', ['lfg_post_id' => $post->id]);

        return redirect()->route('lfg.show', $post->id)
            ->with('success', 'İlan başarıyla oluşturuldu! +10 XP kazandınız!');
    }

    /**
     * İlan düzenleme formu
     * GET /ilanlar/{id}/duzenle
     */
    public function edit($id)
    {
        $post = LfgPost::findOrFail($id);

        // Sadece ilan sahibi düzenleyebilir
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        $games = Game::active()->ordered()->get();

        return view('lfg.edit', compact('post', 'games'));
    }

    /**
     * İlan güncelle
     * PUT /ilanlar/{id}
     */
    public function update(Request $request, $id)
    {
        $post = LfgPost::findOrFail($id);

        // Sadece ilan sahibi güncelleyebilir
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'min_rank' => 'nullable|string',
            'max_rank' => 'nullable|string',
            'mode' => 'nullable|string|max:100',
            'microphone_required' => 'boolean',
            'city' => 'nullable|string|max:255',
            'play_style_tag' => 'nullable|string|in:try-hard,chill,fun-first,competitive,casual',
            'status' => 'sometimes|in:open,closed',
        ]);

        $post->update($validated);

        return redirect()->route('lfg.show', $post->id)
            ->with('success', 'İlan güncellendi!');
    }

    /**
     * İlan sil
     * DELETE /ilanlar/{id}
     */
    public function destroy($id)
    {
        $post = LfgPost::findOrFail($id);

        // Sadece ilan sahibi silebilir
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        $post->delete();

        return redirect()->route('lfg.index')
            ->with('success', 'İlan silindi!');
    }

    /**
     * İlana başvur
     * POST /ilanlar/{id}/basvur
     */
    public function apply(Request $request, $id)
    {
        $post = LfgPost::findOrFail($id);

        // İlan kapalıysa başvuru yapılamaz
        if ($post->isClosed()) {
            return back()->with('error', 'Bu ilan kapalı');
        }

        // Kendi ilanına başvuramaz
        if ($post->user_id === auth()->id()) {
            return back()->with('error', 'Kendi ilanınıza başvuramazsınız');
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        // Daha önce başvuru yapılmış mı?
        $existingApplication = $post->applications()
            ->where('user_id', auth()->id())
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'Bu ilana zaten başvurdunuz');
        }

        $post->applications()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'] ?? null,
        ]);

        return back()->with('success', 'Başvurunuz gönderildi!');
    }

    /**
     * İlanın başvurularını görüntüle
     * GET /ilanlar/{id}/basvurular
     */
    public function applications($id)
    {
        $post = LfgPost::with(['user.profile', 'game'])->findOrFail($id);

        // Sadece ilan sahibi görebilir
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        $applications = $post->applications()
            ->with('user.profile')
            ->latest()
            ->get();

        return view('lfg.applications', compact('post', 'applications'));
    }

    /**
     * Başvuruyu kabul et
     * POST /ilanlar/basvuru/{id}/kabul
     */
    public function acceptApplication($id)
    {
        $application = \App\Models\LfgApplication::with('lfgPost')->findOrFail($id);

        // Sadece ilan sahibi kabul edebilir
        if ($application->lfgPost->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        $application->accept();

        // Başvuran kullanıcıya XP kazandır
        $application->user->addXp('lfg_application_accepted', ['lfg_post_id' => $application->lfg_post_id]);

        return back()->with('success', 'Başvuru kabul edildi!');
    }

    /**
     * Başvuruyu reddet
     * POST /ilanlar/basvuru/{id}/reddet
     */
    public function rejectApplication($id)
    {
        $application = \App\Models\LfgApplication::with('lfgPost')->findOrFail($id);

        // Sadece ilan sahibi reddedebilir
        if ($application->lfgPost->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        $application->reject();

        return back()->with('success', 'Başvuru reddedildi!');
    }
}
