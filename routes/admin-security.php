<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SecurityController;

// Güvenlik Yönetimi Route'ları
Route::middleware(['auth', 'admin', 'log.admin', 'permission:system.manage'])
    ->prefix('admin/security')
    ->name('admin.security.')
    ->group(function () {
        // Güvenlik tarama sayfası
        Route::get('/', [SecurityController::class, 'index'])->name('index');
        
        // Güvenlik taraması çalıştır
        Route::post('/scan', [SecurityController::class, 'scan'])->name('scan');
        
        // Güvenlik önerileri
        Route::get('/recommendations', [SecurityController::class, 'recommendations'])->name('recommendations');
    });
