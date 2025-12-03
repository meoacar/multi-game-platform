# 🔄 Multi-Game Platform - Migration Guide

## İçindekiler

1. [Genel Bakış](#genel-bakış)
2. [Ön Hazırlık](#ön-hazırlık)
3. [Adım Adım Migration](#adım-adım-migration)
4. [Kod Değişiklikleri](#kod-değişiklikleri)
5. [Testing](#testing)
6. [Deployment](#deployment)
7. [Rollback](#rollback)
8. [Troubleshooting](#troubleshooting)

---

## Genel Bakış

Bu rehber, mevcut PUBG Community platformunu multi-game platformuna dönüştürmek için gereken tüm adımları içerir.

### Değişiklik Özeti

| Bileşen | Değişiklik | Etki |
|---------|-----------|------|
| **Database** | `games` tablosu eklendi, `game_id` kolonları eklendi | Orta |
| **Models** | Global scope'lar eklendi | Düşük |
| **Routes** | Subdomain routing eklendi | Orta |
| **Middleware** | DetectGame eklendi | Düşük |
| **Services** | GameService, MultiTenantService eklendi | Düşük |
| **Views** | Landing page, game switcher eklendi | Düşük |

### Tahmini Süre

- **Development:** 2-3 hafta
- **Testing:** 1 hafta
- **Deployment:** 2-4 saat (maintenance mode)

---

## Ön Hazırlık

### 1. Veritabanı Yedeği

```bash
# MySQL dump
mysqldump -u root -p pubg_community > backup_$(date +%Y%m%d_%H%M%S).sql

# Laravel backup
php artisan backup:database

# Yedeği doğrula
mysql -u root -p test_db < backup_20251203_120000.sql
```

### 2. Git Branch Oluştur

```bash
git checkout -b feature/multi-game-platform
git push -u origin feature/multi-game-platform
```

### 3. Test Ortamı Hazırla

```bash
# Test veritabanı oluştur
mysql -u root -p
CREATE DATABASE pubg_community_test;

# .env.testing oluştur
cp .env .env.testing
# DB_DATABASE=pubg_community_test olarak değiştir
```

### 4. Dependency Kontrolü

```bash
# Composer bağımlılıkları
composer install

# Node bağımlılıkları
npm install

# Test'leri çalıştır (mevcut durumu doğrula)
php artisan test
```

---

## Adım Adım Migration

### Faz 1: Database Migration (2-3 gün)

#### 1.1. games Tablosu Oluştur

```bash
php artisan make:migration create_games_table
```

```php
// database/migrations/xxxx_create_games_table.php
public function up()
{
    Schema::create('games', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('logo')->nullable();
        $table->text('description')->nullable();
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->json('settings')->nullable();
        $table->timestamps();
        
        $table->index('slug');
        $table->index('status');
    });
}
```

#### 1.2. game_id Kolonları Ekle

```bash
php artisan make:migration add_game_id_to_tables
```

```php
// database/migrations/xxxx_add_game_id_to_tables.php
public function up()
{
    // Oyuna özel tablolar
    $tables = [
        'tournaments',
        'clans',
        'lfg_posts',
        'community_posts',
        'guide_posts',
    ];
    
    foreach ($tables as $table) {
        Schema::table($table, function (Blueprint $table) {
            $table->foreignId('game_id')
                  ->after('id')
                  ->constrained()
                  ->onDelete('cascade');
            
            $table->index('game_id');
        });
    }
    
    // Nullable game_id (cross-game)
    Schema::table('badges', function (Blueprint $table) {
        $table->foreignId('game_id')
              ->nullable()
              ->after('id')
              ->constrained()
              ->onDelete('set null');
    });
    
    Schema::table('notifications', function (Blueprint $table) {
        $table->foreignId('game_id')
              ->nullable()
              ->after('id')
              ->constrained()
              ->onDelete('set null');
    });
}

public function down()
{
    $tables = ['tournaments', 'clans', 'lfg_posts', 'community_posts', 'guide_posts', 'badges', 'notifications'];
    
    foreach ($tables as $table) {
        Schema::table($table, function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropColumn('game_id');
        });
    }
}
```

#### 1.3. GameSeeder Oluştur

```bash
php artisan make:seeder GameSeeder
```

```php
// database/seeders/GameSeeder.php
public function run()
{
    // PUBG Mobile
    $pubg = Game::create([
        'name' => 'PUBG Mobile',
        'slug' => 'pubg',
        'logo' => '/images/games/pubg-logo.png',
        'description' => 'PUBG Mobile topluluk platformu',
        'status' => 'active',
        'settings' => [
            'theme_color' => '#FF6B00',
            'secondary_color' => '#FFB800',
            'max_team_size' => 4,
            'platforms' => ['Android', 'iOS'],
            'features' => [
                'tournaments' => true,
                'clans' => true,
                'lfg' => true,
                'matchmaking' => true,
            ],
        ],
    ]);
    
    // Mevcut verileri PUBG'ye ata
    DB::table('tournaments')->update(['game_id' => $pubg->id]);
    DB::table('clans')->update(['game_id' => $pubg->id]);
    DB::table('lfg_posts')->update(['game_id' => $pubg->id]);
    DB::table('community_posts')->update(['game_id' => $pubg->id]);
    DB::table('guide_posts')->update(['game_id' => $pubg->id]);
    
    $this->command->info('✅ PUBG game created and existing data migrated');
    
    // COD Mobile (opsiyonel)
    Game::create([
        'name' => 'Call of Duty Mobile',
        'slug' => 'cod',
        'logo' => '/images/games/cod-logo.png',
        'description' => 'COD Mobile topluluk platformu',
        'status' => 'active',
        'settings' => [
            'theme_color' => '#FF6B00',
            'secondary_color' => '#000000',
            'max_team_size' => 5,
            'platforms' => ['Android', 'iOS'],
            'features' => [
                'tournaments' => true,
                'clans' => true,
                'lfg' => true,
                'matchmaking' => false,
            ],
        ],
    ]);
    
    $this->command->info('✅ COD Mobile game created');
}
```

#### 1.4. Migration'ları Çalıştır

```bash
# ⚠️ ÖNCE YEDEK AL!
mysqldump -u root -p pubg_community > backup_before_migration.sql

# Migration'ları çalıştır
php artisan migrate

# Seeder'ı çalıştır
php artisan db:seed --class=GameSeeder

# Doğrula
php artisan tinker
>>> Game::count() // 2 olmalı
>>> Tournament::whereNull('game_id')->count() // 0 olmalı
```

### Faz 2: Model Güncellemeleri (1-2 gün)

#### 2.1. Game Model Oluştur

```bash
php artisan make:model Game
```

```php
// app/Models/Game.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'name', 'slug', 'logo', 'description', 'status', 'settings'
    ];
    
    protected $casts = [
        'settings' => 'array',
    ];
    
    // İlişkiler
    public function tournaments() { return $this->hasMany(Tournament::class); }
    public function clans() { return $this->hasMany(Clan::class); }
    public function lfgPosts() { return $this->hasMany(LfgPost::class); }
    public function badges() { return $this->hasMany(Badge::class); }
    
    // Scope'lar
    public function scopeActive($query) {
        return $query->where('status', 'active');
    }
    
    public function scopeBySlug($query, $slug) {
        return $query->where('slug', $slug);
    }
    
    // Helper metodlar
    public function getThemeColor(): string {
        return $this->settings['theme_color'] ?? '#FF6B00';
    }
    
    public function getMaxTeamSize(): int {
        return $this->settings['max_team_size'] ?? 4;
    }
}
```

#### 2.2. GameScope Oluştur

```bash
mkdir -p app/Models/Scopes
```

```php
// app/Models/Scopes/GameScope.php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GameScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $gameId = session('game_id');
        
        if ($gameId) {
            $builder->where($model->getTable() . '.game_id', $gameId);
        }
    }
}
```

#### 2.3. Mevcut Modelleri Güncelle

**Tournament Model:**
```php
// app/Models/Tournament.php
use App\Models\Scopes\GameScope;

class Tournament extends Model
{
    protected static function booted()
    {
        // Global scope ekle
        static::addGlobalScope(new GameScope());
        
        // Otomatik game_id atama
        static::creating(function ($model) {
            if (!$model->game_id && session('game_id')) {
                $model->game_id = session('game_id');
            }
        });
    }
    
    // İlişki ekle
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
    
    // Scope metodları
    public function scopeForGame($query, $gameId)
    {
        return $query->where('game_id', $gameId);
    }
    
    public function scopeWithoutGameScope($query)
    {
        return $query->withoutGlobalScope(GameScope::class);
    }
}
```

**Aynı değişiklikleri şu modellere de uygula:**
- `Clan`
- `LfgPost`
- `CommunityPost`
- `GuidePost`
- `Badge` (nullable game_id)
- `Notification` (nullable game_id)

### Faz 3: Middleware ve Services (1 gün)

#### 3.1. DetectGame Middleware

```bash
php artisan make:middleware DetectGame
```

```php
// app/Http/Middleware/DetectGame.php
namespace App\Http\Middleware;

use App\Models\Game;
use Closure;
use Illuminate\Http\Request;

class DetectGame
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // Ana domain kontrolü
        if (count($parts) < 3) {
            session()->forget(['game_id', 'game']);
            return $next($request);
        }
        
        // Subdomain'i al
        $subdomain = $parts[0];
        
        // Oyunu bul
        $game = Game::active()->bySlug($subdomain)->first();
        
        if (!$game) {
            return redirect()->route('main.home')
                ->with('error', 'Geçersiz oyun seçimi');
        }
        
        // Session'a kaydet
        session([
            'game_id' => $game->id,
            'game' => $game,
        ]);
        
        // View'a paylaş
        view()->share('currentGame', $game);
        
        return $next($request);
    }
}
```

**Middleware'i kaydet:**
```php
// bootstrap/app.php veya app/Http/Kernel.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\DetectGame::class,
    ]);
})
```

#### 3.2. GameService Oluştur

```bash
php artisan make:service GameService
# veya manuel oluştur
```

```php
// app/Services/GameService.php
namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GameService
{
    public function getCurrentGame(): ?Game
    {
        $gameId = session('game_id');
        
        if (!$gameId) {
            return null;
        }
        
        return Cache::remember(
            "game.{$gameId}",
            3600,
            fn() => Game::find($gameId)
        );
    }
    
    public function switchGame(string $slug): string
    {
        $game = Game::active()->bySlug($slug)->firstOrFail();
        
        session([
            'game_id' => $game->id,
            'game' => $game,
        ]);
        
        return $this->getGameUrl($game);
    }
    
    public function getActiveGames(): Collection
    {
        return Cache::remember(
            'games.active',
            3600,
            fn() => Game::active()->orderBy('name')->get()
        );
    }
    
    public function getGameUrl(Game $game): string
    {
        $domain = config('app.domain', 'takimsistemi.com');
        return "https://{$game->slug}.{$domain}";
    }
    
    public function getPlatformStats(): array
    {
        return [
            'total_users' => \App\Models\User::count(),
            'total_teams' => \App\Models\Tournament::withoutGlobalScope(\App\Models\Scopes\GameScope::class)->count(),
            'total_clans' => \App\Models\Clan::withoutGlobalScope(\App\Models\Scopes\GameScope::class)->count(),
            'games' => Game::active()->withCount([
                'tournaments',
                'clans',
                'lfgPosts'
            ])->get(),
        ];
    }
}
```

### Faz 4: Routes ve Controllers (1 gün)

#### 4.1. Route Yapısını Güncelle

```php
// routes/web.php
use App\Http\Middleware\DetectGame;

// Ana domain route'ları
Route::domain(config('app.domain'))->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('main.home');
});

// Game subdomain route'ları
Route::domain('{game}.' . config('app.domain'))
    ->middleware([DetectGame::class])
    ->group(function () {
        // Mevcut route'lar buraya
        Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
        Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
        // ...
    });
```

#### 4.2. MainController Oluştur

```bash
php artisan make:controller MainController
```

```php
// app/Http/Controllers/MainController.php
namespace App\Http\Controllers;

use App\Services\GameService;

class MainController extends Controller
{
    public function __construct(
        private GameService $gameService
    ) {}
    
    public function index()
    {
        $games = $this->gameService->getActiveGames();
        $stats = $this->gameService->getPlatformStats();
        
        return view('main.home', compact('games', 'stats'));
    }
}
```

### Faz 5: Views ve Frontend (2-3 gün)

#### 5.1. Landing Page View'ları

```bash
mkdir -p resources/views/main
mkdir -p resources/views/main/partials
```

```blade
{{-- resources/views/main/home.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-center mb-8">
        Takım Sistemi - Multi-Game Platform
    </h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($games as $game)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <img src="{{ $game->logo }}" alt="{{ $game->name }}" class="w-full h-48 object-cover rounded">
                <h2 class="text-2xl font-bold mt-4">{{ $game->name }}</h2>
                <p class="text-gray-600 mt-2">{{ $game->description }}</p>
                <a href="https://{{ $game->slug }}.{{ config('app.domain') }}" 
                   class="btn btn-primary mt-4 block text-center">
                    Oyuna Git
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
```

#### 5.2. Game Switcher Component

```blade
{{-- resources/views/components/game-switcher.blade.php --}}
@if(isset($currentGame))
<div class="relative">
    <button class="flex items-center space-x-2 px-4 py-2 bg-gray-800 rounded">
        <img src="{{ $currentGame->logo }}" alt="{{ $currentGame->name }}" class="w-6 h-6">
        <span>{{ $currentGame->name }}</span>
    </button>
    
    <div class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg hidden">
        @foreach(app(\App\Services\GameService::class)->getActiveGames() as $game)
            <a href="https://{{ $game->slug }}.{{ config('app.domain') }}" 
               class="block px-4 py-2 hover:bg-gray-100">
                {{ $game->name }}
            </a>
        @endforeach
    </div>
</div>
@endif
```

#### 5.3. Oyuna Özel Temalar

```bash
mkdir -p resources/css/themes
```

```css
/* resources/css/themes/pubg.css */
:root {
    --primary-color: #FF6B00;
    --secondary-color: #FFB800;
}

/* resources/css/themes/cod.css */
:root {
    --primary-color: #FF6B00;
    --secondary-color: #000000;
}
```

### Faz 6: Configuration (30 dakika)

#### 6.1. games.php Config

```bash
touch config/games.php
```

```php
// config/games.php
return [
    'default_game_id' => env('DEFAULT_GAME_ID', 1),
    
    'cache_ttl' => env('GAME_CACHE_TTL', 3600),
    
    'features' => [
        'tournaments' => true,
        'clans' => true,
        'lfg' => true,
        'matchmaking' => true,
    ],
];
```

#### 6.2. Session Config

```php
// config/session.php
'domain' => env('SESSION_DOMAIN', '.takimsistemi.com'),
'secure' => env('SESSION_SECURE_COOKIE', true),
'same_site' => 'lax',
```

#### 6.3. .env Güncellemeleri

```env
# .env
APP_DOMAIN=takimsistemi.com
SESSION_DOMAIN=.takimsistemi.com
SANCTUM_STATEFUL_DOMAINS=*.takimsistemi.com,takimsistemi.com

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

---

## Testing

### Unit Tests

```php
// tests/Unit/GameTest.php
public function test_game_has_correct_theme_color()
{
    $game = Game::factory()->create([
        'settings' => ['theme_color' => '#FF6B00']
    ]);
    
    $this->assertEquals('#FF6B00', $game->getThemeColor());
}
```

### Feature Tests

```php
// tests/Feature/SubdomainRoutingTest.php
public function test_subdomain_detects_game()
{
    $game = Game::factory()->create(['slug' => 'pubg']);
    
    $response = $this->get('http://pubg.takimsistemi.test/teams');
    
    $response->assertOk();
    $this->assertEquals($game->id, session('game_id'));
}
```

### Test Çalıştırma

```bash
# Tüm testler
php artisan test

# Coverage
php artisan test --coverage

# Belirli test
php artisan test --filter=GameTest
```

---

## Deployment

### Production Deployment Checklist

```bash
# 1. Veritabanı yedeği
mysqldump -u root -p pubg_community > backup_production_$(date +%Y%m%d).sql

# 2. Maintenance mode
php artisan down

# 3. Kod güncelleme
git pull origin main
composer install --no-dev --optimize-autoloader
npm run build

# 4. Migration
php artisan migrate --force

# 5. Seeder
php artisan db:seed --class=GameSeeder --force

# 6. Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Queue restart
php artisan queue:restart

# 8. Doğrulama
php artisan tinker
>>> Game::count()
>>> Tournament::whereNull('game_id')->count()

# 9. Maintenance mode kapat
php artisan up
```

### DNS Configuration

```
# Wildcard subdomain ekle
*.takimsistemi.com → Server IP

# SSL Certificate
certbot certonly --webroot -w /var/www/html \
  -d takimsistemi.com \
  -d *.takimsistemi.com
```

---

## Rollback

### Migration Rollback

```bash
# 1. Maintenance mode
php artisan down

# 2. Migration geri al
php artisan migrate:rollback --step=2

# 3. Veritabanı restore
mysql -u root -p pubg_community < backup_production_20251203.sql

# 4. Kod geri al
git reset --hard HEAD~5

# 5. Cache temizle
php artisan cache:clear
php artisan config:clear

# 6. Maintenance mode kapat
php artisan up
```

---

## Troubleshooting

### Problem: Migration başarısız

**Çözüm:**
```bash
# Foreign key constraint hatası
php artisan migrate:rollback
# Migration dosyasını düzelt
php artisan migrate
```

### Problem: Session paylaşılmıyor

**Çözüm:**
```env
# .env
SESSION_DOMAIN=.takimsistemi.com  # Nokta ile başlamalı!
SESSION_DRIVER=redis  # File yerine Redis kullan
```

### Problem: Global scope çalışmıyor

**Çözüm:**
```php
// Model'de booted() metodunu kontrol et
protected static function booted()
{
    static::addGlobalScope(new GameScope());
}

// Session'da game_id var mı kontrol et
dd(session('game_id'));
```

### Problem: Subdomain algılanmıyor

**Çözüm:**
```bash
# hosts dosyasını kontrol et
cat /etc/hosts
# 127.0.0.1 pubg.takimsistemi.test olmalı

# Apache virtual host kontrol et
# ServerAlias *.takimsistemi.test olmalı
```

---

## Sonuç

Bu migration guide'ı takip ederek, mevcut PUBG platformunuzu güvenli bir şekilde multi-game platformuna dönüştürebilirsiniz.

**Önemli Noktalar:**
- ✅ Her adımda yedek alın
- ✅ Test ortamında önce deneyin
- ✅ Migration'ları adım adım çalıştırın
- ✅ Her aşamada doğrulama yapın
- ✅ Rollback planınız hazır olsun

**Destek:**
- GitHub Issues: [repo-url]/issues
- Email: support@takimsistemi.com
- Discord: [discord-server]

---

**Versiyon:** 2.0.0  
**Son Güncelleme:** 2025-12-03  
**Yazar:** Takım Sistemi Development Team
