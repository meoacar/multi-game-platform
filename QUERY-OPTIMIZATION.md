# Query Optimizasyonu Dokümantasyonu

Bu dokümant, admin panel için yapılan query optimizasyonlarını açıklar.

## Yapılan Optimizasyonlar

### 1. Database Index'leri

**Migration Dosyası:** `2025_11_29_000001_add_performance_indexes.php`

Sık kullanılan sorgular için index'ler eklendi:

#### Users Tablosu
- `status` - Durum filtreleme için
- `is_admin` - Admin filtreleme için
- `email_verified_at` - Email doğrulama filtreleme için
- `last_login_at` - Son giriş sıralama için
- `xp_total` - XP sıralama için
- `(status, created_at)` - Composite index: durum + tarih
- `(is_admin, status)` - Composite index: admin + durum
- `created_at` - Tarih filtreleme için
- `updated_at` - Aktivite filtreleme için

#### Profiles Tablosu
- `user_id` - Foreign key için
- `city` - Şehir filtreleme için
- `game_id` - Oyun filtreleme için
- `pubg_id` - PUBG ID arama için
- `(user_id, game_id)` - Composite index

#### LFG Posts Tablosu
- `user_id`, `game_id`, `status`, `is_featured`, `city`, `created_at`, `views_count`
- Composite index'ler: `(status, created_at)`, `(game_id, status)`

#### Clans Tablosu
- `leader_id`, `game_id`, `is_verified`, `city`, `created_at`
- Composite index: `(game_id, is_verified)`

#### Guide Posts Tablosu
- `user_id`, `game_id`, `is_published`, `is_featured`, `created_at`, `views_count`
- Composite index'ler: `(is_published, created_at)`, `(game_id, is_published)`

#### Community Posts Tablosu
- `user_id`, `is_featured`, `created_at`

#### Comments Tablosu
- `user_id`, `(commentable_type, commentable_id)`, `created_at`

#### Reports Tablosu
- `reporter_id`, `status`, `priority`, `(reportable_type, reportable_id)`, `created_at`
- Composite index: `(status, priority)`

#### Applications Tablosu
- Clan ve LFG başvuruları için index'ler

#### Admin Tabloları
- Admin Activity Logs, XP Events, Roles, Permissions, System Logs için index'ler

### 2. Eager Loading Optimizasyonları

#### N+1 Problem Çözümleri

**Öncesi:**
```php
$users = User::all();
foreach ($users as $user) {
    echo $user->profile->pubg_id; // Her user için ayrı query
}
```

**Sonrası:**
```php
$users = User::with('profile:id,user_id,pubg_id')->get();
foreach ($users as $user) {
    echo $user->profile->pubg_id; // Tek query ile tüm profiller
}
```

#### Optimize Edilen Controller'lar

**DashboardController:**
- `top_users` - Profile ilişkisi eager load edildi
- `recent_users` - Profile ilişkisi eager load edildi
- `recent_reports` - Reporter ve reportable ilişkileri eager load edildi
- `popular_guides` - User ilişkisi eager load edildi
- `popular_clans` - Members count eager load edildi

**ContentController:**
- `getRecentContent()` - Tüm içerik türleri için eager loading
- `getPopularContent()` - User ve game ilişkileri eager load edildi
- `getReportedContent()` - Reporter ve reportable ilişkileri eager load edildi
- `all()` - Tüm içerik listesi için eager loading

**AnalyticsService:**
- `getTopContentCreators()` - withCount kullanılarak optimize edildi
- `getContentQualityScore()` - Limit eklendi ve sadece gerekli kolonlar seçildi

### 3. Select Optimizasyonları

Sadece gerekli kolonları seçerek veri transferini azalttık:

**Öncesi:**
```php
$users = User::with('profile')->get(); // Tüm kolonlar
```

**Sonrası:**
```php
$users = User::with('profile:id,user_id,pubg_id,avatar')
    ->select('id', 'name', 'email', 'xp_total')
    ->get(); // Sadece gerekli kolonlar
```

### 4. Query Optimizasyonları

