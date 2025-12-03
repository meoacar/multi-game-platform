<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GameController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\LfgController;
use App\Http\Controllers\Api\V1\LfgApplicationController;
use App\Http\Controllers\Api\V1\ClanController;
use App\Http\Controllers\Api\V1\ClanApplicationController;
use App\Http\Controllers\Api\V1\GuideController;
use App\Http\Controllers\Api\V1\CommunityPostController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\FriendshipController;
use App\Http\Controllers\Api\V1\MatchmakingController;
use App\Http\Controllers\Api\BadgeController;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
| API endpoint'leri /api/v1 prefix'i ile başlar
| Sanctum ile token-based authentication kullanılır
*/

// Public routes (Authentication gerektirmez)
Route::prefix('v1')->group(function () {
    // Auth endpoints
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Oyunlar (public)
    Route::get('/games', [GameController::class, 'index']);
    Route::get('/games/{idOrSlug}', [GameController::class, 'show']);

    // Cihazlar (public - herkes görebilir)
    Route::get('/devices', [DeviceController::class, 'index']);

    // Public profil görüntüleme
    Route::get('/users/{id}/profile', [ProfileController::class, 'show']);

    // LFG İlanları (public - herkes görebilir)
    Route::get('/lfg', [LfgController::class, 'index']);
    Route::get('/lfg/{id}', [LfgController::class, 'show']);

    // Klanlar (public - herkes görebilir)
    Route::get('/clans', [ClanController::class, 'index']);
    Route::get('/clans/{id}', [ClanController::class, 'show']);

    // Rehberler (public)
    Route::get('/guides', [GuideController::class, 'index']);
    Route::get('/guides/{guide}', [GuideController::class, 'show']);

    // Topluluk Postları (public)
    Route::get('/community-posts', [CommunityPostController::class, 'index']);
    Route::get('/community-posts/{communityPost}', [CommunityPostController::class, 'show']);

    // Rozetler (public)
    Route::get('/badges', [BadgeController::class, 'index']);
    Route::get('/badges/category/{category}', [BadgeController::class, 'byCategory']);
    Route::get('/badges/rarity/{rarity}', [BadgeController::class, 'byRarity']);
    Route::get('/badges/{slug}', [BadgeController::class, 'show']);

    // Takımlar (public)
    Route::get('/squads', [\App\Http\Controllers\Api\V1\SquadController::class, 'index']);
    Route::get('/squads/{id}', [\App\Http\Controllers\Api\V1\SquadController::class, 'show']);

    // Turnuvalar (public)
    Route::get('/tournaments', [\App\Http\Controllers\Api\V1\TournamentController::class, 'index']);
    Route::get('/tournaments/{id}', [\App\Http\Controllers\Api\V1\TournamentController::class, 'show']);
});

