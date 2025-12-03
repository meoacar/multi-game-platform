# Multi-Game Platform Deployment Kılavuzu

## 📋 İçindekiler

1. [Genel Bakış](#genel-bakış)
2. [Ön Hazırlık](#ön-hazırlık)
3. [Deployment Adımları](#deployment-adımları)
4. [Rollback Prosedürü](#rollback-prosedürü)
5. [Doğrulama](#doğrulama)
6. [Sorun Giderme](#sorun-giderme)

---

## 🎯 Genel Bakış

Bu kılavuz, PUBG Community platformunun çoklu oyun destekleyen "Takım Sistemi" platformuna dönüştürülmesi için deployment prosedürünü açıklar.

### Deployment Scriptleri

| Script | Açıklama |
|--------|----------|
| `deploy-multi-game.bat` | Ana deployment script'i |
| `rollback-multi-game.bat` | Geri alma script'i |
| `verify-deployment.bat` | Doğrulama script'i |
| `backup-database.bat` | Veritabanı yedekleme |
| `restore-database.bat` | Veritabanı geri yükleme |

---

## 🔧 Ön Hazırlık

### 1. Sistem Gereksinimleri

- ✅ PHP 8.1+
- ✅ MySQL 8.0+
- ✅ Composer 2.x
- ✅ Git
- ✅ Yeterli disk alanı (minimum 2GB boş alan)

### 2. Yedekleme

**ÖNEMLİ:** Deployment öncesi mutlaka yedek alın!

```bash
# Manuel yedek alma
backup-database.bat

# Yedek dosyası konumu
database-backups/pubg_community_YYYYMMDD-HHMMSS.sql
```

### 3. Ortam Kontrolü

```bash
# PHP versiyonu
php -v

# Composer versiyonu
composer --version

# MySQL bağlantısı
php artisan tinker --execute="DB::connection()->getPdo();"
```

### 4. .env Ayarları

Deployment öncesi `.env` dosyasında aşağıdaki ayarları kontrol edin:

```env
# Session ayarları (subdomain desteği için)
SESSION_DOMAIN=.takimsistemi.com
SESSION_DRIVER=redis  # veya database

# Sanctum ayarları
SANCTUM_STATEFUL_DOMAINS=takimsistemi.com,*.takimsistemi.com

# Cache ayarları (önerilen)
CACHE_DRIVER=redis

# Queue ayarları
QUEUE_CONNECTION=redis
```

---

## 🚀 Deployment Adımları

### Otomatik Deployment

```bash
# Ana deployment script'ini çalıştır
deploy-multi-game.bat
```

Script aşağıdaki adımları otomatik olarak gerçekleştirir:

1. ✅ Ortam kontrolleri
2. ✅ Veritabanı yedeği
3. ✅ Maintenance modu
4. ✅ Git pull
5. ✅ Composer install
6. ✅ Cache temizleme
7. ✅ Migration'lar
8. ✅ Seeder'lar
9. ✅ Cache optimize
10. ✅ Doğrulama

### Manuel Deployment

Eğer adım adım ilerlemek isterseniz:

#### 1. Yedek Al

```bash
backup-database.bat
```

#### 2. Maintenance Modu

```bash
php artisan down --retry=60
```

#### 3. Kodu Güncelle

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
```

#### 4. Cache Temizle

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

#### 5. Migration'ları Çalıştır

```bash
php artisan migrate --force
```

**Çalıştırılacak Migration'lar:**
- `2025_12_02_200000_update_games_table_for_multi_game.php`
- `2025_12_02_210000_add_game_id_to_remaining_tables.php`

#### 6. Seeder'ları Çalıştır

```bash
php artisan db:seed --class=GameSeeder --force
```

#### 7. Cache Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 8. Doğrulama

```bash
verify-deployment.bat
```

#### 9. Maintenance Modunu Kapat

```bash
php artisan up
```

---

## ⏮️ Rollback Prosedürü

Eğer deployment sırasında sorun yaşarsanız:

### Otomatik Rollback

```bash
rollback-multi-game.bat
```

### Manuel Rollback

#### 1. Yedek Al (Güvenlik)

```bash
backup-database.bat
```

#### 2. Maintenance Modu

```bash
php artisan down --retry=60
```

#### 3. Migration Rollback

```bash
# Son 2 migration'ı geri al
php artisan migrate:rollback --step=2 --force
```

#### 4. Cache Temizle

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

#### 5. Cache Yeniden Oluştur

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 6. Maintenance Modunu Kapat

```bash
php artisan up
```

### Veritabanı Geri Yükleme

Eğer migration rollback başarısız olursa:

```bash
# Yedek dosyasını geri yükle
restore-database.bat database-backups/pubg_community_YYYYMMDD-HHMMSS.sql
```

---

## ✅ Doğrulama

### Otomatik Doğrulama

```bash
verify-deployment.bat
```

### Manuel Doğrulama Kontrol Listesi

#### 1. Veritabanı Kontrolleri

```bash
# games tablosu var mı?
php artisan tinker --execute="Schema::hasTable('games')"

# Oyun sayısı
php artisan tinker --execute="App\Models\Game::count()"

# game_id kolonları var mı?
php artisan tinker --execute="Schema::hasColumn('tournaments', 'game_id')"
```

#### 2. Model Kontrolleri

```bash
# Game model çalışıyor mu?
php artisan tinker --execute="App\Models\Game::first()"

# GameScope çalışıyor mu?
php artisan tinker --execute="session(['game_id' => 1]); App\Models\Tournament::count()"
```

#### 3. Veri Bütünlüğü

```bash
# Kullanıcılar korundu mu?
php artisan tinker --execute="App\Models\User::count()"

# Turnuvalar korundu mu?
php artisan tinker --execute="App\Models\Tournament::withoutGlobalScope(App\Models\Scopes\GameScope::class)->count()"

# Klanlar korundu mu?
php artisan tinker --execute="App\Models\Clan::withoutGlobalScope(App\Models\Scopes\GameScope::class)->count()"
```

#### 4. Web Kontrolleri

- [ ] Ana sayfa açılıyor mu? → `http://takimsistemi.com`
- [ ] PUBG subdomain çalışıyor mu? → `http://pubg.takimsistemi.com`
- [ ] Kullanıcı girişi yapılabiliyor mu?
- [ ] Oyunlar arası geçiş çalışıyor mu?
- [ ] Turnuva listesi görünüyor mu?
- [ ] Klan listesi görünüyor mu?

#### 5. Log Kontrolleri

```bash
# Son hataları kontrol et
tail -n 50 storage/logs/laravel.log

# Veritabanı hatalarını ara
findstr /C:"database" storage\logs\laravel.log

# Migration hatalarını ara
findstr /C:"migration" storage\logs\laravel.log
```

---

## 🔧 Sorun Giderme

### Sorun 1: Migration Başarısız

**Belirtiler:**
- Migration çalışırken hata alınıyor
- "Table already exists" hatası

**Çözüm:**
```bash
# Migration durumunu kontrol et
php artisan migrate:status

# Sorunlu migration'ı manuel düzelt
# Veya rollback yap
rollback-multi-game.bat
```

### Sorun 2: GameScope Çalışmıyor

**Belirtiler:**
- Tüm oyunların verileri görünüyor
- Filtreleme çalışmıyor

**Çözüm:**
```bash
# Session'ı kontrol et
php artisan tinker --execute="session()->all()"

# GameScope'u manuel test et
php artisan tinker --execute="session(['game_id' => 1]); App\Models\Tournament::count()"

# Cache'i temizle
php artisan cache:clear
```

### Sorun 3: Subdomain Çalışmıyor

**Belirtiler:**
- pubg.takimsistemi.com açılmıyor
- Ana sayfaya yönlendiriliyor

**Çözüm:**

1. **hosts dosyasını kontrol et** (`C:\Windows\System32\drivers\etc\hosts`):
```
127.0.0.1 takimsistemi.com
127.0.0.1 pubg.takimsistemi.com
```

2. **Virtual host konfigürasyonunu kontrol et** (`G:\xampp\apache\conf\extra\httpd-vhosts.conf`):
```apache
<VirtualHost *:80>
    ServerName takimsistemi.com
    ServerAlias *.takimsistemi.com
    DocumentRoot "F:/Pubg/pubg-community/public"
    # ...
</VirtualHost>
```

3. **Apache'yi yeniden başlat**

### Sorun 4: Session Paylaşılmıyor

**Belirtiler:**
- Subdomain'ler arası geçişte oturum kapanıyor
- Her subdomain'de yeniden giriş gerekiyor

**Çözüm:**

`.env` dosyasını kontrol et:
```env
SESSION_DOMAIN=.takimsistemi.com
SESSION_DRIVER=redis  # veya database
```

Cache'i temizle:
```bash
php artisan config:clear
php artisan cache:clear
```

### Sorun 5: Veri Kaybı

**Belirtiler:**
- Kullanıcılar veya içerikler kayboldu
- Veritabanı boş görünüyor

**Çözüm:**

**HEMEN ROLLBACK YAP:**
```bash
rollback-multi-game.bat
```

Veya yedekten geri yükle:
```bash
restore-database.bat database-backups/pubg_community_YYYYMMDD-HHMMSS.sql
```

---

## 📞 Destek

Sorun yaşarsanız:

1. **Log dosyalarını kontrol edin:**
   - `storage/logs/laravel.log`
   - `storage/logs/laravel-YYYY-MM-DD.log`

2. **Doğrulama script'ini çalıştırın:**
   ```bash
   verify-deployment.bat
   ```

3. **Gerekirse rollback yapın:**
   ```bash
   rollback-multi-game.bat
   ```

4. **Yedekten geri yükleyin:**
   ```bash
   restore-database.bat [backup_file]
   ```

---

## 📝 Deployment Checklist

### Deployment Öncesi

- [ ] Veritabanı yedeği alındı
- [ ] .env ayarları kontrol edildi
- [ ] Disk alanı yeterli
- [ ] Kullanıcılara duyuru yapıldı
- [ ] Bakım penceresi planlandı

### Deployment Sırasında

- [ ] Maintenance modu aktif
- [ ] Migration'lar başarılı
- [ ] Seeder'lar çalıştırıldı
- [ ] Cache optimize edildi
- [ ] Doğrulama başarılı

### Deployment Sonrası

- [ ] Web sitesi açılıyor
- [ ] Subdomain'ler çalışıyor
- [ ] Kullanıcı girişi yapılabiliyor
- [ ] Veriler korunmuş
- [ ] Log'larda hata yok
- [ ] Kullanıcılara bilgi verildi

---

## 🎉 Başarılı Deployment

Deployment başarılı olduysa:

1. ✅ Ana sayfa çalışıyor
2. ✅ PUBG subdomain çalışıyor
3. ✅ Tüm veriler korunmuş
4. ✅ Oyunlar arası geçiş çalışıyor
5. ✅ Log'larda kritik hata yok

**Tebrikler! Multi-game platform başarıyla deploy edildi! 🚀**

---

## 📚 Ek Kaynaklar

- [MULTI-GAME-ARCHITECTURE.md](MULTI-GAME-ARCHITECTURE.md) - Mimari detayları
- [MULTI-GAME-MIGRATION-GUIDE.md](MULTI-GAME-MIGRATION-GUIDE.md) - Migration rehberi
- [HOW-TO-ADD-NEW-GAME.md](HOW-TO-ADD-NEW-GAME.md) - Yeni oyun ekleme
- [API_DOCS.md](API_DOCS.md) - API dokümantasyonu

---

**Son Güncelleme:** 2025-12-03
**Versiyon:** 1.0.0