#### Composite Index Kullanımı
Sık birlikte kullanılan kolonlar için composite index'ler eklendi:
- `(status, created_at)` - Durum ve tarih filtreleme
- `(game_id, status)` - Oyun ve durum filtreleme
- `(is_admin, status)` - Admin ve durum filtreleme

#### Polymorphic İlişkiler
Polymorphic ilişkiler için index'ler eklendi:
- `(commentable_type, commentable_id)` - Comments için
- `(reportable_type, reportable_id)` - Reports için
- `(target_type, target_id)` - Admin Activity Logs için

## Performans İyileştirmeleri

### Beklenen İyileştirmeler

1. **Dashboard Yükleme Süresi:** %40-60 azalma
2. **Kullanıcı Listesi:** %50-70 azalma
3. **İçerik Listesi:** %40-50 azalma
4. **Analitik Sayfaları:** %30-40 azalma

### Ölçüm Metrikleri

Query sayısını ölçmek için Laravel Debugbar kullanılabilir:

```bash
composer require barryvdh/laravel-debugbar --dev
```

## Migration Çalıştırma

Index'leri eklemek için:

```bash
php artisan migrate
```

Index'leri geri almak için:

```bash
php artisan migrate:rollback
```

## Best Practices

### 1. Eager Loading Kullanımı
```php
// ✅ İyi
$users = User::with('profile')->get();

// ❌ Kötü
$users = User::all();
foreach ($users as $user) {
    $user->profile; // N+1 problem
}
```

### 2. Select Kullanımı
```php
// ✅ İyi
$users = User::select('id', 'name', 'email')->get();

// ❌ Kötü
$users = User::all(); // Tüm kolonlar
```

### 3. Pagination Kullanımı
```php
// ✅ İyi
$users = User::paginate(20);

// ❌ Kötü
$users = User::all(); // Tüm kayıtlar
```

### 4. Index Kullanımı
```php
// ✅ İyi - Index kullanır
$users = User::where('status', 'active')->get();

// ❌ Kötü - Index kullanmaz
$users = User::whereRaw('LOWER(status) = ?', ['active'])->get();
```

### 5. Chunk Kullanımı (Büyük Veri Setleri)
```php
// ✅ İyi
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // İşlem
    }
});

// ❌ Kötü
$users = User::all(); // Bellek problemi
```

## Monitoring

### Query Sayısını Kontrol Etme

```php
DB::enableQueryLog();

// Kodunuz

$queries = DB::getQueryLog();
dd(count($queries)); // Query sayısı
```

### Slow Query Tespiti

`config/database.php` dosyasında:

```php
'mysql' => [
    // ...
    'options' => [
        PDO::ATTR_EMULATE_PREPARES => true,
    ],
    'slow_query_log' => true,
    'slow_query_time' => 2, // 2 saniyeden uzun sürenleri logla
],
```

## Gelecek İyileştirmeler

1. **Redis Cache:** Sık kullanılan sorguları cache'le
2. **Database Replication:** Read/Write ayrımı
3. **Query Caching:** MySQL query cache aktif et
4. **Materialized Views:** Karmaşık analitik sorgular için
5. **Elasticsearch:** Full-text search için

## Notlar

- Index'ler write performansını biraz düşürebilir (INSERT/UPDATE/DELETE)
- Çok fazla index performansı olumsuz etkileyebilir
- Kullanılmayan index'leri periyodik olarak temizleyin
- Index'lerin kullanılıp kullanılmadığını EXPLAIN ile kontrol edin

## EXPLAIN Kullanımı

Query'nin index kullanıp kullanmadığını kontrol etmek için:

```sql
EXPLAIN SELECT * FROM users WHERE status = 'active';
```

Laravel'de:

```php
$query = User::where('status', 'active');
dd($query->toSql(), $query->getBindings());
```

## Sonuç

Bu optimizasyonlar ile:
- N+1 problemleri çözüldü
- Database index'leri eklendi
- Sadece gerekli kolonlar seçildi
- Query sayısı azaltıldı
- Performans önemli ölçüde arttı

Tüm değişiklikler geriye dönük uyumludur ve mevcut kodu bozmaz.
