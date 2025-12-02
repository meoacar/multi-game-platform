<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of profiles
     */
    public function index(Request $request)
    {
        $query = Profile::with('user');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nickname', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('rank')) {
            $query->where('rank', $request->rank);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('server_region')) {
            $query->where('server_region', $request->server_region);
        }

        if ($request->filled('play_style')) {
            $query->where('play_style', $request->play_style);
        }

        $profiles = $query->latest()->paginate(20);

        // Get unique values for filters
        $ranks = Profile::distinct()->pluck('rank')->filter()->sort()->values();
        $cities = Profile::distinct()->pluck('city')->filter()->sort()->values();
        $regions = Profile::distinct()->pluck('server_region')->filter()->sort()->values();
        $playStyles = Profile::distinct()->pluck('play_style')->filter()->sort()->values();

        return view('admin.profiles.index', compact('profiles', 'ranks', 'cities', 'regions', 'playStyles'));
    }

    /**
     * Show the form for editing the specified profile
     */
    public function edit($id)
    {
        $profile = Profile::with('user')->findOrFail($id);
        
        return view('admin.profiles.edit', compact('profile'));
    }

    /**
     * Update the specified profile
     */
    public function update(Request $request, $id)
    {
        $profile = Profile::findOrFail($id);

        $validated = $request->validate([
            'nickname' => 'nullable|string|max:255',
            'pubg_id' => 'nullable|string|max:255',
            'rank' => 'nullable|string|max:255',
            'server_region' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'age_range' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'play_style' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $profile->update($validated);

        return redirect()->route('admin.profiles.index')
            ->with('success', 'Profil başarıyla güncellendi.');
    }

    /**
     * Remove spam/troll profile
     */
    public function destroy($id)
    {
        $profile = Profile::findOrFail($id);
        
        // Reset profile to empty values instead of deleting
        $profile->update([
            'nickname' => null,
            'pubg_id' => null,
            'rank' => null,
            'server_region' => null,
            'city' => null,
            'age_range' => null,
            'gender' => null,
            'play_style' => null,
            'bio' => null,
            'avatar_path' => null,
            'twitch_username' => null,
            'youtube_channel' => null,
            'discord_username' => null,
        ]);

        return redirect()->route('admin.profiles.index')
            ->with('success', 'Profil temizlendi.');
    }

    /**
     * Show statistics edit form
     */
    public function editStatistics($id)
    {
        $profile = Profile::with('user')->findOrFail($id);
        
        return view('admin.profiles.statistics', compact('profile'));
    }

    /**
     * Update game statistics
     */
    public function updateStatistics(Request $request, $id)
    {
        $profile = Profile::findOrFail($id);

        $validated = $request->validate([
            'matches_played' => 'required|integer|min:0',
            'wins' => 'required|integer|min:0',
            'kills' => 'required|integer|min:0',
            'deaths' => 'required|integer|min:0',
            'headshots' => 'required|integer|min:0',
            'top_10_finishes' => 'required|integer|min:0',
            'damage_dealt' => 'required|integer|min:0',
            'survival_time' => 'required|integer|min:0',
            'longest_kill' => 'required|integer|min:0',
        ]);

        // İstatistikleri güncelle ve oranları hesapla
        $profile->updateStatistics($validated);

        return redirect()->route('admin.profiles.edit', $profile->id)
            ->with('success', 'Oyun istatistikleri başarıyla güncellendi.');
    }
}
