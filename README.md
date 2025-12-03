# 🎮 Takım Sistemi - Multi-Game Platform

Laravel 11 tabanlı, çoklu oyun destekleyen oyuncu topluluk platformu. PUBG Mobile, COD Mobile ve diğer mobil oyunlar için birleşik topluluk deneyimi.

## 🌟 Multi-Game Platform

**Takım Sistemi**, subdomain tabanlı multi-tenant mimari ile her oyun için ayrı deneyim sunarken, merkezi kullanıcı yönetimi ve cross-game özellikleri destekler.

### Desteklenen Oyunlar
- 🎯 PUBG Mobile - `pubg.takimsistemi.com`
- 🔫 COD Mobile - `cod.takimsistemi.com`
- 🎮 Diğer oyunlar kolayca eklenebilir

### Platform Özellikleri
- ✅ **Tek Hesap, Tüm Oyunlar** - Kullanıcılar tek hesapla tüm oyunlara erişir
- ✅ **Oyuna Özel Deneyim** - Her oyun kendi teması ve içeriğiyle
- ✅ **Cross-Game Özellikler** - Mesajlaşma, arkadaşlık, bildirimler tüm oyunlarda çalışır
- ✅ **Veri İzolasyonu** - Her oyunun verileri güvenli şekilde ayrılır
- ✅ **Kolay Yönetim** - Tek veritabanı, tek uygulama

## 🚀 Özellikler

### Kullanıcı Özellikleri
- ✅ Kayıt/Giriş sistemi (Laravel Sanctum)
- ✅ Profil yönetimi (PUBG bilgileri, sosyal medya)
- ✅ XP & Level sistemi (14 farklı kazanma yolu)
- ✅ Rozet sistemi
- ✅ Bildirim sistemi (5 tip)

### Topluluk Özellikleri
- ✅ LFG (Looking For Group) ilanları
- ✅ Klan sistemi
- ✅ Kalıcı takımlar
- ✅ Turnuva sistemi
- ✅ Rehber yazma
- ✅ Topluluk paylaşımları
- ✅ Arkadaşlık sistemi
- ✅ Mesajlaşma sistemi
- ✅ Matchmaking sistemi (Otomatik eşleştirme)

### Teknik Özellikler
- ✅ Cihaz & hassasiyet ayarları paylaşımı
- ✅ Admin panel
- ✅ RESTful API (68+ endpoint)
- ✅ Responsive tasarım (Tailwind CSS)
- ✅ 80+ test coverage

## 📋 Gereksinimler

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM
- 2GB+ RAM

## 🛠️ Kurulum

### 1. Projeyi Klonla
```bash
git clone [repo-url]
cd pubg-community
```

### 2. Bağımlılıkları Yükle
```bash
composer install
npm install
```

### 3. Environment Ayarları
```bash
cp .env.example .env
php artisan key:generate
```

`.env` dosyasını düzenle:
```env
# Veritabanı
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pubg_community
DB_USERNAME=root
DB_PASSWORD=

# Multi-Game Platform
APP_DOMAIN=takimsistemi.com
SESSION_DOMAIN=.takimsistemi.com
SANCTUM_STATEFUL_DOMAINS=*.takimsistemi.com,takimsistemi.com

# Cache & Session (Production için Redis önerilir)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 4. Veritabanını Hazırla
```bash
php artisan migrate
php artisan db:seed
```

### 5. Storage Link Oluştur
```bash
php artisan storage:link
```

### 6. Frontend Build
```bash
npm run build
# veya development için
npm run dev
```

### 7. Local Subdomain Kurulumu

**Windows (XAMPP/Laragon):**
```bash
# hosts dosyasını düzenle (C:\Windows\System32\drivers\etc\hosts)
127.0.0.1 takimsistemi.test
127.0.0.1 pubg.takimsistemi.test
127.0.0.1 cod.takimsistemi.test

# Apache virtual host yapılandırması için
# apache-vhost-template.conf dosyasına bakın
```

**Linux/Mac:**
```bash
# /etc/hosts dosyasını düzenle
sudo nano /etc/hosts

# Ekle:
127.0.0.1 takimsistemi.test
127.0.0.1 pubg.takimsistemi.test
127.0.0.1 cod.takimsistemi.test
```

Detaylı kurulum için: [LOCAL-TEST-ORTAMI-KURULUM.md](LOCAL-TEST-ORTAMI-KURULUM.md)

### 8. Sunucuyu Başlat
```bash
php artisan serve
```

**Ana Sayfa:** `http://takimsistemi.test`  
**PUBG:** `http://pubg.takimsistemi.test`  
**COD Mobile:** `http://cod.takimsistemi.test`

