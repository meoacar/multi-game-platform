# 🚀 Canlıya Alma Kontrol Listesi - Multi-Game Platform

## ⚠️ CANLIYA ALMADAN ÖNCE MUTLAKA OKUYUN!

Bu dokümantasyon, multi-game platform güncellemesini canlıya alırken yapılması gerekenleri adım adım açıklar.

---

## 1️⃣ DNS Ayarları (ÇOK ÖNEMLİ!)

### Yapılması Gerekenler:

```
A Record ekle:
pubg.takimsistemi.com → Sunucu IP Adresi

Wildcard ekle (gelecekteki oyunlar için):
*.takimsistemi.com → Sunucu IP Adresi
```

### Test:
```bash
ping pubg.takimsistemi.com
# Sunucu IP'sini görmeli
```

**⏱️ Süre:** DNS yayılması 1-24 saat sürebilir!

---

## 2️⃣ SSL Sertifikası

### Wildcard SSL Gerekli:

```bash
# Let's Encrypt ile ücretsiz:
certbot certonly --manual --preferred-challenges=dns \
  -d takimsistemi.com -d *.takimsistemi.com
```

**Önemli:** Normal SSL yeterli değil, wildcard olmalı!

---

## 3️⃣ Web Server Konfigürasyonu

### Apache için:

```apache
ServerName takimsistemi.com
ServerAlias *.takimsistemi.com
```

### Nginx için:

```nginx
server_name takimsistemi.com *.takimsistemi.com;
```

**Test:**
```bash
# Apache
apachectl configtest

# Nginx
nginx -t
```

---

## 4️⃣ .env Dosyası Güncellemeleri

### Mutlaka Ekle/Güncelle:

```env
# Ana domain
APP_DOMAIN=takimsistemi.com

# Session (başında nokta olmalı!)
SESSION_DOMAIN=.takimsistemi.com
SESSION_PATH=/
SESSION_SAME_SITE=lax

# Sanctum
SANCTUM_STATEFUL_DOMAINS=takimsistemi.com,*.takimsistemi.com,localhost,127.0.0.1

# HTTPS için
APP_URL=https://takimsistemi.com
ASSET_URL=https://takimsistemi.com
```

---

## 5️⃣ Veritabanı Kontrolü

### Kontrol Et:

```sql
-- PUBG oyunu var mı?
SELECT * FROM games WHERE id = 1 AND slug = 'pubg';

-- Tüm tablolarda game_id var mı?
SHOW COLUMNS FROM tournaments LIKE 'game_id';
SHOW COLUMNS FROM clans LIKE 'game_id';
SHOW COLUMNS FROM lfg_posts LIKE 'game_id';
```

**Eğer yoksa:** Migration'ları çalıştır!

```bash
php artisan migrate
php artisan db:seed --class=GameSeeder
```

---

## 6️⃣ Cache Temizleme

### Canlıya Almadan Önce:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

---

## 7️⃣ Test (Local/Staging)

### hosts Dosyasına Ekle:

**Windows:** `C:\Windows\System32\drivers\etc\hosts`
**Linux/Mac:** `/etc/hosts`

```
127.0.0.1 takimsistemi.com
127.0.0.1 pubg.takimsistemi.com
```

### Test Senaryoları:

