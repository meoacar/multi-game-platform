# 🔴 Redis Konfigürasyon Rehberi

## Redis Nedir?

Redis (Remote Dictionary Server), açık kaynaklı, in-memory veri yapısı deposudur. Laravel'de cache, session, queue ve broadcasting için kullanılır.

## Neden Redis?

### Avantajları
- ⚡ **Çok Hızlı**: In-memory olduğu için milisaniye altı response time
- 🔄 **Persistence**: Disk'e yazma seçeneği ile veri kaybı önlenir
- 📊 **Veri Yapıları**: String, Hash, List, Set, Sorted Set desteği
- 🌐 **Pub/Sub**: Real-time messaging desteği
- 💾 **Memory Efficient**: Optimize edilmiş memory kullanımı
- 🔒 **Atomic Operations**: Thread-safe işlemler

### Multi-Game Platform İçin Faydaları
- **Session Persistence**: Subdomain'ler arası session paylaşımı
- **Game Context Caching**: Oyun bilgilerinin hızlı erişimi
- **Query Result Caching**: Veritabanı yükünü azaltır
- **Queue Management**: Background job'ların yönetimi
- **Rate Limiting**: API ve web isteklerinin sınırlandırılması

## Redis Kurulumu

### Ubuntu/Debian

```bash
# Redis kurulumu
sudo apt update
sudo apt install redis-server

# Redis başlat
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Status kontrol
sudo systemctl status redis-server

# Redis CLI test
redis-cli ping
# Beklenen: PONG
```

### CentOS/RHEL

```bash
# EPEL repository ekle
sudo yum install epel-release

# Redis kurulumu
sudo yum install redis

# Redis başlat
sudo systemctl start redis
sudo systemctl enable redis

# Status kontrol
sudo systemctl status redis
```

### Windows

```bash
# WSL2 kullanarak
wsl --install
wsl

# Ubuntu içinde Redis kur
sudo apt update
sudo apt install redis-server
sudo service redis-server start
```

### Docker

```bash
# Redis container çalıştır
docker run -d \
  --name redis \
  -p 6379:6379 \
  -v redis-data:/data \
  redis:7-alpine \
  redis-server --appendonly yes --requirepass your_password

# Container status
docker ps | grep redis

# Redis CLI
docker exec -it redis redis-cli -a your_password
```

## Redis Konfigürasyonu

### 1. Redis Server Konfigürasyonu

```bash
# Konfigürasyon dosyasını düzenle
sudo nano /etc/redis/redis.conf
```

#### Production İçin Önerilen Ayarlar

```conf
# ============================================
# NETWORK
# ============================================
bind 127.0.0.1 ::1
protected-mode yes
port 6379
tcp-backlog 511
timeout 0
tcp-keepalive 300

# ============================================
# SECURITY
# ============================================
requirepass your_strong_password_here

# Tehlikeli komutları devre dışı bırak
rename-command FLUSHDB ""
rename-command FLUSHALL ""
rename-command CONFIG ""

# ============================================
# MEMORY MANAGEMENT
# ============================================
maxmemory 2gb
maxmemory-policy allkeys-lru

# ============================================
# PERSISTENCE
# ============================================
# RDB (Snapshot)
save 900 1
save 300 10
save 60 10000

stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir /var/lib/redis

# AOF (Append Only File)
appendonly yes
appendfilename "appendonly.aof"
appendfsync everysec
no-appendfsync-on-rewrite no
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb

# ============================================
# LOGGING
# ============================================
loglevel notice
logfile /var/log/redis/redis-server.log

# ============================================
# DATABASES
# ============================================
databases 16

# ============================================
# PERFORMANCE
# ============================================
# Slow log
slowlog-log-slower-than 10000
slowlog-max-len 128

# Latency monitoring
latency-monitor-threshold 100
```

### 2. Laravel Redis Konfigürasyonu

#### config/database.php

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),

    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],

    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
        'read_timeout' => 60,
        'context' => [
            'stream' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ],
    ],

    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],

    'queue' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_QUEUE_DB', '2'),
    ],

    'session' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_SESSION_DB', '3'),
    ],
],
```

#### .env Ayarları

```env
# Redis Connection
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_strong_password
REDIS_PORT=6379

# Redis Databases (Ayrı DB'ler kullan)
REDIS_DB=0              # Default
REDIS_CACHE_DB=1        # Cache
REDIS_QUEUE_DB=2        # Queue
REDIS_SESSION_DB=3      # Session

