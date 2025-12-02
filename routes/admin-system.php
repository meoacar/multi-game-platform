<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\BackupController;

// Sistem Yönetimi Route'ları
Route::middleware(['auth', 'admin', 'log.admin', 'permission:system.manage'])
    ->prefix('admin/system')
    ->name('admin.system.')
    ->group(function () {
        Route::get('/', [SystemController::class, 'index'])->name('index');
        Route::get('/health', [SystemController::class, 'health'])->name('health');
        Route::get('/info', [SystemController::class, 'info'])->name('info');
        Route::get('/performance', [SystemController::class, 'performance'])->name('performance');
        
        // Cache Yönetimi
        Route::get('/cache/stats', [SystemController::class, 'cacheStats'])->name('cache.stats');
        Route::post('/cache/clear', [SystemController::class, 'clearCache'])->name('cache.clear');
        Route::post('/cache/warm-up', [SystemController::class, 'warmUpCache'])->name('cache.warm-up');
        Route::post('/cache/optimize', [SystemController::class, 'optimizeCache'])->name('cache.optimize');
    });

// Yedekleme Yönetimi Route'ları
Route::middleware(['auth', 'admin', 'log.admin'])
    ->prefix('admin/backup')
    ->name('admin.backup.')
    ->group(function () {
        // Ana sayfa ve liste
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::get('/list', [BackupController::class, 'list'])->name('list');
        
        // Yedek oluşturma
        Route::get('/create', [BackupController::class, 'create'])->name('create');
        Route::post('/', [BackupController::class, 'store'])->name('store');
        
        // Yedek detay ve işlemler
        Route::get('/{id}', [BackupController::class, 'show'])->name('show');
        Route::post('/{id}/restore', [BackupController::class, 'restore'])->name('restore');
        Route::get('/{id}/download', [BackupController::class, 'download'])->name('download');
        Route::delete('/{id}', [BackupController::class, 'destroy'])->name('destroy');
        
        // Toplu işlemler
        Route::post('/cleanup', [BackupController::class, 'cleanup'])->name('cleanup');
        
        // Yardımcı endpoint'ler
        Route::get('/statistics', [BackupController::class, 'statistics'])->name('statistics');
        Route::get('/check-configuration', [BackupController::class, 'checkConfiguration'])->name('check-configuration');
        Route::get('/check-disk-space', [BackupController::class, 'checkDiskSpace'])->name('check-disk-space');
    });