1. ✅ Ana sayfa açılıyor mu? → `http://takimsistemi.com`
2. ✅ Eski URL redirect ediyor mu? → `http://takimsistemi.com/turnuvalar`
3. ✅ Yeni URL çalışıyor mu? → `http://pubg.takimsistemi.com/turnuvalar`
4. ✅ Giriş yapılabiliyor mu?
5. ✅ Session korunuyor mu? (subdomain'ler arası)
6. ✅ Admin paneli çalışıyor mu?

---

## 8️⃣ Canlıya Alma Sırası

### Adım Adım:

1. **Veritabanı Yedeği Al**
   ```bash
   php artisan backup:run
   # veya
   mysqldump -u root -p pubg_community > backup_$(date +%Y%m%d).sql
   ```

2. **Kodu Canlıya Aktar**
   ```bash
   git pull origin main
   composer install --no-dev --optimize-autoloader
   ```

3. **Migration'ları Çalıştır**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=GameSeeder --force
   ```

4. **Cache'leri Temizle**
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Web Server'ı Yeniden Başlat**
   ```bash
   # Apache
   sudo systemctl restart apache2
   
   # Nginx
   sudo systemctl restart nginx
   sudo systemctl restart php8.2-fpm
   ```

6. **Test Et**
   - Ana sayfayı aç
   - Eski URL'leri test et
   - Giriş yap
   - Bir iki işlem yap

---

## 9️⃣ Monitoring

### İlk 24 Saat İzle:

```bash
# Error log'ları
tail -f storage/logs/laravel.log

# Web server log'ları
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log

# Redirect'leri izle
tail -f /var/log/apache2/access.log | grep "301"
```

---

## 🔟 SEO Ayarları

### Google Search Console:

1. Yeni subdomain ekle: `pubg.takimsistemi.com`
2. Sitemap'i güncelle ve gönder
3. URL inspection tool ile test et

### Sitemap Güncelleme:

```xml
<!-- Yeni URL'ler ekle -->
<url>
  <loc>https://pubg.takimsistemi.com/turnuvalar</loc>
  <changefreq>daily</changefreq>
  <priority>0.8</priority>
</url>
```

---

## ⚠️ Olası Sorunlar ve Çözümleri

### 1. "Session kayboluyor"

**Çözüm:**
```env
SESSION_DOMAIN=.takimsistemi.com  # Başında nokta olmalı!
```

### 2. "Redirect çalışmıyor"

**Kontrol:**
- DNS ayarları yapıldı mı?
- Web server konfigürasyonu doğru mu?
- Cache temizlendi mi?

### 3. "SSL hatası"

**Çözüm:**
- Wildcard SSL sertifikası kullan
- Mixed content hatası varsa APP_URL'i https yap

### 4. "404 Not Found"

**Kontrol:**
- Migration'lar çalıştı mı?
- GameSeeder çalıştı mı?
- Route cache temizlendi mi?

---

## 🔄 Rollback (Geri Alma)

### Acil Durum:

1. **Middleware'i Kapat:**
```php
// bootstrap/app.php içinde yorum satırı yap:
// $middleware->appendToGroup('web', \App\Http\Middleware\RedirectLegacyUrls::class);
```

2. **Cache Temizle:**
```bash
php artisan cache:clear
php artisan config:clear
```

3. **Veritabanını Geri Yükle:**
```bash
mysql -u root -p pubg_community < backup_YYYYMMDD.sql
```

---

## ✅ Final Checklist

Canlıya almadan önce tüm bunları kontrol et:

- [ ] DNS ayarları yapıldı ve test edildi
- [ ] SSL sertifikası kuruldu (wildcard)
- [ ] Web server konfigürasyonu yapıldı
- [ ] .env dosyası güncellendi
- [ ] Veritabanı yedeği alındı
- [ ] Migration'lar test edildi
- [ ] Local/Staging'de test edildi
- [ ] Cache temizlendi
- [ ] Monitoring hazır
- [ ] Rollback planı hazır
- [ ] Ekip bilgilendirildi

---

## 📞 Acil Durum İletişim

Sorun yaşarsanız:

1. **Log'ları kontrol et:** `storage/logs/laravel.log`
2. **Middleware'i kapat** (yukarıdaki rollback adımları)
3. **Veritabanını geri yükle**

---

## 📚 Detaylı Dokümantasyon

Daha fazla bilgi için:
- `BACKWARD-COMPATIBILITY-GUIDE.md` - Detaylı teknik dokümantasyon
- `.kiro/specs/multi-game-platform/design.md` - Mimari tasarım
- `.kiro/specs/multi-game-platform/requirements.md` - Gereksinimler

---

**Son Güncelleme:** 2 Aralık 2025
**Versiyon:** 1.0
**Durum:** Production Ready ✅
