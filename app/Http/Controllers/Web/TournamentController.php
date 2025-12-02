<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

/**
 * Tournament Web Controller
 * Turnuva yönetimi web sayfaları
 */
class TournamentController extends Controller
{
    /**
     * Turnuva listesi sayfası
     */
    public function index(Request $request): View
    {
        $query = Tournament::with(['organizer.profile', 'game', 'teams']);

        // Filtreleme
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        $tournaments = $query->latest()->paginate(12);
        $games = Game::where('is_active', true)->get();

        return view('tournaments.index', compact('tournaments', 'games'));
    }

    /**
     * Turnuva detay sayfası
     */
    public function show(string $slug): View
    {
        $tournament = Tournament::with(['organizer.profile', 'game', 'teams.captain.profile'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('tournaments.show', compact('tournament'));
    }

    /**
     * Yeni turnuva oluşturma formu
     */
    public function create(): View
    {
        $games = Game::where('is_active', true)->get();
        return view('tournaments.create', compact('games'));
    }

    /**
     * Turnuva kaydet
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'description' => 'required|string',
            'rules' => 'required|string',
            'prize_pool' => 'nullable|string|max:255',
            'max_teams' => 'required|integer|min:2|max:64',
            'team_size' => 'required|integer|min:1|max:10',
            'registration_starts_at' => 'required|date',
            'registration_ends_at' => 'required|date|after:registration_starts_at',
            'tournament_starts_at' => 'required|date|after:registration_ends_at',
            'tournament_ends_at' => 'required|date|after:tournament_starts_at',
        ]);

        $validated['organizer_id'] = $request->user()->id;
        $validated['slug'] = Str::slug($validated['name']);
        $validated['status'] = 'upcoming';

        $tournament = Tournament::create($validated);

        // XP ekle
        $request->user()->addXp('created_tournament');

        return redirect()->route('tournaments.show', $tournament->slug)
            ->with('success', 'Turnuva başarıyla oluşturuldu!');
    }

    /**
     * Turnuvaya kayıt sayfası
     */
    public function register(Request $request, string $slug): View|RedirectResponse
    {
        $tournament = Tournament::where('slug', $slug)->firstOrFail();

        // Durum kontrolü
        if ($tournament->status !== 'registration_open') {
            return redirect()->route('tournaments.show', $slug)
                ->with('error', 'Kayıtlar açık değil.');
        }

        // Kapasite kontrolü
        if ($tournament->teams()->count() >= $tournament->max_teams) {
            return redirect()->route('tournaments.show', $slug)
                ->with('error', 'Turnuva dolu.');
        }

        return view('tournaments.register', compact('tournament'));
    }

    /**
     * Turnuvaya kayıt işlemi
     */
    public function storeRegistration(Request $request, string $slug): RedirectResponse
    {
        $tournament = Tournament::where('slug', $slug)->firstOrFail();

        // Durum kontrolü
        if ($tournament->status !== 'registration_open') {
            return redirect()->back()->with('error', 'Kayıtlar açık değil.');
        }

        // Kapasite kontrolü
        if ($tournament->teams()->count() >= $tournament->max_teams) {
            return redirect()->back()->with('error', 'Turnuva dolu.');
        }

        $validated = $request->validate([
            'team_name' => 'required|string|max:255',
            'members' => 'required|array|size:' . $tournament->team_size,
            'members.*' => 'required|integer|exists:users,id',
        ]);

        // Duplicate kayıt kontrolü
        $existing = $tournament->teams()
            ->where('captain_id', $request->user()->id)
            ->exists();

        if ($existing) {
            return redirect()->back()->with('error', 'Zaten kayıtlısınız.');
        }

        $tournament->teams()->create([
            'captain_id' => $request->user()->id,
            'name' => $validated['team_name'],
            'members' => $validated['members'],
            'status' => 'registered',
        ]);

        // XP ekle
        $request->user()->addXp('joined_tournament');

        return redirect()->route('tournaments.show', $slug)
            ->with('success', 'Turnuvaya başarıyla kayıt oldunuz!');
    }

    /**
     * Bracket (eşleşme ağacı) sayfası
     */
    public function bracket(string $slug): View
    {
        $tournament = Tournament::with(['organizer', 'teams.captain'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('tournaments.bracket', compact('tournament'));
    }

    /**
     * Bracket oluştur (sadece organizatör)
     */
    public function generateBracket(Request $request, string $slug): RedirectResponse
    {
        $tournament = Tournament::with('teams')->where('slug', $slug)->firstOrFail();

        // Sadece organizatör yapabilir
        if ($tournament->organizer_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Bu işlem için yetkiniz yok.');
        }

        // En az 2 takım olmalı
        if ($tournament->teams()->count() < 2) {
            return redirect()->back()->with('error', 'En az 2 takım gerekli.');
        }

        // Takımları karıştır
        $teams = $tournament->teams()->get()->shuffle();
        
        // Bracket oluştur (single elimination)
        $bracket = $this->createSingleEliminationBracket($teams);
        
        $tournament->update([
            'bracket_data' => $bracket,
            'status' => 'in_progress'
        ]);

        return redirect()->route('tournaments.bracket', $slug)
            ->with('success', 'Eşleşmeler oluşturuldu!');
    }

    /**
     * Maç sonucu güncelle (sadece organizatör)
     */
    public function updateMatch(Request $request, string $slug, int $roundIndex, int $matchIndex): RedirectResponse
    {
        $tournament = Tournament::where('slug', $slug)->firstOrFail();

        // Sadece organizatör yapabilir
        if ($tournament->organizer_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Bu işlem için yetkiniz yok.');
        }

        $validated = $request->validate([
            'team1_score' => 'required|integer|min:0',
            'team2_score' => 'required|integer|min:0',
        ]);

        $bracket = $tournament->bracket_data;
        
        if (!isset($bracket[$roundIndex][$matchIndex])) {
            return redirect()->back()->with('error', 'Geçersiz maç.');
        }

        $match = &$bracket[$roundIndex][$matchIndex];
        $match['team1_score'] = $validated['team1_score'];
        $match['team2_score'] = $validated['team2_score'];
        
        // Kazananı belirle
        if ($validated['team1_score'] > $validated['team2_score']) {
            $match['winner'] = $match['team1_id'];
            $winnerName = $match['team1_name'];
        } elseif ($validated['team2_score'] > $validated['team1_score']) {
            $match['winner'] = $match['team2_id'];
            $winnerName = $match['team2_name'];
        } else {
            return redirect()->back()->with('error', 'Berabere olamaz, kazanan belirleyin.');
        }

        $match['status'] = 'completed';

        // Kazananı bir sonraki tura taşı
        if (isset($bracket[$roundIndex + 1])) {
            $nextMatchIndex = floor($matchIndex / 2);
            $isTeam1 = $matchIndex % 2 === 0;
            
            if ($isTeam1) {
                $bracket[$roundIndex + 1][$nextMatchIndex]['team1_id'] = $match['winner'];
                $bracket[$roundIndex + 1][$nextMatchIndex]['team1_name'] = $winnerName;
            } else {
                $bracket[$roundIndex + 1][$nextMatchIndex]['team2_id'] = $match['winner'];
                $bracket[$roundIndex + 1][$nextMatchIndex]['team2_name'] = $winnerName;
            }
        }

        // Final maçı tamamlandıysa turnuvayı bitir
        if ($roundIndex === count($bracket) - 1 && $matchIndex === 0) {
            $tournament->update([
                'bracket_data' => $bracket,
                'status' => 'completed'
            ]);
            
            return redirect()->route('tournaments.bracket', $slug)
                ->with('success', 'Turnuva tamamlandı! Şampiyon: ' . $winnerName);
        }

        $tournament->update(['bracket_data' => $bracket]);

        return redirect()->route('tournaments.bracket', $slug)
            ->with('success', 'Maç sonucu güncellendi!');
    }

    /**
     * Single elimination bracket oluştur
     */
    private function createSingleEliminationBracket($teams): array
    {
        $teamCount = $teams->count();
        
        // 2'nin kuvveti olacak şekilde yuvarla
        $bracketSize = pow(2, ceil(log($teamCount, 2)));
        
        $bracket = [];
        $round = [];
        
        // İlk turu oluştur
        for ($i = 0; $i < $bracketSize / 2; $i++) {
            $team1 = $teams->get($i * 2);
            $team2 = $teams->get($i * 2 + 1);
            
            $round[] = [
                'team1_id' => $team1 ? $team1->id : null,
                'team1_name' => $team1 ? $team1->name : 'BYE',
                'team2_id' => $team2 ? $team2->id : null,
                'team2_name' => $team2 ? $team2->name : 'BYE',
                'status' => 'pending',
            ];
        }
        
        $bracket[] = $round;
        
        // Sonraki turları oluştur (boş)
        $matchCount = count($round);
        while ($matchCount > 1) {
            $matchCount = ceil($matchCount / 2);
            $round = [];
            
            for ($i = 0; $i < $matchCount; $i++) {
                $round[] = [
                    'team1_id' => null,
                    'team1_name' => 'TBD',
                    'team2_id' => null,
                    'team2_name' => 'TBD',
                    'status' => 'pending',
                ];
            }
            
            $bracket[] = $round;
        }
        
        return $bracket;
    }
}
