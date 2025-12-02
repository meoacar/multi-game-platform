<?php

namespace App\Http\Middleware;

use App\Models\AdminActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * LogAdminActivity Middleware
 * 
 * Admin panelindeki tüm işlemleri loglar.
 * Her admin aksiyonu veritabanına kaydedilir (admin, işlem, IP, user agent, vb.)
 */
class LogAdminActivity
{
    /**
     * Loglanmayacak route'lar (çok sık çağrılan, önemsiz route'lar)
     */
    protected array $exceptRoutes = [
        'admin.dashboard',
        'admin.api.*',
    ];

    /**
     * Loglanmayacak HTTP metodları
     */
    protected array $exceptMethods = [
        'GET',
        'HEAD',
        'OPTIONS',
    ];

    /**
     * Gelen isteği işle
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // İsteği işle
        $response = $next($request);

        // Sadece başarılı istekleri logla (2xx status code)
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Admin aktivitesini logla
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    protected function logActivity(Request $request, Response $response): void
    {
        // Kullanıcı giriş yapmamışsa logla
        if (!auth()->check()) {
            return;
        }

        // GET isteklerini loglama (sadece değişiklik yapan işlemleri logla)
        if (in_array($request->method(), $this->exceptMethods)) {
            return;
        }

        // Hariç tutulan route'ları loglama
        $routeName = $request->route()?->getName();
        if ($routeName && $this->shouldExcludeRoute($routeName)) {
            return;
        }

        // Action'ı belirle
        $action = $this->determineAction($request);

        // Target bilgilerini belirle
        [$targetType, $targetId] = $this->determineTarget($request);

        // Meta bilgileri topla
        $meta = $this->collectMeta($request, $response);

        // Loga kaydet
        try {
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'meta' => $meta,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log hatası uygulamayı etkilememeli
            \Log::error('Admin activity log failed: ' . $e->getMessage());
        }
    }

    /**
     * Route hariç tutulmalı mı kontrol et
     *
     * @param  string  $routeName
     * @return bool
     */
    protected function shouldExcludeRoute(string $routeName): bool
    {
        foreach ($this->exceptRoutes as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Action'ı belirle
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function determineAction(Request $request): string
    {
        $routeName = $request->route()?->getName();
        $method = $request->method();

        // Route name'den action çıkar
        if ($routeName) {
            // admin.users.update -> update_user
            $parts = explode('.', $routeName);
            if (count($parts) >= 3) {
                $resource = rtrim($parts[1], 's'); // users -> user
                $action = $parts[2];
                return "{$action}_{$resource}";
            }
        }

        // Route name yoksa HTTP metodundan çıkar
        $methodActions = [
            'POST' => 'create',
            'PUT' => 'update',
            'PATCH' => 'update',
            'DELETE' => 'delete',
        ];

        return $methodActions[$method] ?? 'unknown';
    }

    /**
     * Target bilgilerini belirle
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array [targetType, targetId]
     */
    protected function determineTarget(Request $request): array
    {
        $routeName = $request->route()?->getName();
        
        if (!$routeName) {
            return [null, null];
        }

        // Route parametrelerinden ID'yi al
        $routeParams = $request->route()?->parameters() ?? [];
        
        // Yaygın ID parametreleri
        $idParams = ['user', 'profile', 'device', 'lfg', 'clan', 'guide', 'post', 'report', 'badge', 'game', 'page', 'setting'];
        
        foreach ($idParams as $param) {
            if (isset($routeParams[$param])) {
                $id = $routeParams[$param];
                
                // Model instance ise ID'sini al
                if (is_object($id) && method_exists($id, 'getKey')) {
                    $id = $id->getKey();
                }
                
                return [ucfirst($param), $id];
            }
        }

        return [null, null];
    }

    /**
     * Meta bilgileri topla
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return array
     */
    protected function collectMeta(Request $request, Response $response): array
    {
        $meta = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
        ];

        // Request data'yı ekle (hassas bilgileri filtrele)
        $requestData = $request->except(['password', 'password_confirmation', '_token', '_method']);
        if (!empty($requestData)) {
            $meta['request_data'] = $requestData;
        }

        // Route parametrelerini ekle
        $routeParams = $request->route()?->parameters() ?? [];
        if (!empty($routeParams)) {
            // Model instance'ları ID'ye çevir
            $params = [];
            foreach ($routeParams as $key => $value) {
                if (is_object($value) && method_exists($value, 'getKey')) {
                    $params[$key] = $value->getKey();
                } else {
                    $params[$key] = $value;
                }
            }
            $meta['route_params'] = $params;
        }

        return $meta;
    }
}
