<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use App\Models\BackupLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Backup Controller
 * 
 * Yedekleme yönetimi:
 * - Yedek oluşturma (database, files, full)
 * - Yedek listesi
 * - Yedek geri yükleme
 * - Yedek indirme
 * - Yedek silme
 * - Yedekleme istatistikleri
 */
class BackupController extends Controller
{
    /**
     * Backup Service
     */
    protected BackupService $backupService;

    /**
     * Constructor
     */
    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Yedekleme ana sayfası
     * GET /admin/backup
     */
    public function index()
    {
        // Yedekleme yapılandırmasını kontrol et
        $configuration = $this->backupService->checkConfiguration();
        
        // Yedekleme dizini bilgilerini al
        $directoryInfo = $this->backupService->getBackupDirectorySize();
        
        // İstatistikleri al
        $statistics = $this->backupService->getStatistics();
        
        // Son yedekleri al
        $recentBackups = $this->backupService->listBackups(null, 10);
        
        return view('admin.system.backup', compact(
            'configuration',
            'directoryInfo',
            'statistics',
            'recentBackups'
        ));
    }

    /**
     * Yedekleri listele
     * GET /admin/backup/list
     */
    public function list(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'nullable|in:database,files,full',
            'status' => 'nullable|in:pending,running,completed,failed',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $type = $request->input('type');
        $status = $request->input('status');
        $limit = $request->input('limit', 50);

        $query = BackupLog::with('creator')->latest('created_at');

        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $backups = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $backups,
        ]);
    }

    /**
     * Yedek oluşturma formu
     * GET /admin/backup/create
     */
    public function create()
    {
        // Disk alanı kontrolü
        $hasDiskSpace = $this->backupService->checkDiskSpace();
        
        // Yapılandırma kontrolü
        $configuration = $this->backupService->checkConfiguration();
        
        return view('admin.system.backup-create', compact(
            'hasDiskSpace',
            'configuration'
        ));
    }

    /**
     * Yedek oluştur
     * POST /admin/backup
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:database,files,full',
        ]);

        $type = $request->input('type');

        try {
            // Disk alanı kontrolü
            if (!$this->backupService->checkDiskSpace()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yetersiz disk alanı. En az 1GB boş alan gerekli.',
                ], 400);
            }

            // Yapılandırma kontrolü
            $configuration = $this->backupService->checkConfiguration();
            if (!$configuration['is_configured']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yedekleme yapılandırması hatalı',
                    'issues' => $configuration['issues'],
                ], 400);
            }

            // Yedekleme oluştur
            $backup = match ($type) {
                'database' => $this->backupService->backupDatabase(auth()->id()),
                'files' => $this->backupService->backupFiles(auth()->id()),
                'full' => $this->backupService->backupFull(auth()->id()),
            };

            // Admin aktivitesini logla
            Log::info('Yedek oluşturuldu', [
                'admin_id' => auth()->id(),
                'backup_id' => $backup->id,
                'type' => $type,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yedekleme başarıyla oluşturuldu',
                'data' => $backup,
            ]);

        } catch (\Exception $e) {
            Log::error('Yedekleme hatası', [
                'admin_id' => auth()->id(),
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Yedekleme oluşturulurken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Yedek detayları
     * GET /admin/backup/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $backup = BackupLog::with('creator')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $backup,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Yedek bulunamadı',
            ], 404);
        }
    }

    /**
     * Yedek geri yükle
     * POST /admin/backup/{id}/restore
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $backup = BackupLog::findOrFail($id);

            // Güvenlik kontrolü - sadece tamamlanmış yedekler geri yüklenebilir
            if (!$backup->isCompleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sadece tamamlanmış yedekler geri yüklenebilir',
                ], 400);
            }

            // Yedek geri yükle
            $this->backupService->restore($id);

            // Admin aktivitesini logla
            Log::warning('Yedek geri yüklendi', [
                'admin_id' => auth()->id(),
                'backup_id' => $id,
                'type' => $backup->type,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yedek başarıyla geri yüklendi',
            ]);

        } catch (\Exception $e) {
            Log::error('Yedek geri yükleme hatası', [
                'admin_id' => auth()->id(),
                'backup_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Yedek geri yüklenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Yedek indir
     * GET /admin/backup/{id}/download
     */
    public function download(int $id)
    {
        try {
            // Admin aktivitesini logla
            Log::info('Yedek indirildi', [
                'admin_id' => auth()->id(),
                'backup_id' => $id,
            ]);

            return $this->backupService->downloadBackup($id);

        } catch (\Exception $e) {
            Log::error('Yedek indirme hatası', [
                'admin_id' => auth()->id(),
                'backup_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Yedek indirilirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Yedek sil
     * DELETE /admin/backup/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $backup = BackupLog::findOrFail($id);
            
            // Yedek sil
            $this->backupService->deleteBackup($id);

            // Admin aktivitesini logla
            Log::info('Yedek silindi', [
                'admin_id' => auth()->id(),
                'backup_id' => $id,
                'type' => $backup->type,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yedek başarıyla silindi',
            ]);

        } catch (\Exception $e) {
            Log::error('Yedek silme hatası', [
                'admin_id' => auth()->id(),
                'backup_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Yedek silinirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eski yedekleri temizle
     * POST /admin/backup/cleanup
     */
    public function cleanup(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'nullable|in:database,files,full',
        ]);

        $type = $request->input('type');

        try {
            $deletedCount = $this->backupService->cleanupOldBackups($type);

            // Admin aktivitesini logla
            Log::info('Eski yedekler temizlendi', [
                'admin_id' => auth()->id(),
                'type' => $type,
                'deleted_count' => $deletedCount,
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} adet eski yedek temizlendi",
                'deleted_count' => $deletedCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Yedek temizleme hatası', [
                'admin_id' => auth()->id(),
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Yedekler temizlenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Yedekleme istatistikleri
     * GET /admin/backup/statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->backupService->getStatistics();
            $directoryInfo = $this->backupService->getBackupDirectorySize();

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $statistics,
                    'directory' => $directoryInfo,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İstatistikler alınırken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Yapılandırma kontrolü
     * GET /admin/backup/check-configuration
     */
    public function checkConfiguration(): JsonResponse
    {
        try {
            $configuration = $this->backupService->checkConfiguration();

            return response()->json([
                'success' => true,
                'data' => $configuration,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Yapılandırma kontrol edilirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disk alanı kontrolü
     * GET /admin/backup/check-disk-space
     */
    public function checkDiskSpace(): JsonResponse
    {
        try {
            $hasDiskSpace = $this->backupService->checkDiskSpace();
            $directoryInfo = $this->backupService->getBackupDirectorySize();

            return response()->json([
                'success' => true,
                'data' => [
                    'has_disk_space' => $hasDiskSpace,
                    'directory_info' => $directoryInfo,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Disk alanı kontrol edilirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }
}
