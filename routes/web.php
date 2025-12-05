<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LfgController;
use App\Http\Controllers\Web\ClanController;
use App\Http\Controllers\Web\GuideController;
use App\Http\Controllers\Web\CommunityController;
use App\Http\Controllers\Web\DeviceController;
use App\Http\Controllers\Web\XpController;
use App\Http\Controllers\Web\SquadController;
use App\Http\Controllers\Web\TournamentController;
use App\Http\Controllers\Web\FriendshipController;
use App\Http\Controllers\Web\MessageController;
use App\Http\Controllers\Web\MatchmakingController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ClanController as AdminClanController;
use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\QueueController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Multi-Game Platform Routes
|--------------------------------------------------------------------------
|
| Bu route dosyası subdomain tabanlı multi-game routing destekler.
| - Ana domain (takimsistemi.com) → main.php route'ları
| - Oyun subdomains (pubg.takimsistemi.com) → Aşağıdaki route'lar
|
*/

// Ana domain route'ları (Landing Page)
$domain = config('app.domain', 'takimsistemi.test');

// Localhost kontrolü
$host = request()->getHost();
$isLocalhost = in_array($host, ['localhost', '127.0.0.1']) || 
               str_contains($host, 'localhost:') || 
               str_contains($host, '127.0.0.1:');

if ($isLocalhost) {
    // Localhost: Ana sayfa için main.php route'larını yükle
    require __DIR__.'/main.php';
} else {
    // Production: Subdomain routing
    // Ana domain (takimsistemi.test) için landing page
    Route::domain($domain)->group(function () {
        require __DIR__.'/main.php';
    });
    
    // Oyun subdomain'leri için DetectGame middleware otomatik çalışacak
    // Tüm route'lar aşağıda tanımlı
}

// Localhost veya subdomain için ortak route'lar
// Ana sayfa
Route::get('/', [HomeController::class, 'index'])->name('home');

