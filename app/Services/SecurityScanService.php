<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/**
 * Güvenlik tarama servisi
 * SQL injection, XSS ve CSRF kontrollerini yapar
 */
class SecurityScanService
{
    protected array $results = [];
    protected array $vulnerabilities = [];
    
    /**
     * Tam güvenlik taraması yap
     */
    public function runFullScan(): array
    {
        $this->results = [
            'sql_injection' => $this->scanSqlInjection(),
            'xss' => $this->scanXss(),
            'csrf' => $this->scanCsrf(),
            'summary' => [],
        ];
        
        // Özet oluştur
        $totalVulnerabilities = 0;
        $criticalCount = 0;
        $warningCount = 0;
        
        foreach ($this->results as $key => $result) {
            if ($key !== 'summary' && isset($result['vulnerabilities'])) {
                $totalVulnerabilities += count($result['vulnerabilities']);
                foreach ($result['vulnerabilities'] as $vuln) {
                    if ($vuln['severity'] === 'critical') {
                        $criticalCount++;
                    } elseif ($vuln['severity'] === 'warning') {
                        $warningCount++;
                    }
                }
            }
        }
        
        $this->results['summary'] = [
            'total_vulnerabilities' => $totalVulnerabilities,
            'critical' => $criticalCount,
            'warning' => $warningCount,
            'status' => $criticalCount > 0 ? 'critical' : ($warningCount > 0 ? 'warning' : 'safe'),
        ];
        
        return $this->results;
    }
    
    /**
     * SQL Injection kontrolü
     */
    public function scanSqlInjection(): array
    {
        $vulnerabilities = [];
        
        // 1. Raw query kullanımlarını kontrol et
        $rawQueryFiles = $this->scanForPattern(
            app_path(),
            '/DB::raw\s*\(\s*["\'].*?\$.*?["\']\s*\)/',
            'SQL Injection riski: DB::raw() içinde değişken kullanımı'
        );
        
        foreach ($rawQueryFiles as $file) {
            $vulnerabilities[] = [
                'type' => 'sql_injection',
                'severity' => 'critical',
                'file' => $file['file'],
                'line' => $file['line'],
                'description' => 'DB::raw() içinde parametre binding kullanılmadan değişken kullanımı tespit edildi',
                'recommendation' => 'DB::raw() yerine parametre binding kullanın veya değişkenleri güvenli şekilde escape edin',
            ];
        }
        
        // 2. whereRaw kullanımlarını kontrol et
        $whereRawFiles = $this->scanForPattern(
            app_path(),
            '/whereRaw\s*\(\s*["\'].*?\$.*?["\']\s*\)/',
            'SQL Injection riski: whereRaw() içinde değişken kullanımı'
        );
        
        foreach ($whereRawFiles as $file) {
            $vulnerabilities[] = [
                'type' => 'sql_injection',
                'severity' => 'critical',
                'file' => $file['file'],
                'line' => $file['line'],
                'description' => 'whereRaw() içinde parametre binding kullanılmadan değişken kullanımı tespit edildi',
                'recommendation' => 'whereRaw() ikinci parametresinde binding kullanın: whereRaw("column = ?", [$value])',
            ];
        }
        
        // 3. Direct DB::statement kullanımlarını kontrol et
        $statementFiles = $this->scanForPattern(
            app_path(),
            '/DB::statement\s*\(\s*["\'].*?\$.*?["\']\s*\)/',
            'SQL Injection riski: DB::statement() içinde değişken kullanımı'
        );
        
        foreach ($statementFiles as $file) {
            $vulnerabilities[] = [
                'type' => 'sql_injection',
                'severity' => 'critical',
                'file' => $file['file'],
                'line' => $file['line'],
                'description' => 'DB::statement() içinde parametre binding kullanılmadan değişken kullanımı tespit edildi',
                'recommendation' => 'Parametre binding kullanın: DB::statement("query", [$param])',
            ];
        }
        
        return [
            'scan_type' => 'SQL Injection',
            'vulnerabilities' => $vulnerabilities,
            'total' => count($vulnerabilities),
            'status' => count($vulnerabilities) > 0 ? 'vulnerable' : 'safe',
        ];
    }
    
