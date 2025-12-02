# 🎮 PUBG Mobile Topluluk Platformu

Laravel 11 tabanlı, PUBG Mobile ve diğer mobil oyunlar için oyuncu topluluk platformu.

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
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pubg_community
DB_USERNAME=root
DB_PASSWORD=
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

### 7. Sunucuyu Başlat
```bash
php artisan serve
```

Tarayıcıda `http://localhost:8000` adresini aç.

## 🔑 Test Kullanıcıları

Seeder çalıştırıldıktan sonra:

**Admin Kullanıcı:**
- Email: admin@pubg.com
- Şifre: password

**Normal Kullanıcı:**
- Email: user@pubg.com
- Şifre: password

## 📚 API Dokümantasyonu

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
- **Auth**: `/api/v1/register`, `/api/v1/login`, `/api/v1/logout`, `/api/v1/me`
- **Games**: `/api/v1/games`
- **LFG**: `/api/v1/lfg`
- **Clans**: `/api/v1/clans`
- **Guides**: `/api/v1/guides`
- **Squads**: `/api/v1/squads`
- **Tournaments**: `/api/v1/tournaments`
- **Messages**: `/api/v1/messages`
- **Notifications**: `/api/v1/notifications`
- **Friendships**: `/api/v1/friends`
- **Matchmaking**: `/api/v1/matchmaking`

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
│   │   │   └── Admin/           # Admin Controller'lar
│   │   ├── Middleware/
│   │   ├── Requests/            # Form Validation
│   │   └── Policies/            # Authorization
│   ├── Models/                  # Eloquent Model'ler
│   ├── Services/                # Business Logic
│   └── Notifications/           # Bildirim Sınıfları
├── database/
│   ├── migrations/              # Veritabanı Migration'ları
│   └── seeders/                 # Seed Data
├── resources/
│   └── views/                   # Blade Template'ler
├── routes/
│   ├── api.php                  # API Route'lar
│   ├── web.php                  # Web Route'lar
│   └── console.php
└── tests/
    ├── Feature/                 # Feature Tests
    └── Unit/                    # Unit Tests
```

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

---

**Versiyon**: 1.0.0  
**Durum**: Production Ready ✅
