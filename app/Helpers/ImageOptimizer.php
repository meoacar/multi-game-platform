<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * Image Optimization Helper
 * Resimleri optimize etmek ve farklı boyutlarda kaydetmek için
 */
class ImageOptimizer
{
    /**
     * Resmi optimize et ve farklı boyutlarda kaydet
     *
     * @param string $path Orijinal resim yolu
     * @param array $sizes Oluşturulacak boyutlar ['thumbnail' => [150, 150], 'medium' => [300, 300]]
     * @return array Oluşturulan resim yolları
     */
    public static function optimize($path, array $sizes = [])
    {
        $results = ['original' => $path];
        
        // Varsayılan boyutlar
        if (empty($sizes)) {
            $sizes = [
                'thumbnail' => [150, 150],
                'small' => [300, 300],
                'medium' => [600, 600],
                'large' => [1200, 1200],
            ];
        }
        
        // Her boyut için resim oluştur
        foreach ($sizes as $name => $dimensions) {
            $results[$name] = self::resize($path, $dimensions[0], $dimensions[1], $name);
        }
        
        return $results;
    }
    
    /**
     * Resmi yeniden boyutlandır
     *
     * @param string $path Orijinal resim yolu
     * @param int $width Genişlik
     * @param int $height Yükseklik
     * @param string $suffix Dosya adı soneki
     * @return string Yeni resim yolu
     */
    public static function resize($path, $width, $height, $suffix = '')
    {
        // Dosya bilgilerini al
        $pathInfo = pathinfo($path);
        $newFileName = $pathInfo['filename'] . '_' . $suffix . '.' . $pathInfo['extension'];
        $newPath = $pathInfo['dirname'] . '/' . $newFileName;
        
        // Resmi yükle ve boyutlandır
        $image = Image::make(Storage::path($path));
        
        // Aspect ratio'yu koru
        $image->fit($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        // Kaliteyi ayarla (JPEG için)
        if ($pathInfo['extension'] === 'jpg' || $pathInfo['extension'] === 'jpeg') {
            $image->encode('jpg', 80);
        }
        
        // Kaydet
        $image->save(Storage::path($newPath));
        
        return $newPath;
    }
    
    /**
     * WebP formatına dönüştür
     *
     * @param string $path Orijinal resim yolu
     * @return string WebP resim yolu
     */
    public static function convertToWebP($path)
    {
        $pathInfo = pathinfo($path);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        
        $image = Image::make(Storage::path($path));
        $image->encode('webp', 80);
        $image->save(Storage::path($webpPath));
        
        return $webpPath;
    }
    
    /**
     * Resim boyutunu al
     *
     * @param string $path Resim yolu
     * @return array ['width' => int, 'height' => int, 'size' => int]
     */
    public static function getImageInfo($path)
    {
        $fullPath = Storage::path($path);
        
        if (!file_exists($fullPath)) {
            return null;
        }
        
        $imageSize = getimagesize($fullPath);
        $fileSize = filesize($fullPath);
        
        return [
            'width' => $imageSize[0],
            'height' => $imageSize[1],
            'size' => $fileSize,
            'size_formatted' => self::formatBytes($fileSize),
            'mime' => $imageSize['mime'],
        ];
    }
    
    /**
     * Byte'ları okunabilir formata çevir
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Kullanılmayan resimleri temizle
     *
     * @param array $usedPaths Kullanılan resim yolları
     * @param string $directory Temizlenecek dizin
     * @return int Silinen dosya sayısı
     */
    public static function cleanUnusedImages(array $usedPaths, $directory = 'public/uploads')
    {
        $allFiles = Storage::files($directory);
        $deletedCount = 0;
        
        foreach ($allFiles as $file) {
            if (!in_array($file, $usedPaths)) {
                Storage::delete($file);
                $deletedCount++;
            }
        }
        
        return $deletedCount;
    }
}