    /**
     * XSS (Cross-Site Scripting) kontrolü
     */
    public function scanXss(): array
    {
        $vulnerabilities = [];
        
        // 1. Blade dosyalarında {!! !!} kullanımlarını kontrol et
        $unescapedBladeFiles = $this->scanForPattern(
            resource_path('views'),
            '/\{!!\s*\$.*?\s*!!\}/',
            'XSS riski: Escape edilmemiş değişken çıktısı'
        );
        
        foreach ($unescapedBladeFiles as $file) {
            $vulnerabilities[] = [
                'type' => 'xss',
                'severity' => 'critical',
                'file' => $file['file'],
                'line' => $file['line'],
                'description' => 'Blade template\'de {!! !!} ile escape edilmemiş değişken kullanımı tespit edildi',
                'recommendation' => 'Kullanıcı girdisi içeren değişkenler için {{ }} kullanın veya htmlspecialchars() ile temizleyin',
            ];
        }
        
        // 2. JavaScript içinde PHP değişken kullanımlarını kontrol et
        $jsVarFiles = $this->scanForPattern(
            resource_path('views'),
            '/<script[^>]*>.*?\$.*?<\/script>/s',
            'XSS riski: JavaScript içinde PHP değişkeni'
        );
        
        foreach ($jsVarFiles as $file) {
            $vulnerabilities[] = [
                'type' => 'xss',
                'severity' => 'warning',
                'file' => $file['file'],
                'line' => $file['line'],
                'description' => 'JavaScript bloğu içinde PHP değişkeni kullanımı tespit edildi',
                'recommendation' => 'JSON encode kullanın: var data = @json($variable);',
            ];
        }
        
        // 3. echo kullanımlarını kontrol et (Blade dışında)
        $echoFiles = $this->scanForPattern(
            app_path(),
            '/echo\s+\$/',
            'XSS riski: Doğrudan echo kullanımı'
        );
        
        foreach ($echoFiles as $file) {
            $vulnerabilities[] = [
                'type' => 'xss',
                'severity' => 'warning',
                'file' => $file['file'],
                'line' => $file['line'],
                'description' => 'Controller/Service içinde doğrudan echo kullanımı tespit edildi',
                'recommendation' => 'View kullanın veya response()->json() ile güvenli çıktı verin',
            ];
        }
        
        // 4. Request input'larının doğrudan kullanımını kontrol et
        $directInputFiles = $this->scanForPattern(
            resource_path('views'),
            '/\{\{\s*request\(\)->.*?\}\}/',
            'XSS riski: Request verisi doğrudan kullanımı'
        );
        
        foreach ($directInputFiles as $file) {
            $vulnerabilities[] = [
                'type' => 'xss',
                'severity' => 'warning',
                'file' => $file['file'],
                'line' => $file['line'],
                'description' => 'View\'de request() verisi doğrudan kullanılıyor',
                'recommendation' => 'Controller\'da validate edin ve temizlenmiş veriyi view\'e gönderin',
            ];
        }
        
        return [
            'scan_type' => 'XSS (Cross-Site Scripting)',
            'vulnerabilities' => $vulnerabilities,
            'total' => count($vulnerabilities),
            'status' => count($vulnerabilities) > 0 ? 'vulnerable' : 'safe',
        ];
    }
    
