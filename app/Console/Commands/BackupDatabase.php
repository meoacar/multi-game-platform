<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--path= : Yedek dosyasının kaydedileceği klasör}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Veritabanının yedeğini al';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Veritabanı yedeği alınıyor...');
        
        try {
            // Veritabanı bilgileri
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            
            // Yedek klasörü
            $backupPath = $this->option('path') ?? base_path('storage/backups');
            
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            // Dosya adı
            $filename = $database . '_' . date('Y-m-d_His') . '.sql';
            $filepath = $backupPath . '/' . $filename;
            
            // mysqldump komutu
            $mysqldumpPath = 'G:\\xampp\\mysql\\bin\\mysqldump.exe';
            
            if (!file_exists($mysqldumpPath)) {
                $mysqldumpPath = 'mysqldump'; // System PATH'te varsa
            }
            
            $command = sprintf(
                '%s -h %s -u %s %s %s > %s 2>&1',
                $mysqldumpPath,
                $host,
                $username,
                $password ? "-p{$password}" : '',
                $database,
                $filepath
            );
            
            // Komutu çalıştır
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($filepath) && filesize($filepath) > 0) {
                $size = round(filesize($filepath) / 1024 / 1024, 2);
                
                $this->info("✅ Yedek başarıyla alındı!");
                $this->line("📁 Dosya: {$filepath}");
                $this->line("📊 Boyut: {$size} MB");
                
                // Eski yedekleri temizle (30 günden eski)
                $this->cleanOldBackups($backupPath);
                
                return Command::SUCCESS;
            } else {
                $this->error('❌ Yedek alınamadı!');
                $this->line('MySQL çalışmıyor olabilir.');
                
                // Alternatif: Dosya yedeği
                $this->warn('🔄 Alternatif yöntem deneniyor...');
                return $this->fileBackup($backupPath);
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Hata: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Dosya bazlı yedek al
     */
    private function fileBackup($backupPath)
    {
        try {
            $dataPath = 'G:\\xampp\\mysql\\data\\' . config('database.connections.mysql.database');
            
            if (!file_exists($dataPath)) {
                $this->error('❌ Veritabanı dosyaları bulunamadı!');
                return Command::FAILURE;
            }
            
            $backupFolder = $backupPath . '/files_' . date('Y-m-d_His');
            
            // Klasörü kopyala
            $this->recursiveCopy($dataPath, $backupFolder);
            
            $this->info("✅ Dosya yedeği alındı!");
            $this->line("📁 Klasör: {$backupFolder}");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Dosya yedeği alınamadı: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Klasörü recursive kopyala
     */
    private function recursiveCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recursiveCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        
        closedir($dir);
    }
    
    /**
     * 30 günden eski yedekleri temizle
     */
    private function cleanOldBackups($backupPath)
    {
        $files = glob($backupPath . '/*.sql');
        $now = time();
        $deleted = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 30 * 24 * 60 * 60) { // 30 gün
                    unlink($file);
                    $deleted++;
                }
            }
        }
        
        if ($deleted > 0) {
            $this->line("🗑️  {$deleted} eski yedek temizlendi (30+ gün)");
        }
    }
}
