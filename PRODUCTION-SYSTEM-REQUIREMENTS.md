# 🖥️ Production Sistem Gereksinimleri

## Minimum Sistem Gereksinimleri

### Donanım

#### Küçük Ölçek (100-500 aktif kullanıcı)
- **CPU**: 2 Core (2.5 GHz+)
- **RAM**: 4 GB
- **Disk**: 50 GB SSD
- **Network**: 100 Mbps

#### Orta Ölçek (500-2000 aktif kullanıcı)
- **CPU**: 4 Core (3.0 GHz+)
- **RAM**: 8 GB
- **Disk**: 100 GB SSD
- **Network**: 1 Gbps

#### Büyük Ölçek (2000+ aktif kullanıcı)
- **CPU**: 8+ Core (3.5 GHz+)
- **RAM**: 16+ GB
- **Disk**: 250+ GB SSD (NVMe önerilir)
- **Network**: 1+ Gbps

### İşletim Sistemi

**Önerilen:**
- Ubuntu 22.04 LTS (Jammy)
- Ubuntu 20.04 LTS (Focal)
- Debian 11 (Bullseye)
- CentOS 8 / Rocky Linux 8

**Desteklenen:**
- Ubuntu 18.04 LTS (Bionic) - EOL yakın
- Debian 10 (Buster)
- CentOS 7 - EOL yakın

## Yazılım Gereksinimleri

### Web Server

**Apache 2.4+** (Önerilir)
```bash
# Ubuntu/Debian
sudo apt install apache2

# Gerekli modüller
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers
sudo a2enmod deflate
sudo a2enmod expires
```

**veya Nginx 1.18+**
```bash
# Ubuntu/Debian
sudo apt install nginx
```

### PHP

**PHP 8.2** (Önerilir) veya **PHP 8.1**

```bash
# Ubuntu/Debian
sudo apt install php8.2 php8.2-fpm

# Gerekli PHP Extensions
sudo apt install \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-zip \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-redis \
    php8.2-intl \
    php8.2-soap
```

#### PHP Konfigürasyonu (php.ini)

```ini
; Memory & Execution
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
post_max_size = 50M
upload_max_filesize = 50M

; Session
session.gc_maxlifetime = 7200
session.cookie_lifetime = 0
session.cookie_secure = 1
session.cookie_httponly = 1
session.cookie_samesite = "Lax"

; OPcache (Performance)
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1
opcache.enable_cli = 0

; Error Reporting (Production)
display_errors = Off
display_startup_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
log_errors = On
error_log = /var/log/php/error.log

; Security
expose_php = Off
allow_url_fopen = On
allow_url_include = Off
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

; Date
date.timezone = Europe/Istanbul
```

### Database

**MySQL 8.0+** (Önerilir) veya **MariaDB 10.6+**

```bash
# MySQL 8.0
sudo apt install mysql-server-8.0

# MariaDB 10.6
sudo apt install mariadb-server
```

#### MySQL Konfigürasyonu (my.cnf)

```ini
[mysqld]
# Basic Settings
user = mysql
pid-file = /var/run/mysqld/mysqld.pid
socket = /var/run/mysqld/mysqld.sock
port = 3306
basedir = /usr
datadir = /var/lib/mysql
tmpdir = /tmp

# Character Set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

# InnoDB Settings
innodb_buffer_pool_size = 2G
innodb_log_file_size = 512M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1

# Connection Settings
max_connections = 200
max_connect_errors = 1000
wait_timeout = 600
interactive_timeout = 600

# Query Cache (MySQL 5.7)
# query_cache_type = 1
# query_cache_size = 128M

# Slow Query Log
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2

# Binary Log (Backup & Replication)
log_bin = /var/log/mysql/mysql-bin.log
expire_logs_days = 7
max_binlog_size = 100M

# Performance Schema
performance_schema = ON

[client]
default-character-set = utf8mb4
```

### Redis

**Redis 7.0+** (Önerilir) veya **Redis 6.2+**

```bash
# Ubuntu/Debian
sudo apt install redis-server

# Redis konfigürasyonu
sudo nano /etc/redis/redis.conf
```

#### Redis Konfigürasyonu

```conf
# Network
bind 127.0.0.1 ::1
protected-mode yes
port 6379

# Security
requirepass your_strong_password

# Memory
maxmemory 2gb
maxmemory-policy allkeys-lru

# Persistence
save 900 1
save 300 10
save 60 10000
appendonly yes
appendfsync everysec

# Performance
tcp-backlog 511
timeout 0
tcp-keepalive 300
databases 16

# Logging
loglevel notice
logfile /var/log/redis/redis-server.log
```

### Composer

**Composer 2.x**

```bash
# Composer kurulumu
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Versiyon kontrolü
composer --version
```

### Node.js & NPM

**Node.js 18.x LTS** (Önerilir) veya **Node.js 20.x**

```bash
# NodeSource repository
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -

# Node.js kurulumu
sudo apt install nodejs

# Versiyon kontrolü
node --version
npm --version
```

### Supervisor

**Supervisor 4.x** (Queue worker yönetimi için)

```bash
# Ubuntu/Debian
sudo apt install supervisor

# Supervisor başlat
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

## Opsiyonel Bileşenler

### Memcached (Alternatif Cache)

```bash
sudo apt install memcached php8.2-memcached
```

### Elasticsearch (Gelişmiş Arama)

```bash
# Elasticsearch 8.x
wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
echo "deb https://artifacts.elastic.co/packages/8.x/apt stable main" | sudo tee /etc/apt/sources.list.d/elastic-8.x.list
sudo apt update
sudo apt install elasticsearch
```

### Varnish (HTTP Cache)

```bash
sudo apt install varnish
```

## Güvenlik Gereksinimleri

### Firewall (UFW)

```bash
# UFW kurulumu
sudo apt install ufw