    /**
     * CSRF (Cross-Site Request Forgery) kontrolü
     */
    public function scanCsrf(): array
    {
        $vulnerabilities = [];
        
        // 1. POST/PUT/DELETE route'larında CSRF koruması kontrolü
        $routes = Route::getRoutes();
        
        foreach ($routes as $route) {
            $methods = $route->methods();
            $isStateChanging = in_array('POST', $methods) || 
                              in_array('PUT', $methods) || 
                              in_array('DELETE', $methods) || 
                              in_array('PATCH', $methods);
            
            if ($isStateChanging) {
                $middleware = $route->middleware();
                $hasWeb = in_array('web', $middleware);
                $hasApi = in_array('api', $middleware);
                
                // API route'ları Sanctum ile korunuyor, web route'ları CSRF ile
                if (!$hasWeb && !$hasApi) {
                    $vulnerabilities[] = [
                        'type' => 'csrf',
                        'severity' => 'critical',
                        'route' => $route->uri(),
                        'methods' => implode(', ', $methods),
                        'description' => 'State-changing route CSRF koruması olmadan',
                        'recommendation' => 'Route\'a "web" middleware grubu ekleyin',
                    ];
                }
            }
        }
        
        // 2. Form'larda @csrf token kontrolü
        $formsWithoutCsrf = $this->scanForPattern(
            resource_path('views'),
            '/<form[^>]*method\s*=\s*["\'](?:post|put|delete|patch)["\'][^>]*>(?:(?!@csrf).)*?<\/form>/is',
            'CSRF riski: Form\'da @csrf token yok'
        );
        
        foreach ($formsWithoutCsrf as $file) {
            $vulnerabilities[] = [
                'type' => 'csrf',
                'severity' => 'critical',
                'file' => $file['file'],
                'line' => $file['line'],
                'description' => 'POST/PUT/DELETE/PATCH form\'unda @csrf token bulunamadı',
                'recommendation' => 'Form içine @csrf direktifi ekleyin',
            ];
        }
        
        // 3. AJAX isteklerinde CSRF token kontrolü
        $ajaxWithoutCsrf = $this->scanForPattern(
            resource_path('views'),
            '/\$\.(?:post|ajax)\s*\([^)]*\)/s',
            'CSRF riski: AJAX isteğinde token kontrolü'
        );
        
        foreach ($ajaxWithoutCsrf as $file) {
            // Bu bir warning çünkü meta tag ile global olarak ayarlanmış olabilir
            $vulnerabilities[] = [
                'type' => 'csrf',
                'severity' => 'warning',
                'file' => $file['file'],
                'line' => $file['line'],
                'description' => 'AJAX isteği tespit edildi - CSRF token kontrolü gerekli',
                'recommendation' => 'X-CSRF-TOKEN header\'ı ekleyin veya _token parametresi gönderin',
            ];
        }
        
        return [
            'scan_type' => 'CSRF (Cross-Site Request Forgery)',
            'vulnerabilities' => $vulnerabilities,
            'total' => count($vulnerabilities),
            'status' => count($vulnerabilities) > 0 ? 'vulnerable' : 'safe',
        ];
    }
    
    /**
     * Dosyalarda pattern arama
     */
    protected function scanForPattern(string $directory, string $pattern, string $description): array
    {
        $results = [];
        
        if (!File::exists($directory)) {
            return $results;
        }
        
        $files = File::allFiles($directory);
        
        foreach ($files as $file) {
            // Sadece PHP ve Blade dosyalarını tara
            if (!in_array($file->getExtension(), ['php', 'blade'])) {
                continue;
            }
            
            $content = File::get($file->getPathname());
            $lines = explode("\n", $content);
            
            foreach ($lines as $lineNumber => $line) {
                if (preg_match($pattern, $line)) {
                    $results[] = [
                        'file' => str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname()),
                        'line' => $lineNumber + 1,
                        'content' => trim($line),
                        'description' => $description,
                    ];
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Güvenlik önerilerini getir
     */
    public function getSecurityRecommendations(): array
    {
        return [
            'sql_injection' => [
                'title' => 'SQL Injection Koruması',
                'recommendations' => [
                    'Her zaman Eloquent ORM veya Query Builder kullanın',
                    'Raw query kullanırken mutlaka parametre binding kullanın',
                    'Kullanıcı girdilerini asla doğrudan SQL sorgusuna eklemeyin',
                    'Input validation ve sanitization yapın',
                    'Prepared statements kullanın',
                ],
            ],
            'xss' => [
                'title' => 'XSS (Cross-Site Scripting) Koruması',
                'recommendations' => [
                    'Blade template\'lerde {{ }} kullanın (otomatik escape)',
                    '{!! !!} kullanımından kaçının, gerekirse htmlspecialchars() kullanın',
                    'Kullanıcı girdilerini her zaman validate ve sanitize edin',
                    'Content Security Policy (CSP) header\'ları ekleyin',
                    'JavaScript\'e veri aktarırken @json() kullanın',
                ],
            ],
            'csrf' => [
                'title' => 'CSRF (Cross-Site Request Forgery) Koruması',
                'recommendations' => [
                    'Tüm state-changing route\'lara "web" middleware ekleyin',
                    'Form\'larda @csrf direktifi kullanın',
                    'AJAX isteklerinde X-CSRF-TOKEN header\'ı gönderin',
                    'API route\'larında Sanctum token authentication kullanın',
                    'SameSite cookie ayarlarını yapılandırın',
                ],
            ],
            'general' => [
                'title' => 'Genel Güvenlik Önerileri',
                'recommendations' => [
                    'HTTPS kullanın (production\'da zorunlu)',
                    'Güvenlik header\'ları ekleyin (X-Frame-Options, X-Content-Type-Options, vb.)',
                    'Rate limiting uygulayın',
                    'Input validation her zaman backend\'de yapın',
                    'Hassas bilgileri loglamayın',
                    'Düzenli güvenlik güncellemeleri yapın',
                    'Error reporting\'i production\'da kapatın',
                ],
            ],
        ];
    }
}