# Cache Driver
CACHE_DRIVER=redis
CACHE_PREFIX=takimsistemi_cache

# Session Driver
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Queue Driver
QUEUE_CONNECTION=redis

# Broadcast Driver
BROADCAST_CONNECTION=redis
```

### 3. PHP Redis Extension

#### phpredis Extension (Önerilir)

```bash
# Ubuntu/Debian
sudo apt install php-redis

# CentOS/RHEL
sudo yum install php-redis

# PECL ile
sudo pecl install redis

# php.ini'ye ekle
extension=redis.so

# PHP restart
sudo systemctl restart php8.2-fpm  # veya apache2
```

#### Predis (Alternatif - Pure PHP)

```bash
# Composer ile
composer require predis/predis

# .env
REDIS_CLIENT=predis
```

## Database Ayrımı Stratejisi

### Neden Ayrı Database'ler?

- **İzolasyon**: Her servis kendi namespace'inde çalışır
- **Temizlik**: Bir servisi temizlemek diğerlerini etkilemez
- **Monitoring**: Her servisin memory kullanımı ayrı izlenebilir
- **Performance**: Conflict riski azalır

### Database Kullanımı

```
DB 0: Default (Genel kullanım)
DB 1: Cache (Game info, query results)
DB 2: Queue (Background jobs)
DB 3: Session (User sessions)
DB 4-15: Reserved (Gelecek kullanım)
```

### Database Seçimi

```bash
# Redis CLI
redis-cli -a your_password

# Database seç
SELECT 1

# Mevcut database'i gör
CLIENT GETNAME

# Database'deki key sayısı
DBSIZE

# Tüm key'leri listele
KEYS *

# Database'i temizle
FLUSHDB
```

## Cache Kullanımı

### Game Information Caching

```php
use Illuminate\Support\Facades\Cache;

// Game bilgisini cache'le
$game = Cache::remember("game.{$gameId}", 3600, function () use ($gameId) {
    return Game::find($gameId);
});

// Active games cache
$activeGames = Cache::remember('games.active', 3600, function () {
    return Game::active()->get();
});

// Cache invalidation
Cache::forget("game.{$gameId}");
Cache::forget('games.active');

// Tag-based caching
Cache::tags(['games'])->put("game.{$gameId}", $game, 3600);
Cache::tags(['games'])->flush(); // Tüm game cache'lerini temizle
```

### Query Result Caching

```php
// Sık kullanılan sorguları cache'le
$tournaments = Cache::remember(
    "tournaments.game.{$gameId}.featured",
    600,
    function () use ($gameId) {
        return Tournament::where('game_id', $gameId)
            ->where('is_featured', true)
            ->with('organizer')
            ->get();
    }
);
```

## Session Kullanımı

### Cross-Subdomain Session

```php
// config/session.php
'driver' => env('SESSION_DRIVER', 'redis'),
'connection' => 'session',
'lifetime' => 120,
'expire_on_close' => false,
'encrypt' => true,
'domain' => env('SESSION_DOMAIN', '.takimsistemi.com'),
'secure' => env('SESSION_SECURE_COOKIE', true),
'same_site' => 'lax',
```

### Session Kullanımı

```php
// Game context session'a kaydet
session([
    'game_id' => $game->id,
    'game' => $game,
]);

// Session'dan oku
$gameId = session('game_id');
$game = session('game');

// Session temizle
session()->forget('game_id');
session()->flush();
```

## Queue Kullanımı

### Queue Configuration

```php
// config/queue.php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'queue',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
        'after_commit' => false,
    ],
],
```

### Queue Worker

```bash
# Worker başlat
php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

# Specific queue
php artisan queue:work redis --queue=high,default,low

# Queue status
php artisan queue:monitor

# Failed jobs
php artisan queue:failed
php artisan queue:retry all
```

## Monitoring & Maintenance

### Redis Monitoring

```bash
# Redis CLI
redis-cli -a your_password

# Info
INFO
INFO memory
INFO stats
INFO replication

# Memory kullanımı
MEMORY USAGE key_name

# Slow log
SLOWLOG GET 10

# Client list
CLIENT LIST

# Monitor (real-time)
MONITOR
```

### Memory Monitoring

```bash
# Memory stats
redis-cli -a your_password INFO memory | grep used_memory_human

# Key sayısı
redis-cli -a your_password DBSIZE

# Biggest keys
redis-cli -a your_password --bigkeys

