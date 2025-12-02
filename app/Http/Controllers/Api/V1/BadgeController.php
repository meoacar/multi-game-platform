<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Badge;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::all();

        return response()->json(['data' => $badges]);
    }

    public function userBadges()
    {
        $user = auth()->user();
        $badges = $user->badges()->withPivot('unlocked_at')->get();

        return response()->json(['data' => $badges]);
    }
}
