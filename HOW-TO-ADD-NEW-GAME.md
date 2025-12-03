# 🎮 Yeni Oyun Ekleme Rehberi

Bu rehber, Takım Sistemi platformuna yeni bir oyun ekleme sürecini adım adım açıklar.

## İçindekiler

1. [Gereksinimler](#gereksinimler)
2. [Adım 1: Veritabanına Oyun Ekleme](#adım-1-veritabanına-oyun-ekleme)
3. [Adım 2: Tema Oluşturma](#adım-2-tema-oluşturma)
4. [Adım 3: Logo ve Asset'ler](#adım-3-logo-ve-assetler)
5. [Adım 4: Subdomain Yapılandırması](#adım-4-subdomain-yapılandırması)
6. [Adım 5: Test](#adım-5-test)
7. [Adım 6: Production Deployment](#adım-6-production-deployment)

---

## Gereksinimler

- ✅ Admin yetkisi
- ✅ Oyun logosu (PNG, 512x512px)
- ✅ Oyun ikonu (PNG, 128x128px)
- ✅ Tema renkleri (primary, secondary)
- ✅ Oyun ayarları (max team size, platforms, vb.)

---

## Adım 1: Veritabanına Oyun Ekleme

### Yöntem 1: Tinker ile (Önerilen)

```bash
php artisan tinker
```

```php
use App\Models\Game;

Game::create([
    'name' => 'Call of Duty Mobile',
    'slug' => 'cod',
    'logo' => 'storage/games/cod-logo.png',
    'icon' => 'storage/games/cod-icon.png',
    'description' => 'Call of Duty Mobile topluluk platformu. Takım bul, turnuvalara katıl, klan oluştur.',
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
        'ranks' => [
            'Rookie',
            'Veteran',
            'Elite',
            'Pro',
            'Master',
            'Grandmaster',
            'Legendary'
        ],
        'modes' => [
            'Multiplayer',
            'Battle Royale',
            'Zombies'
        ]
    ],
    'order' => 2
]);
```

### Yöntem 2: Seeder ile

```bash
php artisan make:seeder CodGameSeeder
```

```php
// database/seeders/CodGameSeeder.php
namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class CodGameSeeder extends Seeder
{
    public function run()
    {
        Game::create([
            'name' => 'Call of Duty Mobile',
            'slug' => 'cod',
            'logo' => 'storage/games/cod-logo.png',
            'icon' => 'storage/games/cod-icon.png',
            'description' => 'Call of Duty Mobile topluluk platformu',
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
            'order' => 2
        ]);
        
        $this->command->info('✅ COD Mobile oyunu eklendi');
    }
}
```

```bash
php artisan db:seed --class=CodGameSeeder
```

### Doğrulama

```bash
php artisan tinker
>>> Game::where('slug', 'cod')->first()
>>> Game::count() // Toplam oyun sayısı
```

---

## Adım 2: Tema Oluşturma

### 2.1. CSS Tema Dosyası Oluştur

```bash
# Tema dosyası oluştur
touch resources/css/themes/cod.css
```

```css
/* resources/css/themes/cod.css */

:root {
    /* Ana renkler */
    --primary-color: #FF6B00;
    --primary-hover: #E55F00;
    --secondary-color: #000000;
    --secondary-hover: #1A1A1A;
    
    /* Arka plan renkleri */
    --bg-primary: #0A0A0A;
    --bg-secondary: #1A1A1A;
    --bg-tertiary: #2A2A2A;
    
    /* Text renkleri */
    --text-primary: #FFFFFF;
    --text-secondary: #CCCCCC;
    --text-muted: #999999;
    
    /* Border renkleri */
    --border-color: #333333;
    --border-hover: #444444;
    
    /* Success/Error renkleri */
    --success-color: #10B981;
    --error-color: #EF4444;
    --warning-color: #F59E0B;
    --info-color: #3B82F6;
}

/* Oyuna özel stiller */
.game-cod {
    background-image: url('/images/games/cod-background.jpg');
    background-size: cover;
    background-position: center;
}

.game-cod .btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.game-cod .btn-primary:hover {
    background-color: var(--primary-hover);
    border-color: var(--primary-hover);
}

.game-cod .card {
    background-color: var(--bg-secondary);
    border-color: var(--border-color);
}

.game-cod .navbar {
    background-color: var(--bg-primary);
    border-bottom: 2px solid var(--primary-color);
}

/* Rank badge'leri */
.game-cod .rank-badge {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: var(--text-primary);
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-weight: 600;
}
```

### 2.2. Tema Yükleme Logic'i

Layout dosyanızda tema dinamik olarak yüklenmelidir:

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    
    {{-- Ana CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Oyuna özel tema --}}
    @if(isset($currentGame))
        <link rel="stylesheet" href="{{ asset('css/themes/' . $currentGame->slug . '.css') }}">
        <style>
            :root {
                --game-primary: {{ $currentGame->getThemeColor() }};
                --game-secondary: {{ $currentGame->settings['secondary_color'] ?? '#000000' }};
            }
        </style>
    @endif
</head>
<body class="@if(isset($currentGame)) game-{{ $currentGame->slug }} @endif">
    @yield('content')
</body>
</html>
```

### 2.3. CSS Build

```bash
# Development
npm run dev

# Production
npm run build
```

---

## Adım 3: Logo ve Asset'ler

### 3.1. Logo Hazırlama

**Gereksinimler:**
- **Logo:** 512x512px, PNG, şeffaf arka plan
- **Icon:** 128x128px, PNG, şeffaf arka plan
- **Background:** 1920x1080px, JPG/PNG

### 3.2. Asset'leri Yükleme

```bash
# Storage dizinini oluştur
mkdir -p storage/app/public/games

# Logo'ları kopyala
cp /path/to/cod-logo.png storage/app/public/games/
cp /path/to/cod-icon.png storage/app/public/games/
cp /path/to/cod-background.jpg public/images/games/

# Symlink'i yenile (gerekirse)
php artisan storage:link
```

### 3.3. Veritabanını Güncelle

```bash
php artisan tinker
```

```php
$game = Game::where('slug', 'cod')->first();
$game->logo = 'storage/games/cod-logo.png';
$game->icon = 'storage/games/cod-icon.png';
$game->save();
```

---

## Adım 4: Subdomain Yapılandırması

### 4.1. Local Development (Windows/XAMPP)

**hosts dosyasını düzenle:**
```
C:\Windows\System32\drivers\etc\hosts
```

Ekle:
```
127.0.0.1 cod.takimsistemi.test
```

**Apache Virtual Host:**
```apache
# apache-vhost-template.conf
<VirtualHost *:80>
    ServerName takimsistemi.test
    ServerAlias *.takimsistemi.test
    DocumentRoot "C:/xampp/htdocs/pubg-community/public"
    
    <Directory "C:/xampp/htdocs/pubg-community/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Apache'yi yeniden başlat:**
```bash
# XAMPP Control Panel'den Apache'yi restart et
```

### 4.2. Production (Linux/Nginx)

**DNS Ayarları:**
```
# DNS provider'ınızda wildcard subdomain ekleyin
*.takimsistemi.com → Server IP
```

**Nginx Configuration:**
```nginx
# /etc/nginx/sites-available/takimsistemi.com
server {
    listen 80;
    listen [::]:80;
    server_name takimsistemi.com *.takimsistemi.com;
    
    root /var/www/takimsistemi/public;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

**SSL Certificate (Let's Encrypt):**
```bash
sudo certbot --nginx -d takimsistemi.com -d *.takimsistemi.com
```

**Nginx'i yeniden başlat:**
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## Adım 5: Test

### 5.1. Manuel Test

**1. Ana sayfayı kontrol et:**
```
http://takimsistemi.test
```
- ✅ COD oyunu listede görünüyor mu?
- ✅ Logo doğru görünüyor mu?
- ✅ Açıklama doğru mu?

**2. COD subdomain'ini test et:**
```
http://cod.takimsistemi.test
```
- ✅ Sayfa açılıyor mu?
- ✅ Tema renkleri doğru mu?
- ✅ Session'da game_id var mı?

**3. Oyuna özel içerik oluştur:**
- ✅ LFG ilanı oluştur
- ✅ Klan oluştur
- ✅ Turnuva oluştur

**4. Oyun değiştirmeyi test et:**
- ✅ Game switcher'dan PUBG'ye geç
- ✅ Session güncellendi mi?
- ✅ Tema değişti mi?

### 5.2. Otomatik Test

```bash
# Test dosyası oluştur
php artisan make:test CodGameTest
```

```php
// tests/Feature/CodGameTest.php
namespace Tests\Feature;

use App\Models\Game;
use Tests\TestCase;

class CodGameTest extends TestCase
{
    public function test_cod_game_exists()
    {
        $game = Game::where('slug', 'cod')->first();
        
        $this->assertNotNull($game);
        $this->assertEquals('Call of Duty Mobile', $game->name);
        $this->assertEquals('active', $game->status);
    }
    
    public function test_cod_subdomain_works()
    {
        $game = Game::factory()->create(['slug' => 'cod']);
        
        $response = $this->get('http://cod.takimsistemi.test/');
        
        $response->assertOk();
        $this->assertEquals($game->id, session('game_id'));
    }
    
    public function test_cod_theme_loads()
    {
        $game = Game::factory()->create([
            'slug' => 'cod',
            'settings' => ['theme_color' => '#FF6B00']
        ]);
        
        $this->setGameContext($game);
        
        $response = $this->get('/');
        
        $response->assertSee('game-cod');
        $response->assertSee('#FF6B00');
    }
}
```

```bash
# Test'leri çalıştır
php artisan test --filter=CodGameTest
```

### 5.3. API Test

```bash
# Oyun listesini al
curl -X GET "http://takimsistemi.test/api/v1/games"

# COD oyununu al
curl -X GET "http://takimsistemi.test/api/v1/games/cod"

# COD LFG ilanlarını al
curl -X GET "http://takimsistemi.test/api/v1/lfg?game_slug=cod"
```

---

## Adım 6: Production Deployment

### 6.1. Pre-Deployment Checklist

- [ ] Veritabanı yedeği alındı
- [ ] Logo ve asset'ler yüklendi
- [ ] Tema CSS'i build edildi
- [ ] DNS ayarları yapıldı
- [ ] SSL sertifikası alındı
- [ ] Test'ler geçti

### 6.2. Deployment

```bash
# 1. Maintenance mode
php artisan down

# 2. Kod güncelleme
git pull origin main

# 3. Dependencies
composer install --no-dev --optimize-autoloader
npm run build

# 4. Oyunu ekle
php artisan tinker
>>> Game::create([...])

# 5. Asset'leri yükle
# Logo'ları storage/app/public/games/ dizinine kopyala

# 6. Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Doğrulama
php artisan tinker
>>> Game::where('slug', 'cod')->first()

# 8. Maintenance mode kapat
php artisan up
```

### 6.3. Post-Deployment Verification

```bash
# 1. Ana sayfa
curl -I https://takimsistemi.com

# 2. COD subdomain
curl -I https://cod.takimsistemi.com

# 3. API
curl https://takimsistemi.com/api/v1/games/cod

# 4. Log'ları kontrol et
tail -f storage/logs/laravel.log
```

---

## Troubleshooting

### Problem: Subdomain çalışmıyor

**Çözüm:**
```bash
# hosts dosyasını kontrol et
cat /etc/hosts | grep cod

# DNS propagation kontrol et
nslookup cod.takimsistemi.com

# Apache/Nginx config kontrol et
sudo nginx -t
```

### Problem: Tema yüklenmiyor

**Çözüm:**
```bash
# CSS build edildi mi?
npm run build

# Dosya var mı?
ls -la public/css/themes/cod.css

# Cache temizle
php artisan view:clear
```

### Problem: Logo görünmüyor

**Çözüm:**
```bash
# Storage link var mı?
php artisan storage:link

# Dosya var mı?
ls -la storage/app/public/games/cod-logo.png

# Permissions
chmod -R 755 storage/app/public/games/
```

### Problem: Session paylaşılmıyor

**Çözüm:**
```env
# .env
SESSION_DOMAIN=.takimsistemi.com  # Nokta ile başlamalı!
SESSION_DRIVER=redis  # File yerine Redis
```

---

## Örnek: Valorant Ekleme

Tam bir örnek olarak Valorant oyununu ekleyelim:

```bash
# 1. Tinker ile oyun ekle
php artisan tinker
```

```php
Game::create([
    'name' => 'Valorant',
    'slug' => 'valorant',
    'logo' => 'storage/games/valorant-logo.png',
    'icon' => 'storage/games/valorant-icon.png',
    'description' => 'Valorant topluluk platformu. Takım bul, turnuvalara katıl.',
    'status' => 'active',
    'settings' => [
        'theme_color' => '#FF4655',
        'secondary_color' => '#0F1923',
        'max_team_size' => 5,
        'platforms' => ['PC'],
        'features' => [
            'tournaments' => true,
            'clans' => true,
            'lfg' => true,
            'matchmaking' => true,
        ],
        'ranks' => [
            'Iron', 'Bronze', 'Silver', 'Gold', 
            'Platinum', 'Diamond', 'Immortal', 'Radiant'
        ],
        'modes' => [
            'Unrated', 'Competitive', 'Spike Rush', 'Deathmatch'
        ]
    ],
    'order' => 3
]);
```

```bash
# 2. Tema oluştur
cat > resources/css/themes/valorant.css << 'EOF'
:root {
    --primary-color: #FF4655;
    --secondary-color: #0F1923;
}

.game-valorant {
    background-image: url('/images/games/valorant-background.jpg');
}

.game-valorant .btn-primary {
    background-color: var(--primary-color);
}
EOF

# 3. Build
npm run build

# 4. hosts dosyasına ekle
echo "127.0.0.1 valorant.takimsistemi.test" | sudo tee -a /etc/hosts

# 5. Test
curl http://valorant.takimsistemi.test
```

---

## Sonuç

Yeni oyun ekleme süreci tamamlandı! Artık platformunuzda yeni bir oyun var.

**Özet:**
1. ✅ Veritabanına oyun eklendi
2. ✅ Tema oluşturuldu
3. ✅ Logo ve asset'ler yüklendi
4. ✅ Subdomain yapılandırıldı
5. ✅ Test edildi
6. ✅ Production'a deploy edildi

**Sonraki Adımlar:**
- Oyuna özel içerik oluştur (turnuvalar, klanlar)
- Kullanıcıları bilgilendir
- Analytics takip et
- Feedback topla

---

**Versiyon:** 2.0.0  
**Son Güncelleme:** 2025-12-03  
**Yazar:** Takım Sistemi Development Team