// SEO Routes (Public)
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// Statik Sayfalar (Public)
Route::get('/sayfa/{slug}', [PageController::class, 'show'])->name('pages.show');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/giris', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/giris', [AuthController::class, 'login']);
    Route::get('/kayit', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/kayit', [AuthController::class, 'register']);
    Route::get('/sifremi-unuttum', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/sifremi-unuttum', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/sifre-sifirla/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/sifre-sifirla', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/dogrula', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::get('/email/dogrula/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/dogrulama-linki-gonder', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::post('/cikis', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Onboarding Routes
Route::middleware(['auth'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/', [OnboardingController::class, 'start'])->name('index');
    Route::get('/step/{step}', [OnboardingController::class, 'step'])->name('step');
    Route::post('/step0', [OnboardingController::class, 'step0'])->name('step0');
    Route::post('/step1', [OnboardingController::class, 'step1'])->name('step1');
    Route::post('/step2', [OnboardingController::class, 'step2'])->name('step2');
    Route::post('/step3', [OnboardingController::class, 'step3'])->name('step3');
    Route::post('/step4', [OnboardingController::class, 'step4'])->name('step4');
    Route::post('/skip', [OnboardingController::class, 'skip'])->name('skip');
    Route::post('/back', [OnboardingController::class, 'back'])->name('back');
});

// İlanlar
Route::get('/ilanlar', [LfgController::class, 'index'])->name('lfg.index');

Route::middleware(['auth', 'check.banned', 'check.onboarding'])->group(function () {
    Route::get('/ilanlar/yeni', [LfgController::class, 'create'])->name('lfg.create');
    Route::post('/ilanlar', [LfgController::class, 'store'])->name('lfg.store');
    Route::get('/ilanlar/{id}/duzenle', [LfgController::class, 'edit'])->name('lfg.edit');
    Route::put('/ilanlar/{id}', [LfgController::class, 'update'])->name('lfg.update');
    Route::delete('/ilanlar/{id}', [LfgController::class, 'destroy'])->name('lfg.destroy');
    Route::post('/ilanlar/{id}/basvur', [LfgController::class, 'apply'])->name('lfg.apply');
    Route::get('/ilanlar/{id}/basvurular', [LfgController::class, 'applications'])->name('lfg.applications');
    Route::post('/ilanlar/basvuru/{id}/kabul', [LfgController::class, 'acceptApplication'])->name('lfg.application.accept');
    Route::post('/ilanlar/basvuru/{id}/reddet', [LfgController::class, 'rejectApplication'])->name('lfg.application.reject');
});

Route::get('/ilanlar/{id}', [LfgController::class, 'show'])->name('lfg.show');

// Klanlar
Route::get('/klanlar', [ClanController::class, 'index'])->name('clans.index');

Route::middleware(['auth', 'check.banned', 'check.onboarding'])->group(function () {
    Route::get('/klanlar/yeni', [ClanController::class, 'create'])->name('clans.create');
    Route::post('/klanlar', [ClanController::class, 'store'])->name('clans.store');
});

Route::get('/klanlar/{slug}', [ClanController::class, 'show'])->name('clans.show');

// Rehber
Route::get('/rehber', [GuideController::class, 'index'])->name('guide.index');

Route::middleware(['auth', 'check.banned', 'check.onboarding'])->group(function () {
    Route::get('/rehber/yeni', [GuideController::class, 'create'])->name('guide.create');
    Route::post('/rehber', [GuideController::class, 'store'])->name('guide.store');
});

Route::get('/rehber/{guide}', [GuideController::class, 'show'])->name('guide.show');

// Topluluk
Route::get('/topluluk', [\App\Http\Controllers\Web\CommunityController::class, 'index'])->name('community.index');

Route::middleware(['auth', 'check.banned', 'check.onboarding'])->group(function () {
    Route::get('/topluluk/yeni', [\App\Http\Controllers\Web\CommunityController::class, 'create'])->name('community.create');
    Route::post('/topluluk', [\App\Http\Controllers\Web\CommunityController::class, 'store'])->name('community.store');
});

Route::get('/topluluk/{post}', [\App\Http\Controllers\Web\CommunityController::class, 'show'])->name('community.show');

// Cihaz Ayarları
Route::get('/cihazlar', [\App\Http\Controllers\Web\DeviceController::class, 'index'])->name('devices.index');
Route::get('/cihazlar/{device}', [\App\Http\Controllers\Web\DeviceController::class, 'show'])->name('devices.show');

// XP & Liderlik
Route::get('/liderlik-tablosu', [\App\Http\Controllers\Web\XpController::class, 'leaderboard'])->name('xp.leaderboard');
Route::get('/rozetler', [\App\Http\Controllers\Web\XpController::class, 'badges'])->name('xp.badges');

Route::middleware(['auth', 'check.onboarding'])->group(function () {
    Route::get('/xp-gecmisi', [\App\Http\Controllers\Web\XpController::class, 'history'])->name('xp.history');
});

// Takımlar (Squads)
Route::get('/takimlar', [\App\Http\Controllers\Web\SquadController::class, 'index'])->name('squads.index');

Route::middleware(['auth', 'check.banned', 'check.onboarding'])->group(function () {
    Route::get('/takimlar/yeni', [\App\Http\Controllers\Web\SquadController::class, 'create'])->name('squads.create');
    Route::post('/takimlar', [\App\Http\Controllers\Web\SquadController::class, 'store'])->name('squads.store');
});

Route::get('/takimlar/{squad}', [\App\Http\Controllers\Web\SquadController::class, 'show'])->name('squads.show');

// Turnuvalar
Route::get('/turnuvalar', [TournamentController::class, 'index'])->name('tournaments.index');

Route::middleware(['auth', 'check.banned', 'check.onboarding'])->group(function () {
    Route::get('/turnuvalar/yeni', [TournamentController::class, 'create'])->name('tournaments.create');
    Route::post('/turnuvalar', [TournamentController::class, 'store'])->name('tournaments.store');
    Route::get('/turnuvalar/{tournament}/kayit', [TournamentController::class, 'register'])->name('tournaments.register');
    Route::post('/turnuvalar/{tournament}/kayit', [TournamentController::class, 'storeRegistration'])->name('tournaments.register.store');
    Route::post('/turnuvalar/{tournament}/bracket/generate', [TournamentController::class, 'generateBracket'])->name('tournaments.bracket.generate');
    Route::post('/turnuvalar/{tournament}/bracket/{round}/{match}', [TournamentController::class, 'updateMatch'])->name('tournaments.bracket.update');
});

Route::get('/turnuvalar/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');
Route::get('/turnuvalar/{tournament}/bracket', [TournamentController::class, 'bracket'])->name('tournaments.bracket');

// Arkadaşlık
Route::middleware(['auth', 'check.onboarding'])->group(function () {
    Route::get('/arkadaslar', [\App\Http\Controllers\Web\FriendshipController::class, 'index'])->name('friends.index');
    Route::get('/arkadas-istekleri', [\App\Http\Controllers\Web\FriendshipController::class, 'requests'])->name('friends.requests');
    Route::post('/arkadas-ekle', [\App\Http\Controllers\Web\FriendshipController::class, 'sendRequest'])->name('friends.send-request');
    Route::post('/arkadas-istegi/{id}/kabul', [\App\Http\Controllers\Web\FriendshipController::class, 'accept'])->name('friends.accept');
    Route::post('/arkadas-istegi/{id}/reddet', [\App\Http\Controllers\Web\FriendshipController::class, 'reject'])->name('friends.reject');
    Route::delete('/arkadas/{friendId}', [\App\Http\Controllers\Web\FriendshipController::class, 'destroy'])->name('friends.destroy');
    Route::post('/arkadas/{friendId}/engelle', [\App\Http\Controllers\Web\FriendshipController::class, 'block'])->name('friends.block');
});

// Mesajlaşma
Route::middleware(['auth', 'check.onboarding'])->group(function () {
    Route::get('/mesajlar', [\App\Http\Controllers\Web\MessageController::class, 'index'])->name('messages.index');
    Route::get('/mesajlar/{userId}', [\App\Http\Controllers\Web\MessageController::class, 'show'])->name('messages.show');
    Route::post('/mesajlar', [\App\Http\Controllers\Web\MessageController::class, 'store'])->name('messages.store');
});

// Matchmaking (Eşleşme Sistemi)
Route::get('/eslesme', [MatchmakingController::class, 'index'])->name('matchmaking.index');

Route::middleware(['auth', 'check.banned', 'check.onboarding'])->group(function () {
    Route::post('/eslesme/baslat', [MatchmakingController::class, 'start'])->name('matchmaking.start');
    Route::post('/eslesme/iptal', [MatchmakingController::class, 'cancel'])->name('matchmaking.cancel');
    Route::get('/eslesme/gecmis', [MatchmakingController::class, 'history'])->name('matchmaking.history');
});

// Profil
Route::get('/profil/{user}', [\App\Http\Controllers\Web\ProfileController::class, 'show'])->name('profile.show');

Route::middleware(['auth', 'check.onboarding'])->group(function () {
    Route::get('/profilim', [\App\Http\Controllers\Web\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profilim/tum-oyunlar', [\App\Http\Controllers\Web\ProfileController::class, 'multiGameDashboard'])->name('profile.multi-game');
    Route::get('/profilim/duzenle', [\App\Http\Controllers\Web\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profilim/duzenle', [\App\Http\Controllers\Web\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profilim/avatar', [\App\Http\Controllers\Web\ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
    Route::delete('/profilim/avatar', [\App\Http\Controllers\Web\ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    Route::get('/profilim/cihaz', [\App\Http\Controllers\Web\ProfileController::class, 'device'])->name('profile.device');
    Route::put('/profilim/cihaz', [\App\Http\Controllers\Web\ProfileController::class, 'updateDevice'])->name('profile.device.update');
    Route::get('/profilim/istatistikler', [\App\Http\Controllers\Web\ProfileController::class, 'statistics'])->name('profile.statistics');
    Route::put('/profilim/istatistikler', [\App\Http\Controllers\Web\ProfileController::class, 'updateStatistics'])->name('profile.statistics.update');
});

// Ayarlar ve Bildirimler
Route::middleware(['auth', 'check.onboarding'])->group(function () {
    Route::get('/ayarlar', [\App\Http\Controllers\Web\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/ayarlar/profil', [\App\Http\Controllers\Web\SettingsController::class, 'updateProfile'])->name('settings.update.profile');
    Route::put('/ayarlar/sifre', [\App\Http\Controllers\Web\SettingsController::class, 'updatePassword'])->name('settings.update.password');
    Route::put('/ayarlar/bildirimler', [\App\Http\Controllers\Web\SettingsController::class, 'updateNotifications'])->name('settings.update.notifications');
    Route::put('/ayarlar/gizlilik', [\App\Http\Controllers\Web\SettingsController::class, 'updatePrivacy'])->name('settings.update.privacy');
    Route::post('/ayarlar/dondur', [\App\Http\Controllers\Web\SettingsController::class, 'freezeAccount'])->name('settings.freeze');
    Route::delete('/ayarlar/sil', [\App\Http\Controllers\Web\SettingsController::class, 'deleteAccount'])->name('settings.delete');
    
    // Bildirimler (Türkçe + İngilizce alias)
    Route::get('/bildirimler', [\App\Http\Controllers\Web\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications', [\App\Http\Controllers\Web\NotificationController::class, 'index']); // İngilizce alias
});

// Admin Routes
Route::middleware(['auth', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard - Tüm adminler erişebilir
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard API Endpoints (AJAX için)
    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');
        Route::get('/chart/{type}', [DashboardController::class, 'getChartData'])->name('chart');
        Route::get('/activities', [DashboardController::class, 'getActivities'])->name('activities');
    });

    // Users API Endpoints (AJAX için)
    Route::prefix('api/users')->name('api.users.')->group(function () {
        Route::post('/bulk-ban', [AdminUserController::class, 'apiBulkBan'])
            ->middleware(['permission:users.ban'])
            ->name('bulk-ban');
        
        Route::post('/bulk-email', [AdminUserController::class, 'apiBulkEmail'])
            ->middleware(['permission:users.bulk_actions'])
            ->name('bulk-email');
        
        Route::post('/bulk-xp', [AdminUserController::class, 'apiBulkXp'])
            ->middleware(['permission:users.bulk_actions'])
            ->name('bulk-xp');
        
        Route::post('/bulk-delete', [AdminUserController::class, 'apiBulkDelete'])
            ->middleware(['permission:users.delete'])
            ->name('bulk-delete');
        
        Route::get('/export', [AdminUserController::class, 'apiExport'])
            ->middleware(['permission:users.export'])
            ->name('export');
    });
    
    // Kullanıcı Yönetimi
    Route::middleware(['permission:users.view'])->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    });
    
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])
        ->middleware(['permission:users.edit'])
        ->name('users.edit');
    
    Route::put('/users/{user}', [AdminUserController::class, 'update'])
        ->middleware(['permission:users.edit'])
        ->name('users.update');
    
    Route::post('/users/{user}/ban', [AdminUserController::class, 'ban'])
        ->middleware(['permission:users.ban'])
        ->name('users.ban');
    
    Route::post('/users/{user}/unban', [AdminUserController::class, 'unban'])
        ->middleware(['permission:users.ban'])
        ->name('users.unban');
    
    Route::post('/users/{user}/make-admin', [AdminUserController::class, 'makeAdmin'])
        ->middleware(['permission:users.manage_roles'])
        ->name('users.make-admin');
    
    Route::post('/users/{user}/remove-admin', [AdminUserController::class, 'removeAdmin'])
        ->middleware(['permission:users.manage_roles'])
        ->name('users.remove-admin');
    
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
        ->middleware(['permission:users.delete'])
        ->name('users.destroy');
    
    // Toplu İşlemler
    Route::post('/users/bulk-action', [AdminUserController::class, 'bulkAction'])
        ->middleware(['permission:users.bulk_actions'])
        ->name('users.bulk-action');
    
    // Export İşlemleri
    Route::get('/users/export/csv', [AdminUserController::class, 'exportCsv'])
        ->middleware(['permission:users.export'])
        ->name('users.export.csv');
    
    Route::get('/users/export/excel', [AdminUserController::class, 'exportExcel'])
        ->middleware(['permission:users.export'])
        ->name('users.export.excel');
    
    // Profil Yönetimi
    Route::middleware(['permission:profiles.view'])->group(function () {
        Route::get('/profiles', [AdminProfileController::class, 'index'])->name('profiles.index');
        Route::get('/profiles/{profile}', [AdminProfileController::class, 'show'])->name('profiles.show');
    });
    
    Route::get('/profiles/{profile}/edit', [AdminProfileController::class, 'edit'])
        ->middleware(['permission:profiles.edit'])
        ->name('profiles.edit');
    
    Route::put('/profiles/{profile}', [AdminProfileController::class, 'update'])
        ->middleware(['permission:profiles.edit'])
        ->name('profiles.update');
    
    Route::get('/profiles/{profile}/statistics', [AdminProfileController::class, 'editStatistics'])
        ->middleware(['permission:profiles.edit'])
        ->name('profiles.statistics');
    
    Route::put('/profiles/{profile}/statistics', [AdminProfileController::class, 'updateStatistics'])
        ->middleware(['permission:profiles.edit'])
        ->name('profiles.statistics.update');
    
    Route::put('/profiles/{profile}/verify', [AdminProfileController::class, 'verify'])
        ->middleware(['permission:profiles.verify'])
        ->name('profiles.verify');
    
    Route::delete('/profiles/{profile}', [AdminProfileController::class, 'destroy'])
        ->middleware(['permission:profiles.delete'])
        ->name('profiles.destroy');
    
    // Cihaz Yönetimi
    Route::get('/devices', [\App\Http\Controllers\Admin\DeviceController::class, 'index'])
        ->middleware(['permission:devices.view'])
        ->name('devices.index');
    
    Route::get('/devices/{id}/edit', [\App\Http\Controllers\Admin\DeviceController::class, 'edit'])
        ->middleware(['permission:devices.edit'])
        ->name('devices.edit');
    
    Route::put('/devices/{id}', [\App\Http\Controllers\Admin\DeviceController::class, 'update'])
        ->middleware(['permission:devices.edit'])
        ->name('devices.update');
    
    Route::delete('/devices/{id}', [\App\Http\Controllers\Admin\DeviceController::class, 'destroy'])
        ->middleware(['permission:devices.delete'])
        ->name('devices.destroy');
    
    // Rapor Yönetimi
    Route::middleware(['permission:reports.view'])->group(function () {
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
    });
    
    Route::middleware(['permission:reports.manage'])->group(function () {
        Route::post('/reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
        Route::post('/reports/{report}/reject', [AdminReportController::class, 'reject'])->name('reports.reject');
        Route::post('/reports/{report}/update-priority', [AdminReportController::class, 'updatePriority'])->name('reports.update-priority');
        Route::post('/reports/bulk-action', [AdminReportController::class, 'bulkAction'])->name('reports.bulk-action');
    });
    
    // Rapor API Endpoints (AJAX için)
    Route::prefix('api/reports')->name('api.reports.')->middleware(['permission:reports.view'])->group(function () {
        Route::get('/statistics', [AdminReportController::class, 'apiStatistics'])->name('statistics');
    });
    
    // LFG İlanları Yönetimi - ESKİ TANIMLAR SİLİNDİ (Aşağıda yeni prefix grubu var)
    
    // Klan Yönetimi - ESKİ TANIMLAR SİLİNDİ (Aşağıda yeni prefix grubu var)
    
    // Rehber Yönetimi - ESKİ TANIMLAR SİLİNDİ (Aşağıda yeni prefix grubu var)
    
    // Topluluk Gönderileri Yönetimi - ESKİ TANIMLAR SİLİNDİ (Aşağıda yeni prefix grubu var)
    
    // XP & Badges Yönetimi
    Route::middleware(['permission:xp.view'])->group(function () {
        Route::get('/xp/events', [\App\Http\Controllers\Admin\XpController::class, 'events'])->name('xp.events');
        Route::get('/xp/badges', [\App\Http\Controllers\Admin\XpController::class, 'badges'])->name('xp.badges');
    });
    
    Route::middleware(['permission:xp.manage'])->group(function () {
        Route::post('/xp/badges', [\App\Http\Controllers\Admin\XpController::class, 'storeBadge'])->name('xp.badges.store');
        Route::put('/xp/badges/{id}', [\App\Http\Controllers\Admin\XpController::class, 'updateBadge'])->name('xp.badges.update');
        Route::delete('/xp/badges/{id}', [\App\Http\Controllers\Admin\XpController::class, 'destroyBadge'])->name('xp.badges.destroy');
        Route::post('/users/{id}/adjust-xp', [\App\Http\Controllers\Admin\XpController::class, 'adjustUserXp'])->name('users.adjust-xp');
    });

    // Analitik Yönetimi
    Route::prefix('analytics')->name('analytics.')->middleware(['permission:analytics.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('index');
        Route::get('/users', [\App\Http\Controllers\Admin\AnalyticsController::class, 'users'])->name('users');
        Route::get('/content', [\App\Http\Controllers\Admin\AnalyticsController::class, 'content'])->name('content');
        Route::get('/platform', [\App\Http\Controllers\Admin\AnalyticsController::class, 'platform'])->name('platform');
        
        // Export
        Route::post('/export/pdf', [\App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('export.pdf');
        Route::post('/export/excel', [\App\Http\Controllers\Admin\AnalyticsController::class, 'exportExcel'])->name('export.excel');
        Route::post('/schedule', [\App\Http\Controllers\Admin\AnalyticsController::class, 'scheduleReport'])->name('schedule');
    });

    // Analitik API Endpoints (AJAX için)
    Route::prefix('api/analytics')->name('api.analytics.')->middleware(['permission:analytics.view'])->group(function () {
        Route::get('/users', [\App\Http\Controllers\Admin\AnalyticsController::class, 'apiUserAnalytics'])->name('users');
        Route::get('/content', [\App\Http\Controllers\Admin\AnalyticsController::class, 'apiContentAnalytics'])->name('content');
        Route::get('/platform', [\App\Http\Controllers\Admin\AnalyticsController::class, 'apiPlatformAnalytics'])->name('platform');
    });
    
    // Oyun Yönetimi
    Route::get('/games', [\App\Http\Controllers\Admin\GameController::class, 'index'])
        ->middleware(['permission:games.view'])
        ->name('games.index');
    
    Route::middleware(['permission:games.manage'])->group(function () {
        Route::post('/games', [\App\Http\Controllers\Admin\GameController::class, 'store'])->name('games.store');
        Route::put('/games/{id}', [\App\Http\Controllers\Admin\GameController::class, 'update'])->name('games.update');
        Route::delete('/games/{id}', [\App\Http\Controllers\Admin\GameController::class, 'destroy'])->name('games.destroy');
    });
    
    // Site Ayarları
    Route::middleware(['permission:settings.view'])->group(function () {
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::get('/settings/category/{category}', [\App\Http\Controllers\Admin\SettingController::class, 'getByCategory'])->name('settings.category');
    });
    
    Route::middleware(['permission:settings.manage'])->group(function () {
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/clear-cache', [\App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('/settings/test', [\App\Http\Controllers\Admin\SettingController::class, 'testSettings'])->name('settings.test');
    });
    
    // Statik Sayfalar (CMS)
    Route::get('/pages', [\App\Http\Controllers\Admin\PageController::class, 'index'])
        ->middleware(['permission:pages.view'])
        ->name('pages.index');
    
    Route::middleware(['permission:pages.manage'])->group(function () {
        Route::get('/pages/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [\App\Http\Controllers\Admin\PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('pages.destroy');
        Route::patch('/pages/{page}/toggle-publish', [\App\Http\Controllers\Admin\PageController::class, 'togglePublish'])->name('pages.toggle-publish');
        Route::post('/pages/bulk-publish', [\App\Http\Controllers\Admin\PageController::class, 'bulkPublish'])->name('pages.bulk-publish');
        Route::post('/pages/bulk-unpublish', [\App\Http\Controllers\Admin\PageController::class, 'bulkUnpublish'])->name('pages.bulk-unpublish');
        Route::delete('/pages/bulk-delete', [\App\Http\Controllers\Admin\PageController::class, 'bulkDelete'])->name('pages.bulk-delete');
    });
    
    // Admin Aktivite Logları
    Route::middleware(['permission:logs.view'])->group(function () {
        Route::get('/logs', [AdminActivityLogController::class, 'index'])->name('logs.index');
        Route::get('/logs/{log}', [AdminActivityLogController::class, 'show'])->name('logs.show');
    });
    
    Route::post('/logs/cleanup', [AdminActivityLogController::class, 'cleanup'])
        ->middleware(['permission:logs.manage'])
        ->name('logs.cleanup');
});

// Admin İçerik Yönetimi Route'ları
Route::middleware(['auth', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    // İçerik Yönetimi (Genel)
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ContentController::class, 'index'])->name('index');
        Route::get('/all', [\App\Http\Controllers\Admin\ContentController::class, 'all'])->name('all');
        Route::get('/statistics', [\App\Http\Controllers\Admin\ContentController::class, 'statistics'])->name('statistics');
    });

    // LFG İlanları Yönetimi
    Route::prefix('lfg-posts')->name('lfg-posts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\LfgPostController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\LfgPostController::class, 'getStatistics'])->name('statistics');
        Route::get('/{id}', [\App\Http\Controllers\Admin\LfgPostController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\LfgPostController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\LfgPostController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\LfgPostController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/close', [\App\Http\Controllers\Admin\LfgPostController::class, 'close'])->name('close');
        Route::post('/{id}/toggle-featured', [\App\Http\Controllers\Admin\LfgPostController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/bulk-close', [\App\Http\Controllers\Admin\LfgPostController::class, 'bulkClose'])->name('bulk-close');
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\LfgPostController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Klan Yönetimi
    Route::prefix('clans')->name('clans.')->group(function () {
        Route::get('/', [AdminClanController::class, 'index'])->name('index');
        Route::get('/statistics', [AdminClanController::class, 'getStatistics'])->name('statistics');
        Route::get('/applications', [AdminClanController::class, 'applications'])->name('applications');
        Route::get('/{id}', [AdminClanController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdminClanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminClanController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminClanController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-verified', [AdminClanController::class, 'toggleVerified'])->name('toggle-verified');
        Route::put('/applications/{id}', [AdminClanController::class, 'updateApplicationStatus'])->name('applications.update');
        Route::post('/{id}/remove-member', [AdminClanController::class, 'removeMember'])->name('remove-member');
    });

    // Rehber Yönetimi
    Route::prefix('guides')->name('guides.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GuideController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\GuideController::class, 'getStatistics'])->name('statistics');
        Route::get('/{id}', [\App\Http\Controllers\Admin\GuideController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\GuideController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\GuideController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\GuideController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-published', [\App\Http\Controllers\Admin\GuideController::class, 'togglePublished'])->name('toggle-published');
        Route::post('/{id}/toggle-featured', [\App\Http\Controllers\Admin\GuideController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/bulk-publish', [\App\Http\Controllers\Admin\GuideController::class, 'bulkPublish'])->name('bulk-publish');
        Route::post('/bulk-unpublish', [\App\Http\Controllers\Admin\GuideController::class, 'bulkUnpublish'])->name('bulk-unpublish');
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\GuideController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Topluluk Gönderileri Yönetimi
    Route::prefix('community-posts')->name('community-posts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CommunityPostController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\CommunityPostController::class, 'getStatistics'])->name('statistics');
        Route::get('/{id}', [\App\Http\Controllers\Admin\CommunityPostController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\CommunityPostController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\CommunityPostController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\CommunityPostController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-featured', [\App\Http\Controllers\Admin\CommunityPostController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{id}/toggle-pin', [\App\Http\Controllers\Admin\CommunityPostController::class, 'togglePin'])->name('toggle-pin');
        Route::post('/bulk-feature', [\App\Http\Controllers\Admin\CommunityPostController::class, 'bulkFeature'])->name('bulk-feature');
        Route::post('/bulk-unfeature', [\App\Http\Controllers\Admin\CommunityPostController::class, 'bulkUnfeature'])->name('bulk-unfeature');
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\CommunityPostController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Yorum Yönetimi
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\CommentController::class, 'getStatistics'])->name('statistics');
        Route::get('/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\CommentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/mark-spam', [\App\Http\Controllers\Admin\CommentController::class, 'markAsSpam'])->name('mark-spam');
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\CommentController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-mark-spam', [\App\Http\Controllers\Admin\CommentController::class, 'bulkMarkAsSpam'])->name('bulk-mark-spam');
    });

    // Moderasyon Yönetimi
    Route::prefix('moderation')->name('moderation.')->middleware(['permission:moderation.view'])->group(function () {
        // Moderasyon kuyruğu ana sayfası
        Route::get('/', [\App\Http\Controllers\Admin\ModerationController::class, 'index'])->name('index');
        
        // Bekleyen raporlar
        Route::get('/reports', [\App\Http\Controllers\Admin\ModerationController::class, 'reports'])->name('reports');
        
        // Şüpheli aktiviteler
        Route::get('/suspicious', [\App\Http\Controllers\Admin\ModerationController::class, 'suspicious'])->name('suspicious');
        
        // Otomatik moderasyon kuralları
        Route::get('/rules', [\App\Http\Controllers\Admin\ModerationController::class, 'rules'])->name('rules');
        
        // Rapor işleme (yetki gerekli)
        Route::post('/reports/{reportId}/process', [\App\Http\Controllers\Admin\ModerationController::class, 'processReport'])
            ->middleware(['permission:moderation.manage'])
            ->name('reports.process');
        
        // Toplu rapor işleme
        Route::post('/reports/bulk-process', [\App\Http\Controllers\Admin\ModerationController::class, 'bulkProcess'])
            ->middleware(['permission:moderation.manage'])
            ->name('reports.bulk-process');
        
        // Kural yönetimi
        Route::post('/rules/{ruleId}/toggle', [\App\Http\Controllers\Admin\ModerationController::class, 'toggleRule'])
            ->middleware(['permission:moderation.manage'])
            ->name('rules.toggle');
        
        // Küfür kelime yönetimi
        Route::post('/rules/bad-words/add', [\App\Http\Controllers\Admin\ModerationController::class, 'addBadWord'])
            ->middleware(['permission:moderation.manage'])
            ->name('rules.bad-words.add');
        
        Route::post('/rules/bad-words/remove', [\App\Http\Controllers\Admin\ModerationController::class, 'removeBadWord'])
            ->middleware(['permission:moderation.manage'])
            ->name('rules.bad-words.remove');
        
        // İçerik test etme
        Route::post('/test-content', [\App\Http\Controllers\Admin\ModerationController::class, 'testContent'])
            ->middleware(['permission:moderation.manage'])
            ->name('test-content');
    });

    // Moderasyon API Endpoints (AJAX için)
    Route::prefix('api/moderation')->name('api.moderation.')->middleware(['permission:moderation.view'])->group(function () {
        Route::get('/queue', [\App\Http\Controllers\Admin\ModerationController::class, 'apiGetQueue'])->name('queue');
        
        Route::post('/process', [\App\Http\Controllers\Admin\ModerationController::class, 'apiProcessReport'])
            ->middleware(['permission:moderation.manage'])
            ->name('process');
    });
});

// API Key ve Webhook Yönetimi
Route::middleware(['auth', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    // API Key Yönetimi
    Route::prefix('api/keys')->name('api.keys.')->middleware(['permission:api.manage'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ApiKeyController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ApiKeyController::class, 'store'])->name('store');
        Route::get('/{apiKey}', [\App\Http\Controllers\Admin\ApiKeyController::class, 'show'])->name('show');
        Route::get('/{apiKey}/edit', [\App\Http\Controllers\Admin\ApiKeyController::class, 'edit'])->name('edit');
        Route::put('/{apiKey}', [\App\Http\Controllers\Admin\ApiKeyController::class, 'update'])->name('update');
        Route::delete('/{apiKey}', [\App\Http\Controllers\Admin\ApiKeyController::class, 'destroy'])->name('destroy');
        Route::post('/{apiKey}/toggle', [\App\Http\Controllers\Admin\ApiKeyController::class, 'toggle'])->name('toggle');
    });

    // API Logları
    Route::get('/api/logs', [\App\Http\Controllers\Admin\ApiKeyController::class, 'logs'])
        ->middleware(['permission:api.view_logs'])
        ->name('api.logs');

    // Webhook Yönetimi
    Route::prefix('api/webhooks')->name('api.webhooks.')->middleware(['permission:webhooks.manage'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WebhookController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\WebhookController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\WebhookController::class, 'store'])->name('store');
        Route::get('/{webhook}', [\App\Http\Controllers\Admin\WebhookController::class, 'show'])->name('show');
        Route::get('/{webhook}/edit', [\App\Http\Controllers\Admin\WebhookController::class, 'edit'])->name('edit');
        Route::put('/{webhook}', [\App\Http\Controllers\Admin\WebhookController::class, 'update'])->name('update');
        Route::delete('/{webhook}', [\App\Http\Controllers\Admin\WebhookController::class, 'destroy'])->name('destroy');
        Route::post('/{webhook}/toggle', [\App\Http\Controllers\Admin\WebhookController::class, 'toggle'])->name('toggle');
        Route::post('/{webhook}/test', [\App\Http\Controllers\Admin\WebhookController::class, 'test'])->name('test');
    });
});

// Sayfa (CMS) Yönetimi
Route::middleware(['auth', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('pages')->name('pages.')->middleware(['permission:pages.view'])->group(function () {
        // Liste ve istatistikler
        Route::get('/', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\PageController::class, 'statistics'])->name('statistics');
        
        // CRUD işlemleri
        Route::get('/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])
            ->middleware(['permission:pages.create'])
            ->name('create');
        
        Route::post('/', [\App\Http\Controllers\Admin\PageController::class, 'store'])
            ->middleware(['permission:pages.create'])
            ->name('store');
        
        Route::get('/{page}', [\App\Http\Controllers\Admin\PageController::class, 'show'])->name('show');
        
        Route::get('/{page}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])
            ->middleware(['permission:pages.edit'])
            ->name('edit');
        
        Route::put('/{page}', [\App\Http\Controllers\Admin\PageController::class, 'update'])
            ->middleware(['permission:pages.edit'])
            ->name('update');
        
        Route::delete('/{page}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])
            ->middleware(['permission:pages.delete'])
            ->name('destroy');
        
        // Önizleme
        Route::get('/{page}/preview', [\App\Http\Controllers\Admin\PageController::class, 'preview'])
            ->name('preview');
        
        // Yayınlama işlemleri
        Route::post('/{page}/toggle-publish', [\App\Http\Controllers\Admin\PageController::class, 'togglePublish'])
            ->middleware(['permission:pages.publish'])
            ->name('toggle-publish');
        
        // Toplu işlemler
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\PageController::class, 'bulkDelete'])
            ->middleware(['permission:pages.delete'])
            ->name('bulk-delete');
        
        Route::post('/bulk-publish', [\App\Http\Controllers\Admin\PageController::class, 'bulkPublish'])
            ->middleware(['permission:pages.publish'])
            ->name('bulk-publish');
        
        Route::post('/bulk-unpublish', [\App\Http\Controllers\Admin\PageController::class, 'bulkUnpublish'])
            ->middleware(['permission:pages.publish'])
            ->name('bulk-unpublish');
    });
});

// Menü Yönetimi
Route::middleware(['auth', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('menus')->name('menus.')->middleware(['permission:menus.view'])->group(function () {
        // Liste
        Route::get('/', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('index');
        
        // CRUD işlemleri
        Route::get('/create', [\App\Http\Controllers\Admin\MenuController::class, 'create'])
            ->middleware(['permission:menus.create'])
            ->name('create');
        
        Route::post('/', [\App\Http\Controllers\Admin\MenuController::class, 'store'])
            ->middleware(['permission:menus.create'])
            ->name('store');

        Route::get('/{menu}/edit', [\App\Http\Controllers\Admin\MenuController::class, 'edit'])
            ->middleware(['permission:menus.edit'])
            ->name('edit');

        Route::put('/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])
            ->middleware(['permission:menus.edit'])
            ->name('update');

        Route::delete('/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])
            ->middleware(['permission:menus.delete'])
            ->name('destroy');

        // Menü Öğesi İşlemleri (AJAX)
        Route::post('/{menu}/items', [\App\Http\Controllers\Admin\MenuController::class, 'storeItem'])
            ->middleware(['permission:menus.edit'])
            ->name('items.store');

        Route::post('/{menu}/items/reorder', [\App\Http\Controllers\Admin\MenuController::class, 'reorderItems'])
            ->middleware(['permission:menus.edit'])
            ->name('items.reorder');
    });

    // Menü Öğesi İşlemleri (AJAX - ID ile)
    Route::prefix('menus/items')->name('menus.items.')->middleware(['permission:menus.edit'])->group(function () {
        Route::put('/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'updateItem'])->name('update');
        Route::delete('/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'destroyItem'])->name('destroy');
        Route::post('/{item}/toggle', [\App\Http\Controllers\Admin\MenuController::class, 'toggleItemStatus'])->name('toggle');
    });
});

// Widget Yönetimi
Route::middleware(['auth', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('widgets')->name('widgets.')->middleware(['permission:widgets.view'])->group(function () {
        // Liste
        Route::get('/', [\App\Http\Controllers\Admin\WidgetController::class, 'index'])->name('index');

        // Konum bazlı yönetim
        Route::get('/manage', [\App\Http\Controllers\Admin\WidgetController::class, 'manage'])->name('manage');

        // CRUD işlemleri
        Route::get('/create', [\App\Http\Controllers\Admin\WidgetController::class, 'create'])
            ->middleware(['permission:widgets.create'])
            ->name('create');

        Route::post('/', [\App\Http\Controllers\Admin\WidgetController::class, 'store'])
            ->middleware(['permission:widgets.create'])
            ->name('store');

        Route::get('/{widget}/edit', [\App\Http\Controllers\Admin\WidgetController::class, 'edit'])
            ->middleware(['permission:widgets.edit'])
            ->name('edit');

        Route::put('/{widget}', [\App\Http\Controllers\Admin\WidgetController::class, 'update'])
            ->middleware(['permission:widgets.edit'])
            ->name('update');

        Route::delete('/{widget}', [\App\Http\Controllers\Admin\WidgetController::class, 'destroy'])
            ->middleware(['permission:widgets.delete'])
            ->name('destroy');

        // Durum değiştirme
        Route::post('/{widget}/toggle', [\App\Http\Controllers\Admin\WidgetController::class, 'toggleStatus'])
            ->middleware(['permission:widgets.edit'])
            ->name('toggle');

        // Önizleme
        Route::get('/{widget}/preview', [\App\Http\Controllers\Admin\WidgetController::class, 'preview'])
            ->name('preview');

        // Sıralama (AJAX)
        Route::post('/reorder', [\App\Http\Controllers\Admin\WidgetController::class, 'reorder'])
            ->middleware(['permission:widgets.edit'])
            ->name('reorder');

        // Toplu işlemler
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\WidgetController::class, 'bulkDelete'])
            ->middleware(['permission:widgets.delete'])
            ->name('bulk-delete');

        Route::post('/bulk-activate', [\App\Http\Controllers\Admin\WidgetController::class, 'bulkActivate'])
            ->middleware(['permission:widgets.edit'])
            ->name('bulk-activate');

        Route::post('/bulk-deactivate', [\App\Http\Controllers\Admin\WidgetController::class, 'bulkDeactivate'])
            ->middleware(['permission:widgets.edit'])
            ->name('bulk-deactivate');

        // İstatistikler (AJAX)
        Route::get('/statistics', [\App\Http\Controllers\Admin\WidgetController::class, 'statistics'])
            ->name('statistics');
    });
});

// Admin Banner/Slider Yönetimi Route'ları
Route::middleware(['auth', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('banners')->name('banners.')->group(function () {
        // Liste ve görüntüleme
        Route::get('/', [\App\Http\Controllers\Admin\BannerController::class, 'index'])
            ->middleware(['permission:banners.view'])
            ->name('index');

        Route::get('/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'show'])
            ->middleware(['permission:banners.view'])
            ->name('show');

        // Oluşturma
        Route::get('/create', [\App\Http\Controllers\Admin\BannerController::class, 'create'])
            ->middleware(['permission:banners.create'])
            ->name('create');

        Route::post('/', [\App\Http\Controllers\Admin\BannerController::class, 'store'])
            ->middleware(['permission:banners.create'])
            ->name('store');

        // Düzenleme
        Route::get('/{banner}/edit', [\App\Http\Controllers\Admin\BannerController::class, 'edit'])
            ->middleware(['permission:banners.edit'])
            ->name('edit');

        Route::put('/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])
            ->middleware(['permission:banners.edit'])
            ->name('update');

        // Silme
        Route::delete('/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])
            ->middleware(['permission:banners.delete'])
            ->name('destroy');

        // Durum değiştirme
        Route::post('/{banner}/toggle', [\App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])
            ->middleware(['permission:banners.edit'])
            ->name('toggle');

        // Önizleme
        Route::get('/{banner}/preview', [\App\Http\Controllers\Admin\BannerController::class, 'preview'])
            ->name('preview');

        // Sıralama (AJAX)
        Route::post('/reorder', [\App\Http\Controllers\Admin\BannerController::class, 'reorder'])
            ->middleware(['permission:banners.edit'])
            ->name('reorder');

        // Toplu işlemler
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\BannerController::class, 'bulkDelete'])
            ->middleware(['permission:banners.delete'])
            ->name('bulk-delete');

        Route::post('/bulk-activate', [\App\Http\Controllers\Admin\BannerController::class, 'bulkActivate'])
            ->middleware(['permission:banners.edit'])
            ->name('bulk-activate');

        Route::post('/bulk-deactivate', [\App\Http\Controllers\Admin\BannerController::class, 'bulkDeactivate'])
            ->middleware(['permission:banners.edit'])
            ->name('bulk-deactivate');

        // İstatistikler (AJAX)
        Route::get('/{banner}/statistics', [\App\Http\Controllers\Admin\BannerController::class, 'statistics'])
            ->name('statistics');

        Route::get('/statistics/overall', [\App\Http\Controllers\Admin\BannerController::class, 'overallStatistics'])
            ->name('statistics.overall');

        // Tracking (Frontend'den çağrılır - public)
        Route::post('/{banner}/track/view', [\App\Http\Controllers\Admin\BannerController::class, 'trackView'])
            ->withoutMiddleware(['auth', 'admin', 'log.admin'])
            ->name('track.view');

        Route::post('/{banner}/track/click', [\App\Http\Controllers\Admin\BannerController::class, 'trackClick'])
            ->withoutMiddleware(['auth', 'admin', 'log.admin'])
            ->name('track.click');

        // A/B Testing
        Route::get('/{banner}/ab-test/create-variant', [\App\Http\Controllers\Admin\BannerController::class, 'createAbTestVariant'])
            ->middleware(['permission:banners.create'])
            ->name('ab-test.create-variant');

        Route::get('/{banner}/ab-test/report', [\App\Http\Controllers\Admin\BannerController::class, 'abTestReport'])
            ->middleware(['permission:banners.view'])
            ->name('ab-test.report');

        // Konum bazlı yönetim
        Route::get('/manage/location', [\App\Http\Controllers\Admin\BannerController::class, 'manage'])
            ->middleware(['permission:banners.view'])
            ->name('manage');
    });

    // Yorum Yönetimi
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\CommentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\CommentController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/{id}/mark-spam', [\App\Http\Controllers\Admin\CommentController::class, 'markSpam'])->name('mark-spam');
    });

    // Moderasyon Sistemi
    Route::prefix('moderation')->name('moderation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ModerationController::class, 'index'])->name('index');
        Route::get('/reports', [\App\Http\Controllers\Admin\ModerationController::class, 'reports'])->name('reports');
        Route::get('/suspicious', [\App\Http\Controllers\Admin\ModerationController::class, 'suspicious'])->name('suspicious');
        Route::get('/rules', [\App\Http\Controllers\Admin\ModerationController::class, 'rules'])->name('rules');
        Route::post('/process/{id}', [\App\Http\Controllers\Admin\ModerationController::class, 'process'])->name('process');
    });

    // Menü Yönetimi
    Route::prefix('menus')->name('menus.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\MenuController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\MenuController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\MenuController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [\App\Http\Controllers\Admin\MenuController::class, 'reorder'])->name('reorder');
    });

    // Widget Yönetimi
    Route::prefix('widgets')->name('widgets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WidgetController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\WidgetController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\WidgetController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\WidgetController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\WidgetController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\WidgetController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-active', [\App\Http\Controllers\Admin\WidgetController::class, 'toggleActive'])->name('toggle-active');
    });

    // Banner Yönetimi
    Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BannerController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-active', [\App\Http\Controllers\Admin\BannerController::class, 'toggleActive'])->name('toggle-active');
    });

    // API Key Yönetimi
    Route::prefix('api-keys')->name('api-keys.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\ApiKeyController::class, 'store'])->name('store');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ApiKeyController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-active', [\App\Http\Controllers\Admin\ApiKeyController::class, 'toggleActive'])->name('toggle-active');
    });

    // Webhook Yönetimi
    Route::prefix('webhooks')->name('webhooks.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WebhookController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\WebhookController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\WebhookController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\WebhookController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/test', [\App\Http\Controllers\Admin\WebhookController::class, 'test'])->name('test');
        Route::get('/{id}/logs', [\App\Http\Controllers\Admin\WebhookController::class, 'logs'])->name('logs');
    });

    // Rol Yönetimi
    Route::prefix('roles')->name('roles.')->middleware(['permission:roles.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
        
        Route::middleware(['permission:roles.manage'])->group(function () {
            Route::get('/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');
            
            // Yetki yönetimi (AJAX)
            Route::post('/{id}/permissions/attach', [\App\Http\Controllers\Admin\RoleController::class, 'attachPermission'])->name('permissions.attach');
            Route::post('/{id}/permissions/detach', [\App\Http\Controllers\Admin\RoleController::class, 'detachPermission'])->name('permissions.detach');
            Route::post('/{id}/permissions/sync', [\App\Http\Controllers\Admin\RoleController::class, 'syncPermissions'])->name('permissions.sync');
            
            // Rol kopyalama
            Route::post('/{id}/duplicate', [\App\Http\Controllers\Admin\RoleController::class, 'duplicate'])->name('duplicate');
        });
        
        Route::get('/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'show'])->name('show');
    });

    // Yetki Yönetimi
    Route::prefix('permissions')->name('permissions.')->middleware(['permission:permissions.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
        Route::get('/groups', [\App\Http\Controllers\Admin\PermissionController::class, 'groups'])->name('groups');
        Route::get('/groups/{group}', [\App\Http\Controllers\Admin\PermissionController::class, 'groupDetail'])->name('group-detail');
        Route::get('/{id}', [\App\Http\Controllers\Admin\PermissionController::class, 'show'])->name('show');
    });

    // Yetki API Endpoints (AJAX için)
    Route::prefix('api/permissions')->name('api.permissions.')->middleware(['permission:permissions.view'])->group(function () {
        Route::get('/usage', [\App\Http\Controllers\Admin\PermissionController::class, 'usage'])->name('usage');
        Route::get('/group-stats', [\App\Http\Controllers\Admin\PermissionController::class, 'groupStats'])->name('group-stats');
    });

    // Zamanlanmış Raporlar
    Route::prefix('scheduled-reports')->name('scheduled-reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ScheduledReportController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\ScheduledReportController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\ScheduledReportController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ScheduledReportController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-active', [\App\Http\Controllers\Admin\ScheduledReportController::class, 'toggleActive'])->name('toggle-active');
    });
});


// Log Yönetimi Route'ları
Route::middleware(['auth', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    // Log Ana Sayfası ve Genel İşlemler
    Route::prefix('logs')->name('logs.')->middleware(['permission:logs.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('index');
        
        // Admin Aktivite Logları
        Route::get('/admin', [\App\Http\Controllers\Admin\LogController::class, 'adminLogs'])->name('admin');
        
        // Sistem Logları
        Route::get('/system', [\App\Http\Controllers\Admin\LogController::class, 'systemLogs'])->name('system');
        
        // Güvenlik Logları
        Route::get('/security', [\App\Http\Controllers\Admin\LogController::class, 'securityLogs'])->name('security');
        
        // Log Detay
        Route::get('/{type}/{id}', [\App\Http\Controllers\Admin\LogController::class, 'show'])->name('show');
        
        // Log Temizleme Sayfası
        Route::get('/cleanup/page', [\App\Http\Controllers\Admin\LogController::class, 'cleanupPage'])
            ->middleware(['permission:logs.manage'])
            ->name('cleanup.page');
        
        // Log Temizleme İşlemi
        Route::post('/cleanup', [\App\Http\Controllers\Admin\LogController::class, 'cleanup'])
            ->middleware(['permission:logs.manage'])
            ->name('cleanup');
        
        // Toplu Log Temizleme
        Route::post('/cleanup/bulk', [\App\Http\Controllers\Admin\LogController::class, 'bulkCleanup'])
            ->middleware(['permission:logs.manage'])
            ->name('cleanup.bulk');
        
        // Log Export
        Route::get('/export/{type}', [\App\Http\Controllers\Admin\LogController::class, 'export'])
            ->middleware(['permission:logs.export'])
            ->name('export');
    });

    // SEO Yönetimi
    Route::prefix('seo')->name('seo.')->middleware(['permission:seo.view'])->group(function () {
        Route::get('/', [SeoController::class, 'index'])->name('index');
        
        Route::get('/create', [SeoController::class, 'create'])
            ->middleware(['permission:seo.manage'])
            ->name('create');
        
        Route::post('/', [SeoController::class, 'store'])
            ->middleware(['permission:seo.manage'])
            ->name('store');
        
        Route::get('/{seoSetting}/edit', [SeoController::class, 'edit'])
            ->middleware(['permission:seo.manage'])
            ->name('edit');
        
        Route::put('/{seoSetting}', [SeoController::class, 'update'])
            ->middleware(['permission:seo.manage'])
            ->name('update');
        
        Route::delete('/{seoSetting}', [SeoController::class, 'destroy'])
            ->middleware(['permission:seo.manage'])
            ->name('destroy');
        
        Route::patch('/{seoSetting}/toggle-active', [SeoController::class, 'toggleActive'])
            ->middleware(['permission:seo.manage'])
            ->name('toggle-active');
        
        Route::post('/clear-cache', [SeoController::class, 'clearCache'])
            ->middleware(['permission:seo.manage'])
            ->name('clear-cache');
    });
});

    // Log Yönetimi
    Route::prefix('logs')->name('logs.')->middleware(['permission:logs.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('index');
        Route::get('/admin', [\App\Http\Controllers\Admin\LogController::class, 'adminLogs'])->name('admin');
        Route::get('/system', [\App\Http\Controllers\Admin\LogController::class, 'systemLogs'])->name('system');
        Route::get('/security', [\App\Http\Controllers\Admin\LogController::class, 'securityLogs'])->name('security');
        Route::get('/{type}/{id}', [\App\Http\Controllers\Admin\LogController::class, 'show'])->name('show');
        
        // Export
        Route::get('/export/{type}', [\App\Http\Controllers\Admin\LogController::class, 'export'])->name('export');
        
        // Temizleme
        Route::get('/cleanup', [\App\Http\Controllers\Admin\LogController::class, 'cleanupPage'])
            ->middleware(['permission:logs.manage'])
            ->name('cleanup.page');
        
        Route::post('/cleanup', [\App\Http\Controllers\Admin\LogController::class, 'cleanup'])
            ->middleware(['permission:logs.manage'])
            ->name('cleanup');
        
        Route::post('/cleanup/bulk', [\App\Http\Controllers\Admin\LogController::class, 'bulkCleanup'])
            ->middleware(['permission:logs.manage'])
            ->name('cleanup.bulk');
    });

    // Moderasyon Yönetimi
    Route::prefix('moderation')->name('moderation.')->middleware(['permission:moderation.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ModerationController::class, 'index'])->name('index');
        Route::get('/queue', [\App\Http\Controllers\Admin\ModerationController::class, 'queue'])->name('queue');
        Route::get('/rules', [\App\Http\Controllers\Admin\ModerationController::class, 'rules'])->name('rules');
        
        Route::post('/process/{id}', [\App\Http\Controllers\Admin\ModerationController::class, 'process'])
            ->middleware(['permission:moderation.manage'])
            ->name('process');
        
        Route::post('/rules', [\App\Http\Controllers\Admin\ModerationController::class, 'storeRule'])
            ->middleware(['permission:moderation.manage'])
            ->name('rules.store');
        
        Route::put('/rules/{id}', [\App\Http\Controllers\Admin\ModerationController::class, 'updateRule'])
            ->middleware(['permission:moderation.manage'])
            ->name('rules.update');
        
        Route::delete('/rules/{id}', [\App\Http\Controllers\Admin\ModerationController::class, 'destroyRule'])
            ->middleware(['permission:moderation.manage'])
            ->name('rules.destroy');
    });

    // Yorum Yönetimi
    Route::prefix('comments')->name('comments.')->middleware(['permission:comments.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\CommentController::class, 'edit'])->name('edit');
        
        Route::put('/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'update'])
            ->middleware(['permission:comments.edit'])
            ->name('update');
        
        Route::delete('/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'destroy'])
            ->middleware(['permission:comments.delete'])
            ->name('destroy');
        
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\CommentController::class, 'bulkDelete'])
            ->middleware(['permission:comments.delete'])
            ->name('bulk-delete');
        
        Route::post('/{id}/mark-spam', [\App\Http\Controllers\Admin\CommentController::class, 'markSpam'])
            ->middleware(['permission:comments.edit'])
            ->name('mark-spam');
    });

    // Rol Yönetimi
    Route::prefix('roles')->name('roles.')->middleware(['permission:roles.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])
            ->middleware(['permission:roles.manage'])
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\RoleController::class, 'store'])
            ->middleware(['permission:roles.manage'])
            ->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])
            ->middleware(['permission:roles.manage'])
            ->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])
            ->middleware(['permission:roles.manage'])
            ->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])
            ->middleware(['permission:roles.manage'])
            ->name('destroy');
    });

    // Yetki Yönetimi
    Route::prefix('permissions')->name('permissions.')->middleware(['permission:permissions.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
        Route::get('/groups', [\App\Http\Controllers\Admin\PermissionController::class, 'groups'])->name('groups');
        Route::get('/groups/{group}', [\App\Http\Controllers\Admin\PermissionController::class, 'groupDetail'])->name('group-detail');
        Route::get('/{id}', [\App\Http\Controllers\Admin\PermissionController::class, 'show'])->name('show');
    });

    // Menü Yönetimi
    Route::prefix('menus')->name('menus.')->middleware(['permission:menus.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\MenuController::class, 'create'])
            ->middleware(['permission:menus.manage'])
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\MenuController::class, 'store'])
            ->middleware(['permission:menus.manage'])
            ->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\MenuController::class, 'edit'])
            ->middleware(['permission:menus.manage'])
            ->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])
            ->middleware(['permission:menus.manage'])
            ->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])
            ->middleware(['permission:menus.manage'])
            ->name('destroy');
        Route::post('/{id}/reorder', [\App\Http\Controllers\Admin\MenuController::class, 'reorder'])
            ->middleware(['permission:menus.manage'])
            ->name('reorder');
    });

    // Widget Yönetimi
    Route::prefix('widgets')->name('widgets.')->middleware(['permission:widgets.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WidgetController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\WidgetController::class, 'create'])
            ->middleware(['permission:widgets.manage'])
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\WidgetController::class, 'store'])
            ->middleware(['permission:widgets.manage'])
            ->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\WidgetController::class, 'edit'])
            ->middleware(['permission:widgets.manage'])
            ->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\WidgetController::class, 'update'])
            ->middleware(['permission:widgets.manage'])
            ->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\WidgetController::class, 'destroy'])
            ->middleware(['permission:widgets.manage'])
            ->name('destroy');
        Route::post('/reorder', [\App\Http\Controllers\Admin\WidgetController::class, 'reorder'])
            ->middleware(['permission:widgets.manage'])
            ->name('reorder');
    });

    // API Key Yönetimi
    Route::prefix('api-keys')->name('api-keys.')->middleware(['permission:api_keys.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\ApiKeyController::class, 'store'])
            ->middleware(['permission:api_keys.manage'])
            ->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\ApiKeyController::class, 'update'])
            ->middleware(['permission:api_keys.manage'])
            ->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ApiKeyController::class, 'destroy'])
            ->middleware(['permission:api_keys.manage'])
            ->name('destroy');
        Route::post('/{id}/regenerate', [\App\Http\Controllers\Admin\ApiKeyController::class, 'regenerate'])
            ->middleware(['permission:api_keys.manage'])
            ->name('regenerate');
    });

    // Webhook Yönetimi
    Route::prefix('webhooks')->name('webhooks.')->middleware(['permission:webhooks.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WebhookController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\WebhookController::class, 'store'])
            ->middleware(['permission:webhooks.manage'])
            ->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\WebhookController::class, 'update'])
            ->middleware(['permission:webhooks.manage'])
            ->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\WebhookController::class, 'destroy'])
            ->middleware(['permission:webhooks.manage'])
            ->name('destroy');
        Route::post('/{id}/test', [\App\Http\Controllers\Admin\WebhookController::class, 'test'])
            ->middleware(['permission:webhooks.manage'])
            ->name('test');
        Route::get('/{id}/logs', [\App\Http\Controllers\Admin\WebhookController::class, 'logs'])->name('logs');
    });

    // Banner Yönetimi
    Route::prefix('banners')->name('banners.')->middleware(['permission:banners.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BannerController::class, 'create'])
            ->middleware(['permission:banners.manage'])
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\BannerController::class, 'store'])
            ->middleware(['permission:banners.manage'])
            ->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\BannerController::class, 'edit'])
            ->middleware(['permission:banners.manage'])
            ->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])
            ->middleware(['permission:banners.manage'])
            ->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])
            ->middleware(['permission:banners.manage'])
            ->name('destroy');
        Route::post('/{id}/toggle-active', [\App\Http\Controllers\Admin\BannerController::class, 'toggleActive'])
            ->middleware(['permission:banners.manage'])
            ->name('toggle-active');
        Route::post('/reorder', [\App\Http\Controllers\Admin\BannerController::class, 'reorder'])
            ->middleware(['permission:banners.manage'])
            ->name('reorder');
    });

    // Zamanlanmış Raporlar
    Route::prefix('scheduled-reports')->name('scheduled-reports.')->middleware(['permission:analytics.view'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ScheduledReportController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\ScheduledReportController::class, 'store'])
            ->middleware(['permission:analytics.manage'])
            ->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\ScheduledReportController::class, 'update'])
            ->middleware(['permission:analytics.manage'])
            ->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ScheduledReportController::class, 'destroy'])
            ->middleware(['permission:analytics.manage'])
            ->name('destroy');
        Route::post('/{id}/toggle-active', [\App\Http\Controllers\Admin\ScheduledReportController::class, 'toggleActive'])
            ->middleware(['permission:analytics.manage'])
            ->name('toggle-active');
    });

