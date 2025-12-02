<?php

namespace App\Services;

use App\Models\BackupLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use ZipArchive;

/**
 * Backup Service
 * 
 * Veritabanı ve dosya yedekleme işlemlerini yönetir
 * 
 * Özellikler:
 * - Veritabanı yedekleme (SQL dump)
 * - Dosya yedekleme (storage, public)
 * - Tam yedekleme (veritabanı + dosyalar)
 * - Yedek geri yükleme
 * - Yedek indirme
 * - Otomatik temizleme
 */
class BackupService
{
    /**
     * Yedekleme dizini
     */
    private string $backupPath;

    /**
     * Maksimum yedek saklama süresi (gün)
     */
    private int $retentionDays;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        $this->retentionDays = config('backup.retention_days', 30);
        
        // Yedekleme dizinini oluştur
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Veritabanı yedekleme
     * 
     * @param int|null $userId Yedeklemeyi oluşturan kullanıcı ID
     * @return BackupLog
     * @throws \Exception
     */
    public function backupDatabase(?int $userId = null): BackupLog
    {
        // Yedekleme kaydı oluştur
        $backupLog = BackupLog::createBackup(BackupLog::TYPE_DATABASE, $userId);
        
        try {
            // Yedeklemeyi başlat
            $backupLog->start();
            
            // Dosya adı oluştur
            $fileName = 'database_' . date('Y-m-d_His') . '.sql';
            $filePath = $this->backupPath . '/' . $fileName;
            
            // Veritabanı bilgilerini al
            $connection = config('database.default');
            $database = config("database.connections.{$connection}.database");
            $username = config("database.connections.{$connection}.username");
            $password = config("database.connections.{$connection}.password");
            $host = config("database.connections.{$connection}.host");
            $port = config("database.connections.{$connection}.port", 3306);
            
            // mysqldump komutu
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($filePath)
            );
            
            // Komutu çalıştır
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Veritabanı yedekleme komutu başarısız oldu');
            }
            
            // Dosya boyutunu kontrol et
            if (!File::exists($filePath) || File::size($filePath) === 0) {
                throw new \Exception('Yedekleme dosyası oluşturulamadı veya boş');
            }
            
            // Dosyayı sıkıştır
            $zipFileName = $fileName . '.gz';
            $zipFilePath = $this->backupPath . '/' . $zipFileName;
            
            $this->compressFile($filePath, $zipFilePath);
            
            // Orijinal SQL dosyasını sil
            File::delete($filePath);
            
            // Dosya boyutunu al
            $fileSize = File::size($zipFilePath);
            
            // Yedeklemeyi tamamla
            $backupLog->complete('backups/' . $zipFileName, $fileSize);
            
            // Eski yedekleri temizle
            $this->cleanupOldBackups(BackupLog::TYPE_DATABASE);
            