# Temel kurallar
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow http
sudo ufw allow https
sudo ufw enable
```

### Fail2Ban (Brute Force Protection)

```bash
# Fail2Ban kurulumu
sudo apt install fail2ban

# Konfigürasyon
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### SSL/TLS Sertifikası

**Let's Encrypt (Ücretsiz)**

```bash
# Certbot kurulumu
sudo apt install certbot python3-certbot-apache

# Sertifika al
sudo certbot --apache -d takimsistemi.com -d *.takimsistemi.com

# Auto-renewal
sudo certbot renew --dry-run
```

## Monitoring & Logging

### Log Rotation

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
    postrotate
        /usr/bin/systemctl reload php8.2-fpm > /dev/null 2>&1 || true
    endscript
}
```

### System Monitoring

**Önerilen Araçlar:**
- **htop**: CPU & Memory monitoring
- **iotop**: Disk I/O monitoring
- **nethogs**: Network monitoring
- **glances**: All-in-one monitoring

```bash
sudo apt install htop iotop nethogs glances
```

## Backup Stratejisi

### Database Backup

```bash
# Günlük backup (Crontab)
0 2 * * * /usr/bin/mysqldump -u root -p'password' pubg_community | gzip > /backup/db/pubg_community_$(date +\%Y\%m\%d).sql.gz

# Haftalık full backup
0 3 * * 0 /usr/bin/mysqldump -u root -p'password' --all-databases | gzip > /backup/db/full_backup_$(date +\%Y\%m\%d).sql.gz
```

### File Backup

```bash
# Günlük file backup
0 4 * * * tar -czf /backup/files/takimsistemi_$(date +\%Y\%m\%d).tar.gz /var/www/takimsistemi --exclude='node_modules' --exclude='vendor'
```

### Redis Backup

```bash
# Redis RDB backup
0 5 * * * cp /var/lib/redis/dump.rdb /backup/redis/dump_$(date +\%Y\%m\%d).rdb
```

## Performance Optimization

### PHP-FPM Pool Configuration

```ini
; /etc/php/8.2/fpm/pool.d/www.conf

[www]
user = www-data
group = www-data
listen = /run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data

; Process Manager
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500

; Performance
pm.process_idle_timeout = 10s
request_terminate_timeout = 300

; Status
pm.status_path = /status
ping.path = /ping
```

### MySQL Query Cache (MySQL 5.7)

```sql
-- Query cache ayarları
SET GLOBAL query_cache_size = 134217728;
SET GLOBAL query_cache_type = 1;
SET GLOBAL query_cache_limit = 2097152;
```

### Redis Memory Optimization

```conf
# Memory optimization
maxmemory-policy allkeys-lru
maxmemory-samples 5

# Compression
rdbcompression yes
```

## CDN & Asset Optimization

### Cloudflare (Önerilir)

- DNS yönetimi
- DDoS protection
- SSL/TLS
- CDN
- Page Rules
- Firewall Rules

### Asset Optimization

```bash
# Image optimization
sudo apt install jpegoptim optipng pngquant gifsicle

# CSS/JS minification (Laravel Mix/Vite)
npm run build
```

## Cron Jobs

```bash
# Laravel Scheduler
* * * * * cd /var/www/takimsistemi && php artisan schedule:run >> /dev/null 2>&1

# Database backup
0 2 * * * cd /var/www/takimsistemi && php artisan backup:run --only-db

# Cache cleanup
0 3 * * * cd /var/www/takimsistemi && php artisan cache:clear

# Log cleanup
0 4 * * * find /var/www/takimsistemi/storage/logs -name "*.log" -mtime +30 -delete
```

## Health Check Endpoints

```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'services' => [
            'database' => DB::connection()->getPdo() ? 'ok' : 'error',
            'redis' => Redis::ping() ? 'ok' : 'error',
            'cache' => Cache::has('health_check') ? 'ok' : 'error',
        ],
    ]);
});
```

## Deployment Checklist

- [ ] PHP 8.2+ kuruldu
- [ ] MySQL 8.0+ kuruldu
- [ ] Redis 7.0+ kuruldu
- [ ] Composer 2.x kuruldu
- [ ] Node.js 18.x kuruldu
- [ ] Supervisor kuruldu
- [ ] Web server (Apache/Nginx) konfigüre edildi
- [ ] SSL sertifikası kuruldu
- [ ] Firewall (UFW) aktif
- [ ] Fail2Ban aktif
- [ ] Log rotation ayarlandı
- [ ] Backup stratejisi oluşturuldu
- [ ] Cron jobs ayarlandı
- [ ] Monitoring kuruldu
- [ ] Health check endpoint'leri test edildi

## Tavsiye Edilen Hosting Sağlayıcıları

### Türkiye
- **DigitalOcean** (Droplet - Frankfurt datacenter)
- **Linode** (Frankfurt datacenter)
- **Vultr** (Frankfurt datacenter)
- **Hetzner** (Almanya - Uygun fiyat)
- **AWS** (eu-central-1 - Frankfurt)

### Önerilen Paketler
- **Başlangıç**: DigitalOcean Droplet 4GB RAM ($24/ay)
- **Orta**: DigitalOcean Droplet 8GB RAM ($48/ay)
- **Büyük**: AWS EC2 t3.xlarge veya dedicated server

## Support & Documentation

- Laravel: https://laravel.com/docs
- PHP: https://www.php.net/docs.php
- MySQL: https://dev.mysql.com/doc/
- Redis: https://redis.io/documentation
- Ubuntu: https://help.ubuntu.com/

---

**Son Güncelleme:** 2025-12-03
**Versiyon:** 1.0.0
