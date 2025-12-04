<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\GameSelectionController;

/*
|--------------------------------------------------------------------------
| Main Domain Routes
|--------------------------------------------------------------------------
|
| Bu route'lar ana domain (squadbul.com) için kullanılır.
| Landing page ve oyun seçimi burada tanımlanır.
|
*/

// Ana sayfa (Oyun Seçimi)
Route::get('/', [GameSelectionController::class, 'index'])->name('main.home');

// Oyun seçimi ve subdomain'e yönlendirme
Route::get('/play/{slug}', [GameSelectionController::class, 'select'])->name('game.select');

// Platform istatistikleri (AJAX)
Route::get('/api/stats', [MainController::class, 'getStats'])->name('main.stats');

// Öne çıkan turnuvalar (tüm oyunlardan)
Route::get('/api/featured-tournaments', [MainController::class, 'getFeaturedTournaments'])
    ->name('main.featured-tournaments');

/*
|--------------------------------------------------------------------------
| Backward Compatibility Redirects
|--------------------------------------------------------------------------
|
| Eski URL formatlarından yeni multi-game URL formatlarına yönlendirmeler.
| Bu route'lar RedirectLegacyUrls middleware tarafından yakalanmayan
| özel durumlar için kullanılır.
|
*/

// Ana sayfa hariç tüm game-specific route'lar için genel redirect
// Not: RedirectLegacyUrls middleware çoğu durumu halleder,
// ancak bazı özel durumlar için explicit redirect'ler gerekebilir

// Örnek: Eski admin route'ları (eğer subdomain değişikliği varsa)
// Route::permanentRedirect('/old-admin', '/admin');