## 🔑 Test Kullanıcıları

Seeder çalıştırıldıktan sonra:

**Admin Kullanıcı:**
- Email: admin@pubg.com
- Şifre: password

**Normal Kullanıcı:**
- Email: user@pubg.com
- Şifre: password

## 📚 API Dokümantasyonu

### ⚠️ Multi-Game Platform Güncellemesi

**Önemli:** 2025-12-02 tarihinden itibaren, tüm oyuna özel API endpoint'leri `game_id` veya `game_slug` parametresi gerektirir.

**Eski endpoint'ler (Deprecated):**
```bash
GET /api/v1/lfg-posts          # ❌ Kullanımdan kaldırıldı
GET /api/v1/clan-list          # ❌ Kullanımdan kaldırıldı
```

**Yeni endpoint'ler:**
```bash
GET /api/v1/lfg?game_slug=pubg           # ✅ Önerilen
GET /api/v1/lfg?game_id=1                # ✅ Alternatif
GET /api/v1/clans?game_slug=cod-mobile   # ✅ Önerilen
```

**Migration Guide:** [API-MIGRATION-GUIDE.md](API-MIGRATION-GUIDE.md)

### Authentication
```bash
# Kayıt
POST /api/v1/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}

# Giriş
POST /api/v1/login
{
  "email": "john@example.com",
  "password": "password"
}

# Token ile istek
Authorization: Bearer {token}
```

### Endpoint'ler

#### Oyun Yönetimi
- **Games**: `GET /api/v1/games` - Aktif oyunları listele
- **Game Detail**: `GET /api/v1/games/{slug}` - Oyun detayı

#### Oyuna Özel Endpoint'ler (game_slug veya game_id gerektirir)
- **LFG**: `GET /api/v1/lfg?game_slug=pubg`
- **Clans**: `GET /api/v1/clans?game_slug=pubg`
- **Guides**: `GET /api/v1/guides?game_slug=pubg`
- **Squads**: `GET /api/v1/squads?game_slug=pubg`
- **Tournaments**: `GET /api/v1/tournaments?game_slug=pubg`

#### Cross-Game Endpoint'ler (oyun parametresi gerektirmez)
- **Auth**: `/api/v1/register`, `/api/v1/login`, `/api/v1/logout`, `/api/v1/me`
- **Messages**: `/api/v1/messages`
- **Notifications**: `/api/v1/notifications`
- **Friendships**: `/api/v1/friends`

Detaylı API dokümantasyonu için: [API_DOCS.md](API_DOCS.md)

## 🧪 Test

```bash
# Tüm testleri çalıştır
php artisan test

# Coverage raporu
php artisan test --coverage

# Belirli bir test
php artisan test --filter=LfgTest
```

## 📁 Proje Yapısı

```
pubg-community/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/V1/          # API Controller'lar
│   │   │   ├── Web/             # Web Controller'lar
│   │   │   ├── Admin/           # Admin Controller'lar
│   │   │   └── MainController.php # Landing Page
│   │   ├── Middleware/
│   │   │   └── DetectGame.php   # 🆕 Oyun algılama
│   │   ├── Requests/            # Form Validation
│   │   └── Policies/            # Authorization
│   ├── Models/
│   │   ├── Game.php             # 🆕 Oyun modeli
│   │   ├── Scopes/
│   │   │   └── GameScope.php    # 🆕 Otomatik filtreleme
│   │   └── ...                  # Diğer modeller
│   ├── Services/
│   │   ├── GameService.php      # 🆕 Oyun yönetimi
│   │   ├── MultiTenantService.php # 🆕 Multi-tenant
│   │   └── ...                  # Diğer servisler
│   └── Notifications/           # Bildirim Sınıfları
├── config/
│   └── games.php                # 🆕 Oyun konfigürasyonu
├── database/
│   ├── migrations/
│   │   ├── *_create_games_table.php # 🆕
│   │   └── *_add_game_id_to_*.php   # 🆕
│   └── seeders/
│       └── GameSeeder.php       # 🆕
├── resources/
│   ├── css/
│   │   └── themes/              # 🆕 Oyuna özel temalar
│   │       ├── pubg.css
│   │       └── cod.css
│   └── views/
│       ├── main/                # 🆕 Landing page
│       └── components/
│           └── game-switcher.blade.php # 🆕
├── routes/
│   ├── api.php                  # API Route'lar
│   ├── web.php                  # Game subdomain route'lar
│   ├── main.php                 # 🆕 Ana domain route'lar
│   └── console.php
└── tests/
    ├── Feature/                 # Feature Tests
    └── Unit/                    # Unit Tests
```