            return $backupLog;
            
        } catch (\Exception $e) {
            // Hata durumunda kaydı güncelle
            $backupLog->fail($e->getMessage());
            throw $e;
        }
    }

    /**
     * Dosya yedekleme
     * 
     * @param int|null $userId Yedeklemeyi oluşturan kullanıcı ID
     * @param array $directories Yedeklenecek dizinler
     * @return BackupLog
     * @throws \Exception
     */
    public function backupFiles(?int $userId = null, array $directories = []): BackupLog
    {
        // Yedekleme kaydı oluştur
        $backupLog = BackupLog::createBackup(BackupLog::TYPE_FILES, $userId);
        
        try {
            // Yedeklemeyi başlat
            $backupLog->start();
            
            // Varsayılan dizinler
            if (empty($directories)) {
                $directories = [
                    storage_path('app/public'),
                    public_path('arkaplan'),
                ];
            }
            
            // Dosya adı oluştur
            $fileName = 'files_' . date('Y-m-d_His') . '.zip';
            $filePath = $this->backupPath . '/' . $fileName;
            
            // ZIP arşivi oluştur
            $zip = new ZipArchive();
            
            if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('ZIP dosyası oluşturulamadı');
            }
            
            // Her dizini arşive ekle
            foreach ($directories as $directory) {
                if (File::exists($directory)) {
                    $this->addDirectoryToZip($zip, $directory, basename($directory));
                }
            }
            
            $zip->close();
            
            // Dosya boyutunu kontrol et
            if (!File::exists($filePath) || File::size($filePath) === 0) {
                throw new \Exception('Yedekleme dosyası oluşturulamadı veya boş');
            }
            
            // Dosya boyutunu al
            $fileSize = File::size($filePath);
            
            // Yedeklemeyi tamamla
            $backupLog->complete('backups/' . $fileName, $fileSize);
            
            // Eski yedekleri temizle
            $this->cleanupOldBackups(BackupLog::TYPE_FILES);
            
            return $backupLog;
            
        } catch (\Exception $e) {
            // Hata durumunda kaydı güncelle
            $backupLog->fail($e->getMessage());
            throw $e;
        }
    }

    /**
     * Tam yedekleme (veritabanı + dosyalar)
     * 
     * @param int|null $userId Yedeklemeyi oluşturan kullanıcı ID
     * @return BackupLog
     * @throws \Exception
     */
    public function backupFull(?int $userId = null): BackupLog
    {
        // Yedekleme kaydı oluştur
        $backupLog = BackupLog::createBackup(BackupLog::TYPE_FULL, $userId);
        
        try {
            // Yedeklemeyi başlat
            $backupLog->start();
            
            // Geçici dizin oluştur
            $tempDir = $this->backupPath . '/temp_' . time();
            File::makeDirectory($tempDir, 0755, true);
            
            // Veritabanı yedekle
            $dbBackup = $this->backupDatabase($userId);
            $dbFile = storage_path('app/' . $dbBackup->file_path);
            
            // Dosyaları yedekle
            $filesBackup = $this->backupFiles($userId);
            $filesFile = storage_path('app/' . $filesBackup->file_path);
            
            // Her iki dosyayı geçici dizine kopyala
            File::copy($dbFile, $tempDir . '/' . basename($dbFile));
            File::copy($filesFile, $tempDir . '/' . basename($filesFile));
            
            // Tam yedek ZIP'i oluştur
            $fileName = 'full_' . date('Y-m-d_His') . '.zip';
            $filePath = $this->backupPath . '/' . $fileName;
            
            $zip = new ZipArchive();
            
            if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('ZIP dosyası oluşturulamadı');
            }
            
            // Geçici dizindeki dosyaları ekle
            $files = File::files($tempDir);
            foreach ($files as $file) {
                $zip->addFile($file->getPathname(), $file->getFilename());
            }
            
            $zip->close();
            
            // Geçici dizini temizle
            File::deleteDirectory($tempDir);
            
            // Alt yedekleme kayıtlarını sil (artık tam yedekte var)
            $dbBackup->delete();
            $filesBackup->delete();
            File::delete($dbFile);
            File::delete($filesFile);
            
            // Dosya boyutunu al
            $fileSize = File::size($filePath);
            
            // Yedeklemeyi tamamla
            $backupLog->complete('backups/' . $fileName, $fileSize);
            
            // Eski yedekleri temizle
            $this->cleanupOldBackups(BackupLog::TYPE_FULL);
            
            return $backupLog;
            
        } catch (\Exception $e) {
            // Hata durumunda kaydı güncelle
            $backupLog->fail($e->getMessage());
            
            // Geçici dosyaları temizle
            if (isset($tempDir) && File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
            
            throw $e;
        }
    }

    /**
     * Yedek geri yükleme
     * 
     * @param int $backupId Yedek ID
     * @return bool
     * @throws \Exception
     */
    public function restore(int $backupId): bool
    {
        $backup = BackupLog::findOrFail($backupId);
        
        if (!$backup->isCompleted()) {
            throw new \Exception('Sadece tamamlanmış yedekler geri yüklenebilir');
        }
        
        $filePath = storage_path('app/' . $backup->file_path);
        
        if (!File::exists($filePath)) {
            throw new \Exception('Yedek dosyası bulunamadı');
        }
        
        try {
            switch ($backup->type) {
                case BackupLog::TYPE_DATABASE:
                    return $this->restoreDatabase($filePath);
                    
                case BackupLog::TYPE_FILES:
                    return $this->restoreFiles($filePath);
                    
                case BackupLog::TYPE_FULL:
                    return $this->restoreFull($filePath);
                    
                default:
                    throw new \Exception('Bilinmeyen yedek tipi');
            }
        } catch (\Exception $e) {
            throw new \Exception('Yedek geri yükleme başarısız: ' . $e->getMessage());
        }
    }

    /**
     * Veritabanı yedek geri yükleme
     * 
     * @param string $filePath
     * @return bool
     * @throws \Exception
     */
    private function restoreDatabase(string $filePath): bool
    {
        // Sıkıştırılmış dosyayı aç
        $sqlFile = str_replace('.gz', '', $filePath);
        $this->decompressFile($filePath, $sqlFile);
        
        // Veritabanı bilgilerini al
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");
        $host = config("database.connections.{$connection}.host");
        $port = config("database.connections.{$connection}.port", 3306);
        
        // mysql komutu ile geri yükle
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s < %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($sqlFile)
        );
        
        exec($command, $output, $returnCode);
        
        // Geçici SQL dosyasını sil
        File::delete($sqlFile);
        
        if ($returnCode !== 0) {
            throw new \Exception('Veritabanı geri yükleme komutu başarısız oldu');
        }
        
        // Cache'i temizle
        Artisan::call('cache:clear');
        
        return true;
    }

    /**
     * Dosya yedek geri yükleme
     * 
     * @param string $filePath
     * @return bool
     * @throws \Exception
     */
    private function restoreFiles(string $filePath): bool
    {
        $zip = new ZipArchive();
        
        if ($zip->open($filePath) !== true) {
            throw new \Exception('ZIP dosyası açılamadı');
        }
        
        // Geçici dizine çıkart
        $tempDir = $this->backupPath . '/restore_temp_' . time();
        File::makeDirectory($tempDir, 0755, true);
        
        $zip->extractTo($tempDir);
        $zip->close();
        
        // Dosyaları hedef dizinlere kopyala
        $directories = [
            'public' => storage_path('app/public'),
            'arkaplan' => public_path('arkaplan'),
        ];
        
        foreach ($directories as $source => $destination) {
            $sourcePath = $tempDir . '/' . $source;
            
            if (File::exists($sourcePath)) {
                // Hedef dizini temizle
                if (File::exists($destination)) {
                    File::deleteDirectory($destination);
                }
                
                // Yeni dosyaları kopyala
                File::copyDirectory($sourcePath, $destination);
            }
        }
        
        // Geçici dizini temizle
        File::deleteDirectory($tempDir);
        
        return true;
    }

    /**
     * Tam yedek geri yükleme
     * 
     * @param string $filePath
     * @return bool
     * @throws \Exception
     */
    private function restoreFull(string $filePath): bool
    {
        $zip = new ZipArchive();
        
        if ($zip->open($filePath) !== true) {
            throw new \Exception('ZIP dosyası açılamadı');
        }
        
        // Geçici dizine çıkart
        $tempDir = $this->backupPath . '/restore_temp_' . time();
        File::makeDirectory($tempDir, 0755, true);
        
        $zip->extractTo($tempDir);
        $zip->close();
        
        // Veritabanı yedeklerini bul ve geri yükle
        $dbFiles = File::glob($tempDir . '/database_*.sql.gz');
        if (!empty($dbFiles)) {
            $this->restoreDatabase($dbFiles[0]);
        }
        
        // Dosya yedeklerini bul ve geri yükle
        $fileBackups = File::glob($tempDir . '/files_*.zip');
        if (!empty($fileBackups)) {
            $this->restoreFiles($fileBackups[0]);
        }
        
        // Geçici dizini temizle
        File::deleteDirectory($tempDir);
        
        return true;
    }

    /**
     * Yedekleri listele
     * 
     * @param string|null $type Yedek tipi (null ise tümü)
     * @param int $limit Limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listBackups(?string $type = null, int $limit = 50)
    {
        $query = BackupLog::with('creator')
            ->latest('created_at');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->limit($limit)->get();
    }

    /**
     * Yedek sil
     * 
     * @param int $backupId
     * @return bool
     * @throws \Exception
     */
    public function deleteBackup(int $backupId): bool
    {
        $backup = BackupLog::findOrFail($backupId);
        
        // Dosyayı sil
        if ($backup->file_path) {
            $filePath = storage_path('app/' . $backup->file_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
        
        // Kaydı sil
        return $backup->delete();
    }

    /**
     * Eski yedekleri temizle
     * 
     * @param string|null $type Yedek tipi (null ise tümü)
     * @return int Silinen yedek sayısı
     */
    public function cleanupOldBackups(?string $type = null): int
    {
        $query = BackupLog::where('created_at', '<', now()->subDays($this->retentionDays));
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $backups = $query->get();
        $count = 0;
        
        foreach ($backups as $backup) {
            try {
                $this->deleteBackup($backup->id);
                $count++;
            } catch (\Exception $e) {
                // Hata durumunda devam et
                continue;
            }
        }
        
        return $count;
    }

    /**
     * Yedek indirme
     * 
     * @param int $backupId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function downloadBackup(int $backupId)
    {
        $backup = BackupLog::findOrFail($backupId);
        
        if (!$backup->isCompleted()) {
            throw new \Exception('Sadece tamamlanmış yedekler indirilebilir');
        }
        
        $filePath = storage_path('app/' . $backup->file_path);
        
        if (!File::exists($filePath)) {
            throw new \Exception('Yedek dosyası bulunamadı');
        }
        
        return response()->download($filePath);
    }

    /**
     * Yedekleme istatistikleri
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total_backups' => BackupLog::count(),
            'completed_backups' => BackupLog::completed()->count(),
            'failed_backups' => BackupLog::failed()->count(),
            'total_size' => BackupLog::getTotalSizeHuman(),
            'last_backup' => BackupLog::getLastSuccessful(),
            'success_rate' => BackupLog::getSuccessRate(30),
            'by_type' => [
                'database' => BackupLog::ofType(BackupLog::TYPE_DATABASE)->count(),
                'files' => BackupLog::ofType(BackupLog::TYPE_FILES)->count(),
                'full' => BackupLog::ofType(BackupLog::TYPE_FULL)->count(),
            ],
        ];
    }

    /**
     * Dosyayı sıkıştır (gzip)
     * 
     * @param string $source Kaynak dosya
     * @param string $destination Hedef dosya
     * @return bool
     * @throws \Exception
     */
    private function compressFile(string $source, string $destination): bool
    {
        if (!File::exists($source)) {
            throw new \Exception('Kaynak dosya bulunamadı');
        }
        
        $sourceHandle = fopen($source, 'rb');
        $destHandle = gzopen($destination, 'wb9');
        
        if (!$sourceHandle || !$destHandle) {
            throw new \Exception('Dosya sıkıştırma başarısız');
        }
        
        while (!feof($sourceHandle)) {
            gzwrite($destHandle, fread($sourceHandle, 1024 * 512));
        }
        
        fclose($sourceHandle);
        gzclose($destHandle);
        
        return true;
    }

    /**
     * Sıkıştırılmış dosyayı aç (gzip)
     * 
     * @param string $source Kaynak dosya (.gz)
     * @param string $destination Hedef dosya
     * @return bool
     * @throws \Exception
     */
    private function decompressFile(string $source, string $destination): bool
    {
        if (!File::exists($source)) {
            throw new \Exception('Kaynak dosya bulunamadı');
        }
        
        $sourceHandle = gzopen($source, 'rb');
        $destHandle = fopen($destination, 'wb');
        
        if (!$sourceHandle || !$destHandle) {
            throw new \Exception('Dosya açma başarısız');
        }
        
        while (!gzeof($sourceHandle)) {
            fwrite($destHandle, gzread($sourceHandle, 1024 * 512));
        }
        
        gzclose($sourceHandle);
        fclose($destHandle);
        
        return true;
    }

    /**
     * Dizini ZIP arşivine ekle (recursive)
     * 
     * @param ZipArchive $zip
     * @param string $directory Dizin yolu
     * @param string $zipPath ZIP içindeki yol
     * @return void
     */
    private function addDirectoryToZip(ZipArchive $zip, string $directory, string $zipPath): void
    {
        $files = File::allFiles($directory);
        
        foreach ($files as $file) {
            $relativePath = $zipPath . '/' . $file->getRelativePathname();
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }

    /**
     * Disk alanı kontrolü
     * 
     * @param int $requiredSpace Gereken alan (byte)
     * @return bool
     */
    public function checkDiskSpace(int $requiredSpace = 0): bool
    {
        $freeSpace = disk_free_space($this->backupPath);
        
        if ($requiredSpace > 0) {
            return $freeSpace >= $requiredSpace;
        }
        
        // En az 1GB boş alan olmalı
        return $freeSpace >= (1024 * 1024 * 1024);
    }

    /**
     * Yedekleme dizini boyutu
     * 
     * @return array
     */
    public function getBackupDirectorySize(): array
    {
        $totalSize = 0;
        $fileCount = 0;
        
        if (File::exists($this->backupPath)) {
            $files = File::allFiles($this->backupPath);
            
            foreach ($files as $file) {
                $totalSize += $file->getSize();
                $fileCount++;
            }
        }
        
        return [
            'total_size' => $totalSize,
            'total_size_human' => $this->formatBytes($totalSize),
            'file_count' => $fileCount,
            'path' => $this->backupPath,
        ];
    }

    /**
     * Byte'ları okunabilir formata çevir
     * 
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Yedekleme yapılandırmasını kontrol et
     * 
     * @return array
     */
    public function checkConfiguration(): array
    {
        $issues = [];
        
        // Dizin yazılabilir mi?
        if (!File::isWritable($this->backupPath)) {
            $issues[] = 'Yedekleme dizini yazılabilir değil: ' . $this->backupPath;
        }
        
        // mysqldump mevcut mu? (Windows ve Linux uyumlu)
        $mysqldumpCheck = $this->commandExists('mysqldump');
        if (!$mysqldumpCheck) {
            $issues[] = 'mysqldump komutu bulunamadı (MySQL/MariaDB kurulu değil veya PATH\'e eklenmemiş)';
        }
        
        // mysql mevcut mu? (Windows ve Linux uyumlu)
        $mysqlCheck = $this->commandExists('mysql');
        if (!$mysqlCheck) {
            $issues[] = 'mysql komutu bulunamadı (MySQL/MariaDB kurulu değil veya PATH\'e eklenmemiş)';
        }
        
        // ZIP extension yüklü mü?
        if (!extension_loaded('zip')) {
            $issues[] = 'PHP ZIP extension yüklü değil';
        }
        
        // Disk alanı yeterli mi?
        if (!$this->checkDiskSpace()) {
            $issues[] = 'Yetersiz disk alanı (en az 1GB gerekli)';
        }
        
        return [
            'is_configured' => empty($issues),
            'issues' => $issues,
            'backup_path' => $this->backupPath,
            'retention_days' => $this->retentionDays,
        ];
    }

    /**
     * Komutun sistemde mevcut olup olmadığını kontrol et
     * Windows ve Linux uyumlu
     * 
     * @param string $command
     * @return bool
     */
    private function commandExists(string $command): bool
    {
        // Windows için
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("where $command 2>nul", $output, $returnCode);
            return $returnCode === 0;
        }
        
        // Linux/Unix için
        exec("which $command 2>/dev/null", $output, $returnCode);
        return $returnCode === 0;
    }
}
