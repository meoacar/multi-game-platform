<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;

/*
|--------------------------------------------------------------------------
| Main Domain Routes
|--------------------------------------------------------------------------
|
| Bu route'lar ana domain (takimsistemi.com) için kullanılır.
| Landing page ve platform geneli özellikler burada tanımlanır.
|
*/

// Ana sayfa (Landing Page) - Tüm oyunları listeler
Route::get('/', [MainController::class, 'index'])->name('main.home');

// Platform istatistikleri (AJAX)
Route::get('/api/stats', [MainController::class, 'getStats'])->name('main.stats');

// Öne çıkan turnuvalar (tüm oyunlardan)
Route::get('/api/featured-tournaments', [MainController::class, 'getFeaturedTournaments'])
    ->name('main.featured-tournaments');

// Oyun seçimi ve yönlendirme
Route::post('/switch-game/{slug}', [MainController::class, 'switchGame'])->name('main.switch-game');

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
