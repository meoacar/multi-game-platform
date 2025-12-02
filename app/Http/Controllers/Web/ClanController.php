<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Clan;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Web Clan Controller
 * Klan yönetimi (Web arayüzü)
 */
class ClanController extends Controller
{
    /**
     * Klan listesi
     * GET /klanlar
     */
    public function index(Request $request)
    {
        $query = Clan::with(['leader.profile', 'game']);

        // Filtreler
        if ($request->has('game_id') && $request->game_id) {
            $query->where('game_id', $request->game_id);
        }

        if ($request->has('city') && $request->city) {
            $query->where('city', $request->city);
        }

        if ($request->has('verified') && $request->verified) {
            $query->where('is_verified', true);
        }

        // Sıralama
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'members':
                $query->orderBy('member_count', 'desc');
                break;
            case 'verified':
                $query->orderBy('is_verified', 'desc')->latest();
                break;
            default:
                $query->latest();
                break;
        }

        $clans = $query->paginate(20)->withQueryString();

        // Filtre için veriler
        $games = Game::active()->ordered()->get();
        $cities = Clan::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city');

        return view('clans.index', compact('clans', 'games', 'cities'));
    }

    /**
     * Klan detayı
     * GET /klanlar/{slug}
     */
    public function show($slug)
    {
        $clan = Clan::where('slug', $slug)
            ->with(['leader.profile', 'game', 'members.profile'])
            ->firstOrFail();

        // Kullanıcının başvurusu var mı?
        $userApplication = null;
        $isMember = false;
        
        if (auth()->check()) {
            $userApplication = $clan->applications()
                ->where('user_id', auth()->id())
                ->first();
            
            $isMember = $clan->hasMember(auth()->user());
        }

        return view('clans.show', compact('clan', 'userApplication', 'isMember'));
    }

    /**
     * Yeni klan oluşturma formu
     * GET /klanlar/yeni
     */
    public function create()
    {
        $games = Game::active()->ordered()->get();
        
        return view('clans.create', compact('games'));
    }

    /**
     * Yeni klan kaydet
     * POST /klanlar
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'min_rank' => 'nullable|string',
            'max_rank' => 'nullable|string',
            'min_age_range' => 'nullable|string|max:50',
            'max_age_range' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'max_members' => 'nullable|integer|min:5|max:100',
            'discord_invite' => 'nullable|url',
        ]);

        // Slug oluştur
        $validated['slug'] = Str::slug($validated['name']);
        
        // Aynı slug varsa unique yap
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Clan::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $clan = $request->user()->ownedClans()->create($validated);

        // Lideri otomatik üye yap
        $clan->members()->attach($request->user()->id, [
            'role' => 'leader',
            'joined_at' => now(),
        ]);

        $clan->updateMemberCount();

        // XP kazandır
        $request->user()->addXp('clan_create', ['clan_id' => $clan->id]);

        return redirect()->route('clans.show', $clan->slug)
            ->with('success', 'Klan başarıyla oluşturuldu! +25 XP kazandınız!');
    }

    /**
     * Klan düzenleme formu
     * GET /klanlar/{slug}/duzenle
     */
    public function edit($slug)
    {
        $clan = Clan::where('slug', $slug)->firstOrFail();

        // Sadece klan lideri düzenleyebilir
        if ($clan->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        $games = Game::active()->ordered()->get();

        return view('clans.edit', compact('clan', 'games'));
    }

    /**
     * Klan güncelle
     * PUT /klanlar/{slug}
     */
    public function update(Request $request, $slug)
    {
        $clan = Clan::where('slug', $slug)->firstOrFail();

        // Sadece klan lideri güncelleyebilir
        if ($clan->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'min_rank' => 'nullable|string',
            'max_rank' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'max_members' => 'nullable|integer|min:5|max:100',
            'discord_invite' => 'nullable|url',
        ]);

        $clan->update($validated);

        return redirect()->route('clans.show', $clan->slug)
            ->with('success', 'Klan güncellendi!');
    }

    /**
     * Klana başvur
     * POST /klanlar/{slug}/basvur
     */
    public function apply(Request $request, $slug)
    {
        $clan = Clan::where('slug', $slug)->firstOrFail();

        // Klan dolu mu?
        if ($clan->isFull()) {
            return back()->with('error', 'Klan dolu');
        }

        // Zaten üye mi?
        if ($clan->hasMember($request->user())) {
            return back()->with('error', 'Zaten bu klanın üyesisiniz');
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        // Daha önce başvuru yapılmış mı?
        $existingApplication = $clan->applications()
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'Bu klana zaten başvurdunuz');
        }

        $clan->applications()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'] ?? null,
        ]);

        return back()->with('success', 'Başvurunuz gönderildi!');
    }

    /**
     * Klanın başvurularını görüntüle
     * GET /klanlar/{slug}/basvurular
     */
    public function applications($slug)
    {
        $clan = Clan::where('slug', $slug)
            ->with(['leader.profile', 'game'])
            ->firstOrFail();

        // Sadece klan lideri görebilir
        if ($clan->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        $applications = $clan->applications()
            ->with('user.profile')
            ->latest()
            ->get();

        return view('clans.applications', compact('clan', 'applications'));
    }

    /**
     * Başvuruyu kabul et
     * POST /klanlar/basvuru/{id}/kabul
     */
    public function acceptApplication($id)
    {
        $application = \App\Models\ClanApplication::with('clan')->findOrFail($id);

        // Sadece klan lideri kabul edebilir
        if ($application->clan->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        // Klan dolu mu?
        if ($application->clan->isFull()) {
            return back()->with('error', 'Klan dolu');
        }

        $application->accept();

        // Klan liderine XP kazandır
        $application->clan->leader->addXp('clan_member_joined', ['clan_id' => $application->clan_id]);

        return back()->with('success', 'Başvuru kabul edildi ve kullanıcı klana eklendi!');
    }

    /**
     * Başvuruyu reddet
     * POST /klanlar/basvuru/{id}/reddet
     */
    public function rejectApplication($id)
    {
        $application = \App\Models\ClanApplication::with('clan')->findOrFail($id);

        // Sadece klan lideri reddedebilir
        if ($application->clan->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        $application->reject();

        return back()->with('success', 'Başvuru reddedildi!');
    }
}
