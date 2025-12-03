# 🚀 Production Deployment Checklist

## Pre-Deployment Kontrol Listesi

### 1. ✅ Veritabanı Hazırlığı

- [ ] **Veritabanı Yedeği Alındı**
  ```bash
  php artisan backup:run --only-db
  ```

- [ ] **Migration Dosyaları Test Edildi**
  ```bash
  # Test ortamında çalıştır
  php artisan migrate --pretend
  ```

- [ ] **Seeder Dosyaları Hazır**
  ```bash
  php artisan db:seed --class=GameSeeder
  ```

- [ ] **Database Indexes Kontrol Edildi**
  - games tablosu: slug, status
  - game-specific tablolar: game_id
  - performance indexes migration çalıştırıldı

### 2. ✅ Environment Konfigürasyonu

- [ ] **.env Dosyası Oluşturuldu**
  ```bash
  cp .env.example .env
  ```

- [ ] **APP_KEY Generate Edildi**
  ```bash
  php artisan key:generate
  ```

- [ ] **Environment Değişkenleri Ayarlandı**
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `APP_URL=https://takimsistemi.com`
  - `APP_DOMAIN=takimsistemi.com`

### 3. ✅ Redis Konfigürasyonu

- [ ] **Redis Kuruldu ve Çalışıyor**
  ```bash
  redis-cli ping
  # Beklenen: PONG
  ```

- [ ] **Redis Şifresi Ayarlandı** (Production için önerilir)
  ```bash
  # redis.conf
  requirepass your_strong_password
  ```

- [ ] **Redis Database'leri Ayrıldı**
  - DB 0: Genel kullanım
  - DB 1: Cache
  - DB 2: Queue
  - DB 3: Session

- [ ] **.env Redis Ayarları**
  ```env
  REDIS_HOST=127.0.0.1
  REDIS_PASSWORD=your_strong_password
  REDIS_PORT=6379
  REDIS_DB=0
  REDIS_CACHE_DB=1
  REDIS_QUEUE_DB=2
  REDIS_SESSION_DB=3
  ```

### 4. ✅ Cache Driver Ayarları

- [ ] **Cache Driver Redis Olarak Ayarlandı**
  ```env
  CACHE_DRIVER=redis
  CACHE_PREFIX=takimsistemi_cache
  ```

- [ ] **Config Cache Temizlendi**
  ```bash
  php artisan config:clear
  php artisan cache:clear
  ```

- [ ] **Cache Test Edildi**
  ```bash
  php artisan tinker
  >>> Cache::put('test', 'value', 60);
  >>> Cache::get('test');
  ```

### 5. ✅ Session Driver Ayarları

- [ ] **Session Driver Redis Olarak Ayarlandı**
  ```env
  SESSION_DRIVER=redis
  SESSION_LIFETIME=120
  SESSION_ENCRYPT=true
  SESSION_DOMAIN=.takimsistemi.com
  SESSION_SECURE_COOKIE=true
  ```

- [ ] **Session Test Edildi**
  ```bash
  # Browser'da login yap ve subdomain'ler arası geçiş test et
  ```

### 6. ✅ Queue Konfigürasyonu

- [ ] **Queue Driver Redis Olarak Ayarlandı**
  ```env
  QUEUE_CONNECTION=redis
  ```

- [ ] **Queue Worker Başlatıldı**
  ```bash
  php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
  ```

- [ ] **Supervisor Konfigürasyonu Oluşturuldu**
  ```ini
  [program:takimsistemi-worker]
  process_name=%(program_name)s_%(process_num)02d
  command=php /path/to/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
  autostart=true
  autorestart=true
  stopasgroup=true
  killasgroup=true
  user=www-data
  numprocs=4
  redirect_stderr=true
  stdout_logfile=/path/to/storage/logs/worker.log
  stopwaitsecs=3600
  ```

### 7. ✅ Web Server Konfigürasyonu

- [ ] **Apache/Nginx Konfigürasyonu**
  - Wildcard subdomain desteği eklendi
  - SSL sertifikaları kuruldu
  - HTTPS redirect aktif

- [ ] **Virtual Host Örneği (Apache)**
  ```apache
  <VirtualHost *:443>
      ServerName takimsistemi.com
      ServerAlias *.takimsistemi.com
      
      DocumentRoot /var/www/takimsistemi/public
      
      SSLEngine on
      SSLCertificateFile /path/to/cert.pem
      SSLCertificateKeyFile /path/to/key.pem
      
      <Directory /var/www/takimsistemi/public>
          AllowOverride All
          Require all granted
      </Directory>
  </VirtualHost>
  ```

### 8. ✅ SSL/TLS Sertifikaları

- [ ] **Let's Encrypt Sertifikası Kuruldu**
  ```bash
  certbot --apache -d takimsistemi.com -d *.takimsistemi.com
  ```

- [ ] **Auto-Renewal Aktif**
  ```bash
  certbot renew --dry-run
  ```

### 9. ✅ Optimizasyon

- [ ] **Composer Optimize Edildi**
  ```bash
  composer install --optimize-autoloader --no-dev
  ```

