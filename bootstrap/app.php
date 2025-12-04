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
            
            // Deprecated API routes - Geriye dönük uyumluluk için
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api-deprecated.php'));
            
            // Debug routes
            if (file_exists(base_path('routes/web-debug.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/web-debug.php'));
            }
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware - Bakım modu kontrolü
        $middleware->append(\App\Http\Middleware\CheckMaintenanceMode::class);
        
        // Backward compatibility - Eski URL'leri yeni formata yönlendir
        $middleware->appendToGroup('web', \App\Http\Middleware\RedirectLegacyUrls::class);
        
        // Multi-game platform - Oyun algılama (web middleware grubuna)
        $middleware->appendToGroup('web', \App\Http\Middleware\DetectGame::class);
        
        // Son giriş zamanını güncelle (web middleware grubuna)
        $middleware->appendToGroup('web', \App\Http\Middleware\UpdateLastLogin::class);
        
        // Cross-subdomain CSRF koruması için özel middleware kullan
        $middleware->validateCsrfTokens(
            except: ['api/*', 'webhooks/*', 'fcm/callback']
        );
        
        // Middleware alias'ları
        $middleware->alias([
            'check.banned' => \App\Http\Middleware\CheckBannedUser::class,
            'check.onboarding' => \App\Http\Middleware\CheckOnboarding::class,
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'permission' => \App\Http\Middleware\CheckAdminPermission::class,
            'role' => \App\Http\Middleware\CheckAdminRole::class,
            'log.admin' => \App\Http\Middleware\LogAdminActivity::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'game' => \App\Http\Middleware\DetectGame::class,
            'deprecated.api' => \App\Http\Middleware\DeprecatedApiEndpoint::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Game Not Found Exception handling
        $exceptions->render(function (\App\Exceptions\GameNotFoundException $e, $request) {
            // Hatayı logla
            app(\App\Services\SecurityLogService::class)->logSecurityEvent(
                'game_not_found',
                $e->getMessage(),
                'warning',
                [
                    'exception_class' => get_class($e),
                    'url' => $request->fullUrl(),
                ]
            );
            
            // API istekleri için JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Game Not Found',
                    'message' => $e->getMessage(),
                ], 404);
            }
            
            // Web istekleri için özel error page
            $activeGames = \App\Models\Game::active()->get();
            return response()->view('errors.game-not-found', [
                'message' => $e->getMessage(),
                'activeGames' => $activeGames,
            ], 404);
        });
        
        // Game Context Exception handling
        $exceptions->render(function (\App\Exceptions\GameContextException $e, $request) {
            // Oyun bağlamı hatasını logla
            app(\App\Services\SecurityLogService::class)->logSecurityEvent(
                'game_context_exception',
                $e->getMessage(),
                'error',
                [
                    'exception_class' => get_class($e),
                    'trace' => $e->getTraceAsString(),
                ]
            );
            
            // API istekleri için JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Game Context Error',
                    'message' => $e->getMessage(),
                ], 403);
            }
            
            // Web istekleri için özel error page
            return response()->view('errors.missing-game-context', [
                'message' => $e->getMessage(),
            ], 403);
        });
        
        // Database Exception handling
        $exceptions->render(function (\App\Exceptions\DatabaseException $e, $request) {
            // Orijinal hatayı logla
            \Illuminate\Support\Facades\Log::error('Database exception', [
                'original_message' => $e->getOriginalMessage(),
                'sanitized_message' => $e->getSanitizedMessage(),
                'game_id' => session('game_id'),
                'user_id' => auth()->id(),
                'is_admin' => auth()->check() && auth()->user()->is_admin,
            ]);
            
            // API istekleri için JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Database Error',
                    'message' => $e->getUserMessage(),
                ], 500);
            }
            
            // Web istekleri için özel error page
            $details = null;
            if (auth()->check() && auth()->user()->is_admin) {
                $details = [
                    'error' => $e->getOriginalMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'game_id' => session('game_id'),
                ];
            }
            
            return response()->view('errors.database-error', [
                'message' => $e->getUserMessage(),
                'details' => $details,
            ], 500);
        });
        
        // PDOException ve QueryException handling - Database hatalarını sanitize et
        $exceptions->render(function (\PDOException $e, $request) {
            $dbException = \App\Exceptions\DatabaseException::create($e);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Database Error',
                    'message' => $dbException->getUserMessage(),
                ], 500);
            }
            
            $details = null;
            if (auth()->check() && auth()->user()->is_admin) {
                $details = [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'game_id' => session('game_id'),
                ];
            }
            
            return response()->view('errors.database-error', [
                'message' => $dbException->getUserMessage(),
                'details' => $details,
            ], 500);
        });
        
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            $dbException = \App\Exceptions\DatabaseException::create($e);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Database Error',
                    'message' => $dbException->getUserMessage(),
                ], 500);
            }
            
            $details = null;
            if (auth()->check() && auth()->user()->is_admin) {
                $details = [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'game_id' => session('game_id'),
                ];
            }
            
            return response()->view('errors.database-error', [
                'message' => $dbException->getUserMessage(),
                'details' => $details,
            ], 500);
        });
        
        // Genel exception handling - Oyunla ilgili hataları logla
        $exceptions->report(function (\Throwable $e) {
            // Sadece oyun bağlamı olan isteklerde logla
            if (session('game_id')) {
                app(\App\Services\SecurityLogService::class)->logSecurityEvent(
                    'game_related_exception',
                    $e->getMessage(),
                    'error',
                    [
                        'exception_class' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]
                );
            }
        });
    })->create();
