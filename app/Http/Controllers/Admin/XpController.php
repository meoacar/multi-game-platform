<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\XpEvent;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;

class XpController extends Controller
{
    /**
     * Display XP events
     */
    public function events(Request $request)
    {
        $query = XpEvent::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $events = $query->latest()->paginate(50);
        $types = XpEvent::distinct()->pluck('type')->sort()->values();

        return view('admin.xp.events', compact('events', 'types'));
    }

    /**
     * Display badges management
     */
    public function badges()
    {
        $badges = Badge::withCount('users')->get();
        return view('admin.xp.badges', compact('badges'));
    }

    /**
     * Store new badge
     */
    public function storeBadge(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:badges',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'xp_required' => 'nullable|integer|min:0',
        ]);

        Badge::create($validated);

        return back()->with('success', 'Rozet oluşturuldu.');
    }

    /**
     * Update badge
     */
    public function updateBadge(Request $request, $id)
    {
        $badge = Badge::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'xp_required' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $badge->update($validated);

        return back()->with('success', 'Rozet güncellendi.');
    }

    /**
     * Delete badge
     */
    public function destroyBadge($id)
    {
        Badge::findOrFail($id)->delete();
        return back()->with('success', 'Rozet silindi.');
    }

    /**
     * Manually adjust user XP
     */
    public function adjustUserXp(Request $request, $userId)
    {
        $validated = $request->validate([
            'points' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($userId);

        XpEvent::create([
            'user_id' => $userId,
            'type' => 'admin_adjustment',
            'points' => $validated['points'],
            'meta' => json_encode(['reason' => $validated['reason'], 'admin_id' => auth()->id()]),
        ]);

        $user->increment('xp_total', $validated['points']);

        return back()->with('success', 'XP ayarlandı.');
    }
}
