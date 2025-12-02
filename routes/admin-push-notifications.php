<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PushNotificationController;

/*
|--------------------------------------------------------------------------
| Admin Push Notification Routes
|--------------------------------------------------------------------------
|
| Push notification yönetimi route'ları
|
*/

Route::middleware(['auth', 'admin'])->prefix('admin/push-notifications')->name('admin.push-notifications.')->group(function () {
    // Dashboard
    Route::get('/', [PushNotificationController::class, 'index'])->name('index');
    
    // Test Push
    Route::get('/test', [PushNotificationController::class, 'testForm'])->name('test');
    Route::post('/test', [PushNotificationController::class, 'sendTest'])->name('send-test');
    
    // Token Listesi
    Route::get('/tokens', [PushNotificationController::class, 'tokens'])->name('tokens');
    Route::delete('/tokens/{userId}', [PushNotificationController::class, 'deleteToken'])->name('delete-token');
    
    // Toplu İşlemler
    Route::post('/cleanup-tokens', [PushNotificationController::class, 'cleanupTokens'])->name('cleanup-tokens');
    
    // İstatistikler (AJAX)
    Route::get('/statistics', [PushNotificationController::class, 'statistics'])->name('statistics');
});
