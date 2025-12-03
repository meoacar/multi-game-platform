# Backward Compatibility (Geriye Uyumluluk) Rehberi

## 📋 Genel Bakış

Multi-game platform güncellemesi ile birlikte URL yapısı değişti:
- **Eski Format:** `takimsistemi.com/turnuvalar`
- **Yeni Format:** `pubg.takimsistemi.com/turnuvalar`

Bu rehber, eski URL'lerin yeni formata otomatik yönlendirilmesini sağlayan sistemi açıklar.

## 🔧 Nasıl Çalışır?

### RedirectLegacyUrls Middleware

`app/Http/Middleware/RedirectLegacyUrls.php` middleware'i:

1. Gelen isteğin subdomain'ini kontrol eder
2. Eğer subdomain yoksa (eski format), game-specific bir route mu diye kontrol eder
3. Game-specific route ise, default game (PUBG) subdomain'ine 301 redirect yapar
4. Query string'leri ve HTTPS/HTTP protokolünü korur

### Redirect Edilen Route'lar

Aşağıdaki route'lar otomatik olarak redirect edilir:

```
/ilanlar          → pubg.takimsistemi.com/ilanlar
/klanlar          → pubg.takimsistemi.com/klanlar
/rehber           → pubg.takimsistemi.com/rehber
/topluluk         → pubg.takimsistemi.com/topluluk
/cihazlar         → pubg.takimsistemi.com/cihazlar
/liderlik-tablosu → pubg.takimsistemi.com/liderlik-tablosu
/rozetler         → pubg.takimsistemi.com/rozetler
/xp-gecmisi       → pubg.takimsistemi.com/xp-gecmisi
/takimlar         → pubg.takimsistemi.com/takimlar
/turnuvalar       → pubg.takimsistemi.com/turnuvalar
/arkadaslar       → pubg.takimsistemi.com/arkadaslar
/arkadas-istekleri → pubg.takimsistemi.com/arkadas-istekleri
/mesajlar         → pubg.takimsistemi.com/mesajlar
/eslesme          → pubg.takimsistemi.com/eslesme
/profil           → pubg.takimsistemi.com/profil
/profilim         → pubg.takimsistemi.com/profilim
/ayarlar          → pubg.takimsistemi.com/ayarlar
/bildirimler      → pubg.takimsistemi.com/bildirimler
/notifications    → pubg.takimsistemi.com/notifications
```

### Redirect Edilmeyen Route'lar

Aşağıdaki route'lar redirect edilmez (normal çalışır):

- Ana sayfa: `/`
- Auth route'ları: `/giris`, `/kayit`, `/sifremi-unuttum`
- API route'ları: `/api/*`
- Admin route'ları: `/admin/*`
- Statik sayfalar: `/sayfa/*`
- SEO route'ları: `/sitemap.xml`, `/robots.txt`

## 🚀 Canlıya Alma Adımları

### 1. DNS Ayarları

**ÖNEMLİ:** Subdomain'lerin çalışması için DNS ayarları yapılmalı!

```
A Record:
pubg.takimsistemi.com → Sunucu IP Adresi

Wildcard (Gelecekteki oyunlar için):
*.takimsistemi.com → Sunucu IP Adresi
```

### 2. Web Server Konfigürasyonu

#### Apache (.htaccess veya VirtualHost)

```apache
<VirtualHost *:80>
    ServerName takimsistemi.com
    ServerAlias *.takimsistemi.com
    
    DocumentRoot /var/www/html/pubg-community/public
    
    <Directory /var/www/html/pubg-community/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:443>
    ServerName takimsistemi.com
    ServerAlias *.takimsistemi.com
    
    DocumentRoot /var/www/html/pubg-community/public
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
    
    <Directory /var/www/html/pubg-community/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    listen 443 ssl;
    
    server_name takimsistemi.com *.takimsistemi.com;
    
    root /var/www/html/pubg-community/public;
    index index.php;
    
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
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

### 3. .env Dosyası Ayarları

```env
# Ana domain (subdomain olmadan)
APP_DOMAIN=takimsistemi.com

# Session ayarları (wildcard subdomain için)
SESSION_DOMAIN=.takimsistemi.com
SESSION_PATH=/
SESSION_SAME_SITE=lax

# Sanctum ayarları (API için)
SANCTUM_STATEFUL_DOMAINS=takimsistemi.com,*.takimsistemi.com,localhost,127.0.0.1
```

### 4. SSL Sertifikası

**Wildcard SSL sertifikası** almanız önerilir:
- `*.takimsistemi.com` için geçerli olmalı
- Let's Encrypt ile ücretsiz alınabilir:

```bash
certbot certonly --manual --preferred-challenges=dns \
  -d takimsistemi.com -d *.takimsistemi.com