// Protected routes (Authentication gerektirir)
Route::prefix('v1')->middleware(['auth:sanctum', 'check.banned'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Profil yönetimi
    Route::put('/me/profile', [ProfileController::class, 'update']);

    // Cihaz yönetimi
    Route::get('/me/device', [DeviceController::class, 'show']);
    Route::put('/me/device', [DeviceController::class, 'update']);

    // LFG İlanları (protected)
    Route::post('/lfg', [LfgController::class, 'store']);
    Route::put('/lfg/{id}', [LfgController::class, 'update']);
    Route::delete('/lfg/{id}', [LfgController::class, 'destroy']);
    Route::post('/lfg/{id}/apply', [LfgController::class, 'apply']);
    Route::get('/lfg/{id}/applications', [LfgController::class, 'applications']);

    // LFG Başvuruları
    Route::patch('/lfg-applications/{id}/accept', [LfgApplicationController::class, 'accept']);
    Route::patch('/lfg-applications/{id}/reject', [LfgApplicationController::class, 'reject']);

    // Klanlar (protected)
    Route::post('/clans', [ClanController::class, 'store']);
    Route::put('/clans/{id}', [ClanController::class, 'update']);
    Route::post('/clans/{id}/apply', [ClanController::class, 'apply']);
    Route::get('/clans/{id}/applications', [ClanController::class, 'applications']);

    // Klan Başvuruları
    Route::post('/clan-applications/{id}/accept', [ClanApplicationController::class, 'accept']);
    Route::post('/clan-applications/{id}/reject', [ClanApplicationController::class, 'reject']);

    // Rehberler (protected)
    Route::post('/guides', [GuideController::class, 'store']);
    Route::put('/guides/{guide}', [GuideController::class, 'update']);
    Route::delete('/guides/{guide}', [GuideController::class, 'destroy']);
    Route::post('/guides/{guide}/like', [GuideController::class, 'like']);

    // Topluluk Postları (protected)
    Route::post('/community-posts', [CommunityPostController::class, 'store']);
    Route::delete('/community-posts/{communityPost}', [CommunityPostController::class, 'destroy']);
    Route::post('/community-posts/{communityPost}/like', [CommunityPostController::class, 'like']);

    // Yorumlar
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Mesajlaşma
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/messages/{userId}', [MessageController::class, 'show']);
    Route::patch('/messages/{id}/read', [MessageController::class, 'markAsRead']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    // Arkadaşlık
    Route::get('/friends', [FriendshipController::class, 'index']);
    Route::get('/friends/requests', [FriendshipController::class, 'requests']);
    Route::post('/friends/request', [FriendshipController::class, 'sendRequest']);
    Route::post('/friends/{id}/accept', [FriendshipController::class, 'accept']);
    Route::post('/friends/{id}/reject', [FriendshipController::class, 'reject']);
    Route::delete('/friends/{friendId}', [FriendshipController::class, 'destroy']);

    // Kullanıcı Rozetleri
    Route::get('/me/badges', [BadgeController::class, 'userBadges']);

    // Bildirimler
    Route::get('/notifications', [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
    Route::get('/notifications/unread', [\App\Http\Controllers\Api\V1\NotificationController::class, 'unread']);
    Route::get('/notifications/unread-count', [\App\Http\Controllers\Api\V1\NotificationController::class, 'unreadCount']);
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/mark-all-read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Api\V1\NotificationController::class, 'destroy']);
    Route::delete('/notifications', [\App\Http\Controllers\Api\V1\NotificationController::class, 'destroyAll']);

    // Takımlar (protected)
    Route::post('/squads', [\App\Http\Controllers\Api\V1\SquadController::class, 'store']);
    Route::put('/squads/{id}', [\App\Http\Controllers\Api\V1\SquadController::class, 'update']);
    Route::delete('/squads/{id}', [\App\Http\Controllers\Api\V1\SquadController::class, 'destroy']);
    Route::post('/squads/{id}/invite', [\App\Http\Controllers\Api\V1\SquadController::class, 'invite']);
    Route::delete('/squads/{id}/members/{userId}', [\App\Http\Controllers\Api\V1\SquadController::class, 'removeMember']);

    // Turnuvalar (protected)
    Route::post('/tournaments', [\App\Http\Controllers\Api\V1\TournamentController::class, 'store']);
    Route::post('/tournaments/{id}/register', [\App\Http\Controllers\Api\V1\TournamentController::class, 'register']);

    // FCM Token Yönetimi
    Route::post('/fcm/token', [\App\Http\Controllers\Api\FcmController::class, 'storeToken']);
    Route::delete('/fcm/token', [\App\Http\Controllers\Api\FcmController::class, 'deleteToken']);
    Route::post('/fcm/test', [\App\Http\Controllers\Api\FcmController::class, 'sendTest']);
    Route::get('/fcm/status', [\App\Http\Controllers\Api\FcmController::class, 'getStatus']);

    // Matchmaking (Eşleşme Sistemi)
    Route::post('/matchmaking/join', [MatchmakingController::class, 'join']);
    Route::post('/matchmaking/leave', [MatchmakingController::class, 'leave']);
    Route::get('/matchmaking/status', [MatchmakingController::class, 'status']);
    Route::post('/matchmaking/matches/{id}/accept', [MatchmakingController::class, 'acceptMatch']);
    Route::post('/matchmaking/matches/{id}/reject', [MatchmakingController::class, 'rejectMatch']);
    Route::get('/matchmaking/history', [MatchmakingController::class, 'history']);
});