## 🏗️ Mimari

### Multi-Tenant Yaklaşımı

**Hibrit Multi-Tenant Mimari:**
- Tek Laravel uygulaması
- Tek veritabanı, `game_id` ile veri izolasyonu
- Subdomain routing ile oyun bağlamı
- Global scope'lar ile otomatik filtreleme

### Request Flow

```
1. User → pubg.takimsistemi.com/teams
2. DetectGame Middleware → "pubg" subdomain'ini algıla
3. GameService → Game modelini bul (id=1)
4. Session → game_id=1 kaydet
5. Global Scope → Tüm query'leri game_id=1 ile filtrele
6. Controller → Sadece PUBG takımlarını döndür
7. View → PUBG teması ile render et
```

### Veri Modeli

```
┌──────────────┐
│    games     │ (Yeni)
│──────────────│
│ id           │
│ name         │
│ slug         │
│ settings     │
└──────────────┘
       │
       │ 1:N
       ▼
┌──────────────┐     ┌──────────────┐
│ tournaments  │     │    clans     │
│──────────────│     │──────────────│
│ game_id (FK) │     │ game_id (FK) │
└──────────────┘     └──────────────┘

┌──────────────┐
│    users     │ (Cross-Game)
│──────────────│
│ NO game_id   │ ← Tüm oyunlarda aynı hesap
└──────────────┘
```

Detaylı mimari dokümantasyon: [MULTI-GAME-ARCHITECTURE.md](MULTI-GAME-ARCHITECTURE.md)

## 🎯 XP Kazanma Yolları

| Aksiyon | XP |
|---------|-----|
| Profil tamamlama | 50 XP |
| LFG ilanı oluşturma | 10 XP |
| Klan oluşturma | 25 XP |
| Rehber yazma | 30 XP |
| Takım oluşturma | 40 XP |
| Turnuva oluşturma | 100 XP |
| Turnuvaya katılma | 30 XP |
| Turnuva kazanma | 200 XP |

## 🏆 Rozetler

- **100 XP** → Topluluğa İlk Adım
- **300 XP** → Aktif Oyuncu
- **600 XP** → Topluluk Çekirdeği

## 🔐 Güvenlik

- CSRF Protection
- XSS Protection
- SQL Injection Protection (Eloquent ORM)
- Password Hashing (bcrypt)
- API Rate Limiting
- Authorization Policies
- Banned User Middleware

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request açın

## 📞 İletişim

- GitHub: [github-url]
- Email: [email]
- Discord: [discord-server]

## 🙏 Teşekkürler

- Laravel Framework
- Tailwind CSS
- Alpine.js
- PUBG Mobile Community

## 📖 Ek Dokümantasyon

- **[MULTI-GAME-ARCHITECTURE.md](MULTI-GAME-ARCHITECTURE.md)** - Detaylı mimari dokümantasyonu
- **[MULTI-GAME-MIGRATION-GUIDE.md](MULTI-GAME-MIGRATION-GUIDE.md)** - Geliştiriciler için migration rehberi
- **[API-MIGRATION-GUIDE.md](API-MIGRATION-GUIDE.md)** - API değişiklikleri
- **[LOCAL-TEST-ORTAMI-KURULUM.md](LOCAL-TEST-ORTAMI-KURULUM.md)** - Local test ortamı kurulumu

## 🔄 Yeni Oyun Ekleme

```bash
# 1. Veritabanına oyun ekle
php artisan tinker
>>> Game::create([
    'name' => 'Call of Duty Mobile',
    'slug' => 'cod',
    'status' => 'active',
    'settings' => [
        'theme_color' => '#FF6B00',
        'max_team_size' => 5,
        'platforms' => ['Android', 'iOS']
    ]
]);

# 2. Tema dosyası oluştur
# resources/css/themes/cod.css

# 3. Subdomain yapılandırması
# hosts dosyasına: 127.0.0.1 cod.takimsistemi.test

# 4. Cache temizle
php artisan cache:clear
```

Detaylı rehber: [MULTI-GAME-MIGRATION-GUIDE.md](MULTI-GAME-MIGRATION-GUIDE.md)

---

**Versiyon**: 2.0.0 (Multi-Game Platform)  
**Durum**: Production Ready ✅  
**Son Güncelleme**: 2025-12-03
