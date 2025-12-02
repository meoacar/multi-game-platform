<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin-system.php'));
            Route::middleware('web')
                ->group(base_path('routes/admin-security.php'));
            Route::middleware('web')
                ->group(base_path('routes/admin-push-notifications.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware - Bakım modu kontrolü
        $middleware->append(\App\Http\Middleware\CheckMaintenanceMode::class);
        
        // Son giriş zamanını güncelle (web middleware grubuna)
        $middleware->appendToGroup('web', \App\Http\Middleware\UpdateLastLogin::class);
        
        // Middleware alias'ları
        $middleware->alias([
            'check.banned' => \App\Http\Middleware\CheckBannedUser::class,
            'check.onboarding' => \App\Http\Middleware\CheckOnboarding::class,
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'permission' => \App\Http\Middleware\CheckAdminPermission::class,
            'role' => \App\Http\Middleware\CheckAdminRole::class,
            'log.admin' => \App\Http\Middleware\LogAdminActivity::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
