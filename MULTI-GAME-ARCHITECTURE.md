# 🏗️ Multi-Game Platform - Mimari Dokümantasyonu

## İçindekiler

1. [Genel Bakış](#genel-bakış)
2. [Mimari Kararlar](#mimari-kararlar)
3. [Sistem Bileşenleri](#sistem-bileşenleri)
4. [Veri Akışı](#veri-akışı)
5. [Güvenlik](#güvenlik)
6. [Performans](#performans)
7. [Ölçeklenebilirlik](#ölçeklenebilirlik)

---

## Genel Bakış

Takım Sistemi, **hibrit multi-tenant mimari** kullanarak çoklu oyun desteği sağlar. Bu yaklaşım:

- ✅ Tek Laravel uygulaması
- ✅ Tek veritabanı
- ✅ `game_id` ile veri izolasyonu
- ✅ Subdomain routing ile oyun bağlamı
- ✅ Global scope'lar ile otomatik filtreleme

### Neden Bu Mimari?

| Özellik | Avantaj |
|---------|---------|
| **Tek Uygulama** | Kolay bakım, tek deployment |
| **Tek Veritabanı** | Basit yedekleme, cross-game sorgular |
| **Subdomain Routing** | Oyun izolasyonu, SEO dostu |
| **Global Scopes** | Otomatik filtreleme, güvenli |

---

## Mimari Kararlar

### 1. Multi-Tenant Stratejisi

**Seçilen:** Shared Database, Shared Schema (game_id ile ayrım)

**Alternatifler:**
- ❌ **Ayrı Veritabanları:** Yönetim karmaşık, cross-game özellikler zor
- ❌ **Ayrı Uygulamalar:** Kod tekrarı, deployment karmaşık
- ✅ **Shared Database + game_id:** Basit, ölçeklenebilir, cross-game destekli

### 2. Oyun Bağlamı Yönetimi

**Seçilen:** Subdomain + Session

```
pubg.takimsistemi.com → game_id = 1
cod.takimsistemi.com  → game_id = 2
```

**Neden Session?**
- ✅ Sunucu tarafında güvenli
- ✅ Wildcard domain ile paylaşılabilir
- ✅ Middleware ile kolay yönetim

### 3. Veri Filtreleme

**Seçilen:** Laravel Global Scopes

```php
// Otomatik filtreleme
Tournament::all(); // Sadece mevcut oyunun turnuvaları

// Manuel filtreleme
Tournament::withoutGlobalScope(GameScope::class)->get(); // Tüm oyunlar
```

**Avantajlar:**
- ✅ Otomatik, geliştiriciler unutamaz
- ✅ Güvenli, veri sızıntısı riski düşük
- ✅ Gerektiğinde devre dışı bırakılabilir

---

## Sistem Bileşenleri

### 1. DetectGame Middleware

**Sorumluluk:** Her istekte subdomain'den oyunu algılar.

```php
// app/Http/Middleware/DetectGame.php

public function handle(Request $request, Closure $next)
{
    // 1. Subdomain'i al
    $host = $request->getHost();
    $subdomain = explode('.', $host)[0];
    
    // 2. Oyunu bul
    $game = Game::active()->bySlug($subdomain)->first();
    
    // 3. Session'a kaydet
    session(['game_id' => $game->id, 'game' => $game]);
    
    // 4. View'a paylaş
    view()->share('currentGame', $game);
    
    return $next($request);
}
```

**Çalışma Prensibi:**
```
Request → DetectGame → Session → Global Scope → Controller → Response
```

### 2. GameScope (Global Scope)

**Sorumluluk:** Tüm query'leri otomatik filtreler.

```php
// app/Models/Scopes/GameScope.php

public function apply(Builder $builder, Model $model)
{
    $gameId = session('game_id');
    
    if ($gameId) {
        $builder->where($model->getTable() . '.game_id', $gameId);
    }
}
```

**Kullanım:**

```php
// Model'de
protected static function booted()
{
    static::addGlobalScope(new GameScope());
}

// Otomatik filtreleme
$tournaments = Tournament::all(); // WHERE game_id = 1

// Scope'u devre dışı bırak
$allTournaments = Tournament::withoutGlobalScope(GameScope::class)->get();
```

### 3. GameService

**Sorumluluk:** Oyun yönetimi iş mantığı.

```php
// app/Services/GameService.php

class GameService
{
    // Mevcut oyunu al
    public function getCurrentGame(): ?Game
    {
        return Cache::remember(
            "game." . session('game_id'),
            3600,
            fn() => Game::find(session('game_id'))
        );
    }
    
    // Oyun değiştir
    public function switchGame(string $slug): string
    {
        $game = Game::active()->bySlug($slug)->firstOrFail();
        session(['game_id' => $game->id]);
        return $this->getGameUrl($game);
    }
    
    // Platform istatistikleri
    public function getPlatformStats(): array
    {
        return [
            'total_users' => User::count(),
            'games' => Game::active()->withCount(['tournaments', 'clans'])->get(),
        ];
    }
}
```

### 4. Game Model

**Sorumluluk:** Oyun bilgilerini ve ayarlarını yönetir.

```php
// app/Models/Game.php

class Game extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'description', 'status', 'settings'];
    protected $casts = ['settings' => 'array'];
    
    // İlişkiler
    public function tournaments() { return $this->hasMany(Tournament::class); }
    public function clans() { return $this->hasMany(Clan::class); }
    
    // Scope'lar
    public function scopeActive($query) { return $query->where('status', 'active'); }
    
    // Helper metodlar
    public function getThemeColor(): string {
        return $this->settings['theme_color'] ?? '#FF6B00';
    }
}
```

**settings JSON yapısı:**
```json
{
    "theme_color": "#FF6B00",
    "secondary_color": "#FFB800",
    "max_team_size": 4,
    "platforms": ["Android", "iOS"],
    "features": {
        "tournaments": true,
        "clans": true,
        "lfg": true
    }
}
```

---

## Veri Akışı

### Senaryo 1: Kullanıcı PUBG Turnuvalarını Görüntüler

```
1. User → pubg.takimsistemi.com/tournaments
   ↓
2. DetectGame Middleware
   - Subdomain: "pubg"
   - Game::bySlug('pubg')->first() → Game(id=1)
   - session(['game_id' => 1])
   ↓
3. TournamentController@index
   - Tournament::all()
   ↓
4. GameScope (Global Scope)
   - WHERE game_id = 1
   ↓
5. Response
   - Sadece PUBG turnuvaları
```

### Senaryo 2: Kullanıcı Oyun Değiştirir

```
1. User → Game Switcher'da "COD Mobile" seçer
   ↓
2. GameService->switchGame('cod')
   - Game::bySlug('cod')->first() → Game(id=2)
   - session(['game_id' => 2])
   - return "https://cod.takimsistemi.com"
   ↓
3. Redirect → cod.takimsistemi.com
   ↓
4. DetectGame Middleware
   - session(['game_id' => 2]) (zaten var)
   ↓
5. Artık tüm query'ler game_id=2 ile filtrelenir
```

### Senaryo 3: Cross-Game Mesajlaşma

```
1. User (PUBG'de) → Mesaj gönder
   ↓
2. MessageController@store
   - Message::create([...]) // game_id YOK
   ↓
3. Alıcı (COD'da) → Mesajları görüntüle
   ↓
4. MessageController@index
   - Message::where('receiver_id', auth()->id())->get()
   - GameScope UYGULANMAZ (Message'da yok)
   ↓
5. Response
   - Tüm oyunlardan mesajlar
```

---

## Güvenlik

### 1. Veri İzolasyonu

**Global Scope ile Otomatik Koruma:**

```php
// ✅ Güvenli - Otomatik filtreleme
$tournaments = Tournament::all(); // WHERE game_id = session('game_id')

// ⚠️ Dikkat - Manuel kontrol gerekli
$tournament = Tournament::withoutGlobalScope(GameScope::class)->find($id);
if ($tournament->game_id !== session('game_id')) {
    abort(403);
}
```

### 2. Authorization Policies

```php
// app/Policies/TournamentPolicy.php

public function view(User $user, Tournament $tournament)
{
    // Oyun bağlamı kontrolü
    if ($tournament->game_id !== session('game_id')) {
        return false;
    }
    
    return true;
}
```

### 3. Cross-Game Access Prevention

```php
// Middleware veya Controller
if ($entity->game_id !== session('game_id')) {
    Log::warning('Cross-game access attempt', [
        'user_id' => auth()->id(),
        'entity_game_id' => $entity->game_id,
        'session_game_id' => session('game_id'),
    ]);
    
    abort(403, 'Bu içeriğe erişim yetkiniz yok.');
}
```

### 4. CSRF Protection

```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 
    '*.takimsistemi.com,takimsistemi.com'
)),

// config/session.php
'domain' => env('SESSION_DOMAIN', '.takimsistemi.com'),
```

---

## Performans

### 1. Caching Stratejisi

```php
// Game bilgilerini cache'le (1 saat)
$game = Cache::remember("game.{$gameId}", 3600, function () use ($gameId) {
    return Game::find($gameId);
});

// Aktif oyunları cache'le (1 saat)
$activeGames = Cache::remember('games.active', 3600, function () {
    return Game::active()->get();
});

// Cache invalidation
Cache::forget("game.{$gameId}");
Cache::tags(['games'])->flush();
```

### 2. Database Indexing

```sql
-- Kritik index'ler
CREATE INDEX idx_tournaments_game_status ON tournaments(game_id, status);
CREATE INDEX idx_clans_game_verified ON clans(game_id, is_verified);
CREATE INDEX idx_lfg_posts_game_status ON lfg_posts(game_id, status);
CREATE INDEX idx_games_slug ON games(slug);
```

### 3. Eager Loading

```php
// ❌ N+1 Problem
$tournaments = Tournament::all();
foreach ($tournaments as $tournament) {
    echo $tournament->game->name; // Her seferinde query
}

// ✅ Eager Loading
$tournaments = Tournament::with('game')->all();
foreach ($tournaments as $tournament) {
    echo $tournament->game->name; // Tek query
}
```

### 4. Query Optimization

```php
// Sadece gerekli kolonları seç
$games = Game::select('id', 'name', 'slug', 'logo')->active()->get();

// Chunk ile büyük veri setleri
Tournament::chunk(100, function ($tournaments) {
    // Process tournaments
});
```

---

## Ölçeklenebilirlik

### 1. Horizontal Scaling

**Load Balancer:**
```
                    ┌─────────────┐
                    │Load Balancer│
                    └─────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        ▼                  ▼                  ▼
   ┌─────────┐       ┌─────────┐       ┌─────────┐
   │ App 1   │       │ App 2   │       │ App 3   │
   └─────────┘       └─────────┘       └─────────┘
        │                  │                  │
        └──────────────────┼──────────────────┘
                           ▼
                    ┌─────────────┐
                    │   MySQL     │
                    └─────────────┘
```

**Session Paylaşımı:**
```php
// config/session.php
'driver' => 'redis', // Tüm sunucularda aynı session
```

### 2. Database Scaling

**Read Replicas:**
```php
// config/database.php
'mysql' => [
    'read' => [
        'host' => ['192.168.1.2', '192.168.1.3'],
    ],
    'write' => [
        'host' => ['192.168.1.1'],
    ],
],
```

**Sharding (Gelecek):**
```
Game 1-10  → Database 1
Game 11-20 → Database 2
Game 21-30 → Database 3
```

### 3. Cache Scaling

**Redis Cluster:**
```php
// config/database.php
'redis' => [
    'client' => 'phpredis',
    'cluster' => true,
    'clusters' => [
        'default' => [
            ['host' => '127.0.0.1', 'port' => 6379],
            ['host' => '127.0.0.1', 'port' => 6380],
        ],
    ],
],
```

### 4. CDN Integration

```php
// Statik asset'ler için CDN
<img src="{{ cdn_url($game->logo) }}" alt="{{ $game->name }}">

// Helper function
function cdn_url($path) {
    return env('CDN_URL', '') . $path;
}
```

---

## Deployment Stratejisi

### 1. Zero-Downtime Deployment

```bash
# 1. Yeni kod deploy et (eski kod çalışmaya devam eder)
git pull origin main
composer install --no-dev --optimize-autoloader

# 2. Migration'ları çalıştır (backward compatible olmalı)
php artisan migrate --force

# 3. Cache'leri temizle
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Queue worker'ları yeniden başlat
php artisan queue:restart

# 5. Symlink'i güncelle (atomic operation)
ln -sfn /var/www/new-release /var/www/current
```

### 2. Rollback Planı

```bash
# 1. Eski release'e dön
ln -sfn /var/www/old-release /var/www/current

# 2. Migration'ları geri al
php artisan migrate:rollback --step=1

# 3. Cache'leri temizle
php artisan cache:clear
```

---

## Monitoring & Logging

### 1. Application Monitoring

```php
// Log game context
Log::info('Tournament created', [
    'game_id' => session('game_id'),
    'game_slug' => session('game')->slug,
    'tournament_id' => $tournament->id,
]);

// Monitor slow queries
DB::listen(function ($query) {
    if ($query->time > 1000) { // 1 saniyeden uzun
        Log::warning('Slow query detected', [
            'sql' => $query->sql,
            'time' => $query->time,
            'game_id' => session('game_id'),
        ]);
    }
});
```

### 2. Error Tracking

```php
// Sentry, Bugsnag, vb.
if (app()->bound('sentry')) {
    app('sentry')->configureScope(function ($scope) {
        $scope->setContext('game', [
            'id' => session('game_id'),
            'slug' => session('game')->slug ?? null,
        ]);
    });
}
```

---

## Best Practices

### 1. Yeni Model Ekleme

```php
// 1. Migration'da game_id ekle
Schema::create('new_table', function (Blueprint $table) {
    $table->id();
    $table->foreignId('game_id')->constrained()->onDelete('cascade');
    // ...
});

// 2. Model'de GameScope ekle
class NewModel extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new GameScope());
        
        static::creating(function ($model) {
            if (!$model->game_id && session('game_id')) {
                $model->game_id = session('game_id');
            }
        });
    }
    
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
```

### 2. Cross-Game Feature Ekleme

```php
// game_id EKLEME - Cross-game olmalı
Schema::create('messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sender_id')->constrained('users');
    $table->foreignId('receiver_id')->constrained('users');
    // game_id YOK
});

// Model'de GameScope EKLEME
class Message extends Model
{
    // Global scope yok
    // Tüm oyunlarda çalışır
}
```

### 3. Testing

```php
// Test'te game context ayarla
public function test_tournament_creation()
{
    $game = Game::factory()->create();
    $this->setGameContext($game);
    
    $tournament = Tournament::factory()->create();
    
    $this->assertEquals($game->id, $tournament->game_id);
}

// Helper method (TestCase.php)
protected function setGameContext(Game $game): void
{
    session(['game_id' => $game->id, 'game' => $game]);
}
```

---

## Sık Sorulan Sorular

### Q: Yeni oyun nasıl eklenir?

```bash
# 1. Veritabanına ekle
Game::create([
    'name' => 'Yeni Oyun',
    'slug' => 'yeni-oyun',
    'status' => 'active',
    'settings' => [...]
]);

# 2. Tema dosyası oluştur
# resources/css/themes/yeni-oyun.css

# 3. Subdomain yapılandır
# DNS: yeni-oyun.takimsistemi.com
```

### Q: Oyunlar arası veri paylaşımı nasıl yapılır?

```php
// Global scope'u devre dışı bırak
$allTournaments = Tournament::withoutGlobalScope(GameScope::class)->get();

// Veya cross-game model kullan (User, Message, Friendship)
$messages = Message::where('receiver_id', auth()->id())->get();
```

### Q: Performance sorunları nasıl çözülür?

1. **Cache kullan:** Game bilgileri, aktif oyunlar
2. **Eager loading:** İlişkileri önceden yükle
3. **Index'ler:** game_id kolonlarına index ekle
4. **Redis:** Session ve cache için Redis kullan

---

## Sonuç

Bu mimari, **basitlik**, **güvenlik** ve **ölçeklenebilirlik** arasında optimal dengeyi sağlar. Tek uygulama ve veritabanı ile yönetim kolaylığı sunarken, subdomain routing ve global scope'lar ile güvenli veri izolasyonu garanti eder.

**Avantajlar:**
- ✅ Kolay yönetim ve bakım
- ✅ Cross-game özellikler
- ✅ Güvenli veri izolasyonu
- ✅ Ölçeklenebilir yapı
- ✅ SEO dostu URL'ler

**Dezavantajlar:**
- ⚠️ Tek veritabanı (sharding gerekebilir)
- ⚠️ Global scope'ları unutma riski (test ile minimize edilir)

---

**Versiyon:** 2.0.0  
**Son Güncelleme:** 2025-12-03  
**Yazar:** Takım Sistemi Development Team
