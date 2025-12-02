<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Yedekleme Ayarları
    |--------------------------------------------------------------------------
    |
    | Veritabanı ve dosya yedekleme işlemleri için yapılandırma ayarları
    |
    */

    /**
     * Yedekleme saklama süresi (gün)
     * Bu süreden eski yedekler otomatik olarak silinir
     */
    'retention_days' => env('BACKUP_RETENTION_DAYS', 30),

    /**
     * Yedekleme dizini
     * Varsayılan: storage/app/backups
     */
    'path' => env('BACKUP_PATH', storage_path('app/backups')),

    /**
     * Otomatik yedekleme aktif mi?
     */
    'auto_backup_enabled' => env('BACKUP_AUTO_ENABLED', false),

    /**
     * Otomatik yedekleme zamanlaması (cron expression)
     * Varsayılan: Her gün saat 02:00
     */
    'auto_backup_schedule' => env('BACKUP_AUTO_SCHEDULE', '0 2 * * *'),

    /**
     * Otomatik yedekleme tipi
     * Seçenekler: database, files, full
     */
    'auto_backup_type' => env('BACKUP_AUTO_TYPE', 'full'),

    /**
     * Yedeklenecek dizinler (dosya yedekleme için)
     */
    'directories' => [
        storage_path('app/public'),
        public_path('arkaplan'),
    ],

    /**
     * Yedekleme sıkıştırma seviyesi (0-9)
     * 0: Sıkıştırma yok
     * 9: Maksimum sıkıştırma
     */
    'compression_level' => env('BACKUP_COMPRESSION_LEVEL', 9),

    /**
     * Minimum disk alanı (byte)
     * Yedekleme için gereken minimum boş disk alanı
     */
    'min_disk_space' => env('BACKUP_MIN_DISK_SPACE', 1073741824), // 1GB

    /**
     * Yedekleme bildirimleri
     */
    'notifications' => [
        /**
         * Yedekleme tamamlandığında bildirim gönder
         */
        'on_success' => env('BACKUP_NOTIFY_SUCCESS', true),

        /**
         * Yedekleme başarısız olduğunda bildirim gönder
         */
        'on_failure' => env('BACKUP_NOTIFY_FAILURE', true),

        /**
         * Bildirim alacak email adresleri
         */
        'email' => env('BACKUP_NOTIFY_EMAIL', 'admin@example.com'),
    ],

    /**
     * Veritabanı yedekleme ayarları
     */
    'database' => [
        /**
         * Yedeklenecek tablolar
         * Boş bırakılırsa tüm tablolar yedeklenir
         */
        'tables' => [],

        /**
         * Yedeklenmeyecek tablolar
         */
        'exclude_tables' => [
            'cache',
            'cache_locks',
            'sessions',
            'jobs',
            'failed_jobs',
        ],

        /**
         * Sadece yapı yedeklensin mi? (data olmadan)
         */
        'structure_only' => false,
    ],

];
