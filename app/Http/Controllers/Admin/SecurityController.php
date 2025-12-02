<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SecurityScanService;
use Illuminate\Http\Request;

/**
 * Güvenlik yönetimi controller'ı
 */
class SecurityController extends Controller
{
    protected SecurityScanService $securityService;

    public function __construct(SecurityScanService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Güvenlik tarama sayfası
     */
    public function index()
    {
        return view('admin.security.index');
    }

    /**
     * Güvenlik taraması çalıştır
     */
    public function scan(Request $request)
    {
        $type = $request->input('type', 'all');

        $results = match($type) {
            'sql' => ['sql_injection' => $this->securityService->scanSqlInjection()],
            'xss' => ['xss' => $this->securityService->scanXss()],
            'csrf' => ['csrf' => $this->securityService->scanCsrf()],
            default => $this->securityService->runFullScan(),
        };

        // Özet hesapla (tek tarama için)
        if (!isset($results['summary'])) {
            $totalVulnerabilities = 0;
            $criticalCount = 0;
            $warningCount = 0;

            foreach ($results as $result) {
                if (isset($result['vulnerabilities'])) {
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

            $results['summary'] = [
                'total_vulnerabilities' => $totalVulnerabilities,
                'critical' => $criticalCount,
                'warning' => $warningCount,
                'status' => $criticalCount > 0 ? 'critical' : ($warningCount > 0 ? 'warning' : 'safe'),
            ];
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'recommendations' => $this->securityService->getSecurityRecommendations(),
        ]);
    }

    /**
     * Güvenlik önerilerini getir
     */
    public function recommendations()
    {
        return response()->json([
            'success' => true,
            'recommendations' => $this->securityService->getSecurityRecommendations(),
        ]);
    }
}