- [ ] **Laravel Optimize Edildi**
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache
  ```

- [ ] **OPcache Aktif**
  ```ini
  ; php.ini
  opcache.enable=1
  opcache.memory_consumption=256
  opcache.interned_strings_buffer=16
  opcache.max_accelerated_files=10000
  opcache.revalidate_freq=2
  ```

### 10. ✅ Güvenlik

- [ ] **File Permissions Ayarlandı**
  ```bash
  chown -R www-data:www-data /var/www/takimsistemi
  chmod -R 755 /var/www/takimsistemi
  chmod -R 775 /var/www/takimsistemi/storage
  chmod -R 775 /var/www/takimsistemi/bootstrap/cache
  ```

- [ ] **Hassas Dosyalar Korundu**
  ```bash
  chmod 600 .env
  ```

- [ ] **CSRF Protection Aktif**
  - Sanctum stateful domains ayarlandı

- [ ] **Rate Limiting Aktif**
  ```env
  RATE_LIMIT_API=60
  RATE_LIMIT_WEB=1000
  ```

### 11. ✅ Monitoring & Logging

- [ ] **Log Rotation Ayarlandı**
  ```bash
  # /etc/logrotate.d/takimsistemi
  /var/www/takimsistemi/storage/logs/*.log {
      daily
      missingok
      rotate 14
      compress
      delaycompress
      notifempty
      create 0640 www-data www-data
      sharedscripts
  }
  ```

- [ ] **Error Tracking (Sentry) Kuruldu**
  ```env
  SENTRY_LARAVEL_DSN=your_sentry_dsn
  ```

- [ ] **Slow Query Logging Aktif**
  ```env
  LOG_SLOW_QUERIES=true
  SLOW_QUERY_THRESHOLD=1000
  ```

### 12. ✅ Backup Stratejisi

- [ ] **Otomatik Backup Ayarlandı**
  ```bash
  # Crontab
  0 2 * * * cd /var/www/takimsistemi && php artisan backup:run --only-db
  0 3 * * 0 cd /var/www/takimsistemi && php artisan backup:run
  ```

- [ ] **Backup Retention Policy**
  ```env
  BACKUP_RETENTION_DAYS=30
  ```

### 13. ✅ Cron Jobs

- [ ] **Laravel Scheduler Ayarlandı**
  ```bash
  # Crontab
  * * * * * cd /var/www/takimsistemi && php artisan schedule:run >> /dev/null 2>&1
  ```

### 14. ✅ Testing

- [ ] **Smoke Tests Çalıştırıldı**
  ```bash
  php artisan test --testsuite=Feature
  ```

- [ ] **Manual Testing**
  - [ ] Ana sayfa yükleniyor
  - [ ] Subdomain routing çalışıyor
  - [ ] Login/Register çalışıyor
  - [ ] Session subdomain'ler arası persist ediyor
  - [ ] Game switching çalışıyor
  - [ ] Cache çalışıyor
  - [ ] Queue jobs çalışıyor

### 15. ✅ DNS Ayarları

- [ ] **A Record**
  ```
  takimsistemi.com → Server IP
  ```

- [ ] **Wildcard A Record**
  ```
  *.takimsistemi.com → Server IP
  ```

- [ ] **DNS Propagation Kontrol**
  ```bash
  nslookup takimsistemi.com
  nslookup pubg.takimsistemi.com
  ```

## Deployment Adımları

### 1. Kod Deployment

```bash
# Git pull
cd /var/www/takimsistemi
git pull origin main

# Dependencies
composer install --optimize-autoloader --no-dev

# NPM build
npm ci
npm run build

# Permissions
chown -R www-data:www-data .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

### 2. Database Migration

```bash
# Backup
php artisan backup:run --only-db

# Migrate
php artisan migrate --force

# Seed (sadece ilk deployment)
php artisan db:seed --class=GameSeeder
```

### 3. Cache & Optimization

```bash
# Clear old cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 4. Queue & Workers

```bash
# Restart queue workers
sudo supervisorctl restart takimsistemi-worker:*
```

### 5. Web Server

```bash
# Apache
sudo systemctl reload apache2

# Nginx
sudo systemctl reload nginx
```

## Post-Deployment Verification

### 1. Health Checks

```bash
# Application health
curl https://takimsistemi.com/health

# Redis connection
redis-cli -h 127.0.0.1 -p 6379 -a your_password ping

# Database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### 2. Monitoring

- [ ] Error logs kontrol edildi
- [ ] Performance metrics kontrol edildi
- [ ] Queue jobs çalışıyor
- [ ] Scheduled tasks çalışıyor

### 3. User Acceptance

- [ ] Test kullanıcıları ile test edildi
- [ ] Tüm kritik özellikler çalışıyor
- [ ] Performance kabul edilebilir seviyede

## Rollback Planı

Bir sorun olursa:

```bash
# 1. Önceki versiyona dön
git checkout previous_tag

# 2. Dependencies
composer install --optimize-autoloader --no-dev

# 3. Database rollback (gerekirse)
php artisan migrate:rollback --step=1

# 4. Cache rebuild
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart services
sudo supervisorctl restart takimsistemi-worker:*
sudo systemctl reload apache2
```

## Support & Troubleshooting

### Redis Sorunları

```bash
# Redis status
sudo systemctl status redis

# Redis logs
sudo tail -f /var/log/redis/redis-server.log

# Redis connection test
redis-cli -h 127.0.0.1 -p 6379 -a your_password
```

### Queue Sorunları

```bash
# Queue status
php artisan queue:monitor

# Failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Cache Sorunları

```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Redis cache flush
redis-cli -h 127.0.0.1 -p 6379 -a your_password -n 1 FLUSHDB
```

## İletişim

Sorun durumunda:
- Logs: `/var/www/takimsistemi/storage/logs/`
- Sentry: Error tracking dashboard
- Monitoring: Server monitoring tools

---

**Son Güncelleme:** 2025-12-03
**Versiyon:** 1.0.0