// Queue Yönetimi (Admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('queue')->name('queue.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\QueueController::class, 'index'])->name('index');
        Route::get('/failed', [\App\Http\Controllers\Admin\QueueController::class, 'failed'])->name('failed');
        Route::get('/failed/{id}', [\App\Http\Controllers\Admin\QueueController::class, 'showFailed'])->name('show-failed');
        Route::post('/retry/{id}', [\App\Http\Controllers\Admin\QueueController::class, 'retryFailed'])->name('retry');
        Route::post('/retry-all', [\App\Http\Controllers\Admin\QueueController::class, 'retryAllFailed'])->name('retry-all');
        Route::delete('/failed/{id}', [\App\Http\Controllers\Admin\QueueController::class, 'deleteFailed'])->name('delete-failed');
        Route::delete('/flush', [\App\Http\Controllers\Admin\QueueController::class, 'flushFailed'])->name('flush');
        Route::post('/test', [\App\Http\Controllers\Admin\QueueController::class, 'testJob'])->name('test');
        Route::get('/check-worker', [\App\Http\Controllers\Admin\QueueController::class, 'checkWorker'])->name('check-worker');
        Route::get('/stats', [\App\Http\Controllers\Admin\QueueController::class, 'stats'])->name('stats');
    });
});

// Badge Yönetimi (Admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('badges')->name('badges.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BadgeController::class, 'index'])->name('index');
        Route::get('/stats', [\App\Http\Controllers\Admin\BadgeController::class, 'stats'])->name('stats');
        Route::get('/create', [\App\Http\Controllers\Admin\BadgeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\BadgeController::class, 'store'])->name('store');
        Route::get('/{badge}/edit', [\App\Http\Controllers\Admin\BadgeController::class, 'edit'])->name('edit');
        Route::put('/{badge}', [\App\Http\Controllers\Admin\BadgeController::class, 'update'])->name('update');
        Route::delete('/{badge}', [\App\Http\Controllers\Admin\BadgeController::class, 'destroy'])->name('destroy');
        Route::post('/{badge}/toggle-active', [\App\Http\Controllers\Admin\BadgeController::class, 'toggleActive'])->name('toggle-active');
        
        // Kullanıcıya rozet ver/al
        Route::post('/assign-to-user', [\App\Http\Controllers\Admin\BadgeController::class, 'assignToUser'])->name('assign-to-user');
        Route::post('/remove-from-user', [\App\Http\Controllers\Admin\BadgeController::class, 'removeFromUser'])->name('remove-from-user');
    });
});


// Matchmaking (Eşleşme Sistemi)
Route::get('/eslesme', [\App\Http\Controllers\Web\MatchmakingController::class, 'index'])->name('matchmaking.index');

Route::middleware(['auth', 'check.banned', 'check.onboarding'])->group(function () {
    Route::post('/eslesme/baslat', [\App\Http\Controllers\Web\MatchmakingController::class, 'start'])->name('matchmaking.start');
    Route::post('/eslesme/iptal', [\App\Http\Controllers\Web\MatchmakingController::class, 'cancel'])->name('matchmaking.cancel');
    Route::get('/eslesme/durum', [\App\Http\Controllers\Web\MatchmakingController::class, 'status'])->name('matchmaking.status');
    Route::get('/eslesme/gecmis', [\App\Http\Controllers\Web\MatchmakingController::class, 'history'])->name('matchmaking.history');
});