# Memory usage by key pattern
redis-cli -a your_password --memkeys
```

### Performance Tuning

```bash
# Latency monitoring
redis-cli -a your_password --latency
redis-cli -a your_password --latency-history

# Intrinsic latency
redis-cli -a your_password --intrinsic-latency 100
```

## Backup & Recovery

### RDB Backup

```bash
# Manual backup
redis-cli -a your_password BGSAVE

# Backup dosyası
/var/lib/redis/dump.rdb

# Backup kopyala
sudo cp /var/lib/redis/dump.rdb /backup/redis/dump-$(date +%Y%m%d).rdb
```

### AOF Backup

```bash
# AOF dosyası
/var/lib/redis/appendonly.aof

# AOF rewrite
redis-cli -a your_password BGREWRITEAOF
```

### Restore

```bash
# Redis durdur
sudo systemctl stop redis-server

# Backup'ı restore et
sudo cp /backup/redis/dump-20251203.rdb /var/lib/redis/dump.rdb
sudo chown redis:redis /var/lib/redis/dump.rdb

# Redis başlat
sudo systemctl start redis-server
```

## Troubleshooting

### Redis Bağlantı Sorunları

```bash
# Redis çalışıyor mu?
sudo systemctl status redis-server

# Port dinliyor mu?
sudo netstat -tlnp | grep 6379

# Firewall kontrolü
sudo ufw status
sudo ufw allow 6379/tcp

# Connection test
redis-cli -h 127.0.0.1 -p 6379 -a your_password ping
```

### Memory Sorunları

```bash
# Memory kullanımı
redis-cli -a your_password INFO memory

# Maxmemory ayarı
redis-cli -a your_password CONFIG GET maxmemory

# Eviction policy
redis-cli -a your_password CONFIG GET maxmemory-policy

# Memory temizle
redis-cli -a your_password FLUSHDB  # Dikkatli kullan!
```

### Performance Sorunları

```bash
# Slow queries
redis-cli -a your_password SLOWLOG GET 10

# Latency
redis-cli -a your_password --latency

# Client connections
redis-cli -a your_password CLIENT LIST
```

## Security Best Practices

### 1. Şifre Koruması

```conf
# redis.conf
requirepass your_very_strong_password_here
```

### 2. Network Binding

```conf
# Sadece localhost
bind 127.0.0.1 ::1

# Specific IP
bind 127.0.0.1 192.168.1.100
```

### 3. Tehlikeli Komutları Devre Dışı Bırak

```conf
rename-command FLUSHDB ""
rename-command FLUSHALL ""
rename-command CONFIG ""
rename-command SHUTDOWN ""
rename-command DEBUG ""
```

### 4. Firewall

```bash
# UFW
sudo ufw deny 6379/tcp
sudo ufw allow from 127.0.0.1 to any port 6379

# iptables
sudo iptables -A INPUT -p tcp --dport 6379 -s 127.0.0.1 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 6379 -j DROP
```

## Laravel Artisan Commands

```bash
# Cache temizle
php artisan cache:clear

# Config cache
php artisan config:cache

# Route cache
php artisan route:cache

# View cache
php artisan view:cache

# Queue restart
php artisan queue:restart

# Redis flush (dikkatli!)
php artisan cache:clear --store=redis
```

## Useful Redis Commands

```bash
# Key operations
SET key value
GET key
DEL key
EXISTS key
EXPIRE key seconds
TTL key

# String operations
INCR counter
DECR counter
APPEND key value

# Hash operations
HSET hash field value
HGET hash field
HGETALL hash
HDEL hash field

# List operations
LPUSH list value
RPUSH list value
LPOP list
RPOP list
LRANGE list 0 -1

# Set operations
SADD set member
SMEMBERS set
SISMEMBER set member

# Sorted Set operations
ZADD sortedset score member
ZRANGE sortedset 0 -1
ZRANK sortedset member
```

## Production Checklist

- [ ] Redis kuruldu ve çalışıyor
- [ ] Şifre ayarlandı
- [ ] Memory limit ayarlandı
- [ ] Persistence (RDB + AOF) aktif
- [ ] Slow log aktif
- [ ] Monitoring kuruldu
- [ ] Backup stratejisi oluşturuldu
- [ ] Firewall kuralları ayarlandı
- [ ] Laravel .env ayarları yapıldı
- [ ] Queue worker çalışıyor
- [ ] Session test edildi
- [ ] Cache test edildi

---

**Son Güncelleme:** 2025-12-03
**Versiyon:** 1.0.0
