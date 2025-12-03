<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LfgController;
use App\Http\Controllers\Api\V1\ClanController;
use App\Http\Controllers\Api\V1\GuideController;
use App\Http\Controllers\Api\V1\CommunityPostController;
use App\Http\Controllers\Api\V1\TournamentController;
use App\Http\Controllers\Api\V1\SquadController;

/*
|--------------------------------------------------------------------------
| Deprecated API Routes
|--------------------------------------------------------------------------
|
| Bu dosya, multi-game platform güncellemesi öncesi kullanılan
| eski API endpoint'lerini içerir. Bu endpoint'ler geriye dönük
| uyumluluk için korunmuştur ancak kullanımdan kaldırılmıştır.
|
| Deprecation Date: 2025-12-02
| Sunset Date: 2026-06-02 (6 ay sonra)
|
| Yeni API endpoint'leri /api/v1 prefix'i ile game_id veya game_slug
| parametresi alır.
|
*/

// Deprecation tarihleri
$deprecationDate = '2025-12-02';
$sunsetDate = '2026-06-02';

/*
|--------------------------------------------------------------------------
| Public Deprecated Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware([
    'api',
    'deprecated.api:null,' . $deprecationDate . ',' . $sunsetDate
])->group(function () use ($deprecationDate, $sunsetDate) {
    
    // Eski LFG endpoint'leri (oyun parametresi olmadan)
    // Yeni: /api/v1/lfg?game_id=1 veya /api/v1/lfg?game_slug=pubg
    Route::get('/lfg-posts', [LfgController::class, 'index'])
        ->middleware('deprecated.api:/api/v1/lfg?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::get('/lfg-posts/{id}', [LfgController::class, 'show'])
        ->middleware('deprecated.api:/api/v1/lfg/{id}?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);

    // Eski Clan endpoint'leri
    // Yeni: /api/v1/clans?game_id=1
    Route::get('/clan-list', [ClanController::class, 'index'])
        ->middleware('deprecated.api:/api/v1/clans?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::get('/clan-detail/{id}', [ClanController::class, 'show'])
        ->middleware('deprecated.api:/api/v1/clans/{id}?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);

    // Eski Guide endpoint'leri
    // Yeni: /api/v1/guides?game_id=1
    Route::get('/guide-list', [GuideController::class, 'index'])
        ->middleware('deprecated.api:/api/v1/guides?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::get('/guide-detail/{guide}', [GuideController::class, 'show'])
        ->middleware('deprecated.api:/api/v1/guides/{guide}?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);

    // Eski Community Post endpoint'leri
    // Yeni: /api/v1/community-posts?game_id=1
    Route::get('/posts', [CommunityPostController::class, 'index'])
        ->middleware('deprecated.api:/api/v1/community-posts?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::get('/posts/{communityPost}', [CommunityPostController::class, 'show'])
        ->middleware('deprecated.api:/api/v1/community-posts/{communityPost}?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);

    // Eski Tournament endpoint'leri
    // Yeni: /api/v1/tournaments?game_id=1
    Route::get('/tournament-list', [TournamentController::class, 'index'])
        ->middleware('deprecated.api:/api/v1/tournaments?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::get('/tournament-detail/{id}', [TournamentController::class, 'show'])
        ->middleware('deprecated.api:/api/v1/tournaments/{id}?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);

    // Eski Squad endpoint'leri
    // Yeni: /api/v1/squads?game_id=1
    Route::get('/team-list', [SquadController::class, 'index'])
        ->middleware('deprecated.api:/api/v1/squads?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::get('/team-detail/{id}', [SquadController::class, 'show'])
        ->middleware('deprecated.api:/api/v1/squads/{id}?game_slug=pubg,' . $deprecationDate . ',' . $sunsetDate);
});

/*
|--------------------------------------------------------------------------
| Protected Deprecated Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware([
    'auth:sanctum',
    'check.banned',
    'deprecated.api:null,' . $deprecationDate . ',' . $sunsetDate
])->group(function () use ($deprecationDate, $sunsetDate) {
    
    // Eski LFG oluşturma endpoint'i
    // Yeni: /api/v1/lfg (game_id body'de gönderilir)
    Route::post('/create-lfg', [LfgController::class, 'store'])
        ->middleware('deprecated.api:/api/v1/lfg,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::put('/update-lfg/{id}', [LfgController::class, 'update'])
        ->middleware('deprecated.api:/api/v1/lfg/{id},' . $deprecationDate . ',' . $sunsetDate);
    
    Route::delete('/delete-lfg/{id}', [LfgController::class, 'destroy'])
        ->middleware('deprecated.api:/api/v1/lfg/{id},' . $deprecationDate . ',' . $sunsetDate);

    // Eski Clan oluşturma endpoint'i
    // Yeni: /api/v1/clans (game_id body'de gönderilir)
    Route::post('/create-clan', [ClanController::class, 'store'])
        ->middleware('deprecated.api:/api/v1/clans,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::put('/update-clan/{id}', [ClanController::class, 'update'])
        ->middleware('deprecated.api:/api/v1/clans/{id},' . $deprecationDate . ',' . $sunsetDate);

    // Eski Guide oluşturma endpoint'i
    // Yeni: /api/v1/guides (game_id body'de gönderilir)
    Route::post('/create-guide', [GuideController::class, 'store'])
        ->middleware('deprecated.api:/api/v1/guides,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::put('/update-guide/{guide}', [GuideController::class, 'update'])
        ->middleware('deprecated.api:/api/v1/guides/{guide},' . $deprecationDate . ',' . $sunsetDate);
    
    Route::delete('/delete-guide/{guide}', [GuideController::class, 'destroy'])
        ->middleware('deprecated.api:/api/v1/guides/{guide},' . $deprecationDate . ',' . $sunsetDate);

    // Eski Community Post oluşturma endpoint'i
    // Yeni: /api/v1/community-posts (game_id body'de gönderilir)
    Route::post('/create-post', [CommunityPostController::class, 'store'])
        ->middleware('deprecated.api:/api/v1/community-posts,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::delete('/delete-post/{communityPost}', [CommunityPostController::class, 'destroy'])
        ->middleware('deprecated.api:/api/v1/community-posts/{communityPost},' . $deprecationDate . ',' . $sunsetDate);

    // Eski Tournament oluşturma endpoint'i
    // Yeni: /api/v1/tournaments (game_id body'de gönderilir)
    Route::post('/create-tournament', [TournamentController::class, 'store'])
        ->middleware('deprecated.api:/api/v1/tournaments,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::post('/join-tournament/{id}', [TournamentController::class, 'register'])
        ->middleware('deprecated.api:/api/v1/tournaments/{id}/register,' . $deprecationDate . ',' . $sunsetDate);

    // Eski Squad oluşturma endpoint'i
    // Yeni: /api/v1/squads (game_id body'de gönderilir)
    Route::post('/create-team', [SquadController::class, 'store'])
        ->middleware('deprecated.api:/api/v1/squads,' . $deprecationDate . ',' . $sunsetDate);
    
    Route::put('/update-team/{id}', [SquadController::class, 'update'])
        ->middleware('deprecated.api:/api/v1/squads/{id},' . $deprecationDate . ',' . $sunsetDate);
    
    Route::delete('/delete-team/{id}', [SquadController::class, 'destroy'])
        ->middleware('deprecated.api:/api/v1/squads/{id},' . $deprecationDate . ',' . $sunsetDate);
});