```

### 5. Cache Temizleme

Canlıya almadan önce tüm cache'leri temizleyin:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 6. Test Etme

Canlıya almadan önce test edin:

```bash
# Local hosts dosyasına ekle (Windows: C:\Windows\System32\drivers\etc\hosts)
127.0.0.1 takimsistemi.com
127.0.0.1 pubg.takimsistemi.com

# Tarayıcıda test et:
http://takimsistemi.com/turnuvalar
# → http://pubg.takimsistemi.com/turnuvalar yönlendirmeli
```

## ⚠️ Dikkat Edilmesi Gerekenler

### 1. SEO Etkisi

- **301 Permanent Redirect** kullanılıyor (SEO dostu)
- Google'ın yeni URL'leri indexlemesi 2-4 hafta sürebilir
- Google Search Console'da yeni subdomain'i ekleyin
- Sitemap'i güncelleyin ve yeniden gönderin

### 2. Mevcut Kullanıcılar

- Kullanıcılar eski URL'leri kullanmaya devam edebilir
- Otomatik olarak yeni URL'e yönlendirilecekler
- Session'lar korunur (cross-subdomain session)
- Giriş yapmış kullanıcılar tekrar giriş yapmak zorunda kalmaz

### 3. Harici Linkler

- Eski URL'lere verilen linkler çalışmaya devam eder
- Sosyal medya paylaşımları etkilenmez
- Email'lerdeki linkler çalışır

### 4. Performans

- Redirect işlemi çok hızlıdır (< 10ms)
- Default game bilgisi cache'lenir (1 saat)
- Subdomain'li isteklerde redirect yapılmaz

## 🔍 Sorun Giderme

### Redirect Çalışmıyor

**Kontrol Listesi:**
1. ✅ DNS ayarları yapıldı mı?
2. ✅ Web server konfigürasyonu doğru mu?
3. ✅ .env dosyasında APP_DOMAIN doğru mu?
4. ✅ Cache temizlendi mi?
5. ✅ Middleware aktif mi? (`bootstrap/app.php`)

**Debug:**
```bash
# Log'ları kontrol et
tail -f storage/logs/laravel.log

# Route'ları kontrol et
php artisan route:list | grep -i redirect
```

### Session Sorunları

Eğer kullanıcılar subdomain'ler arası geçişte session kaybediyorsa:

```env
# .env dosyasında kontrol et:
SESSION_DOMAIN=.takimsistemi.com  # Başında nokta olmalı!
SESSION_PATH=/
SESSION_SAME_SITE=lax
```

### SSL Sorunları

Mixed content hatası alıyorsanız:

```env
# .env dosyasında:
APP_URL=https://takimsistemi.com
ASSET_URL=https://takimsistemi.com
```

## 📊 Monitoring

Redirect'lerin çalışıp çalışmadığını izlemek için:

```bash
# Access log'ları kontrol et
tail -f /var/log/apache2/access.log | grep "301"
tail -f /var/log/nginx/access.log | grep "301"

# Laravel log'ları
tail -f storage/logs/laravel.log | grep "RedirectLegacyUrls"
```

## 🔄 Rollback Planı

Eğer bir sorun çıkarsa geri almak için:

1. **Middleware'i devre dışı bırak:**
```php
// bootstrap/app.php içinde yorum satırı yap:
// $middleware->appendToGroup('web', \App\Http\Middleware\RedirectLegacyUrls::class);
```

2. **Cache temizle:**
```bash
php artisan cache:clear
php artisan config:clear
```

3. **Eski URL yapısına dön:**
```php
// routes/web.php - Subdomain routing'i kaldır
```

## 📞 Destek

Sorun yaşarsanız:
1. `storage/logs/laravel.log` dosyasını kontrol edin
2. Web server error log'larını kontrol edin
3. Browser console'da hata var mı kontrol edin

## ✅ Checklist (Canlıya Alma Öncesi)

- [ ] DNS ayarları yapıldı
- [ ] Web server konfigürasyonu yapıldı
- [ ] SSL sertifikası kuruldu (wildcard)
- [ ] .env dosyası güncellendi
- [ ] Cache temizlendi
- [ ] Local'de test edildi
- [ ] Staging'de test edildi
- [ ] Google Search Console'a yeni subdomain eklendi
- [ ] Sitemap güncellendi
- [ ] Monitoring kuruldu
- [ ] Rollback planı hazır

## 📝 Notlar

- Bu özellik **Requirements 20.1 ve 20.2**'yi karşılar
- Middleware sırası önemli: RedirectLegacyUrls → DetectGame
- Default game olarak PUBG (game_id=1) kullanılır
- Gelecekte yeni oyunlar eklendiğinde middleware güncellenmesine gerek yok
