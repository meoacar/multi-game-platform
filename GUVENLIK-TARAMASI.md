# 🔒 Güvenlik Tarama Sistemi

## Genel Bakış

PUBG Mobile Topluluk Platformu için kapsamlı güvenlik tarama sistemi. SQL Injection, XSS ve CSRF güvenlik açıklarını otomatik olarak tespit eder.

## Özellikler

### 1. SQL Injection Taraması
- ✅ DB::raw() kullanımlarını kontrol eder
- ✅ whereRaw() içinde parametre binding kontrolü
- ✅ DB::statement() güvenlik kontrolü
- ✅ Raw query'lerde değişken kullanımı tespiti

### 2. XSS (Cross-Site Scripting) Taraması
- ✅ Blade template'lerde {!! !!} kullanımı tespiti
- ✅ JavaScript içinde PHP değişken kullanımı kontrolü
- ✅ Doğrudan echo kullanımı tespiti
- ✅ Request verisi doğrudan kullanımı kontrolü

### 3. CSRF (Cross-Site Request Forgery) Taraması
- ✅ POST/PUT/DELETE route'larında middleware kontrolü
- ✅ Form'larda @csrf token kontrolü
- ✅ AJAX isteklerinde token kontrolü

## Kullanım

### CLI Komutu

```bash
# Tam tarama
php artisan security:scan

# Sadece SQL Injection
php artisan security:scan --type=sql

# Sadece XSS
php artisan security:scan --type=xss

# Sadece CSRF
php artisan security:scan --type=csrf

# JSON formatında çıktı
php artisan security:scan --json
```

### Admin Panel

Admin panelinden güvenlik taraması yapmak için:

1. Admin paneline giriş yapın
2. Sistem > Güvenlik Taraması menüsüne gidin
3. İstediğiniz tarama türünü seçin veya "Tam Tarama Başlat" butonuna tıklayın
4. Sonuçları inceleyin ve önerileri uygulayın

**URL:** `/admin/security`

## Tarama Sonuçları

### Durum Seviyeleri

- 🚨 **KRİTİK**: Acil müdahale gerektiren güvenlik açıkları
- ⚠️ **UYARI**: Potansiyel güvenlik riskleri
- ✅ **GÜVENLİ**: Güvenlik açığı bulunamadı

### Örnek Çıktı

```
📊 TARAMA ÖZETİ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Durum: 🚨 KRİTİK
Toplam Güvenlik Açığı: 59
Kritik: 10
Uyarı: 49
```

## Güvenlik Önerileri

### SQL Injection Koruması
1. Her zaman Eloquent ORM veya Query Builder kullanın
2. Raw query kullanırken mutlaka parametre binding kullanın
3. Kullanıcı girdilerini asla doğrudan SQL sorgusuna eklemeyin
4. Input validation ve sanitization yapın
5. Prepared statements kullanın

### XSS Koruması
1. Blade template'lerde `{{ }}` kullanın (otomatik escape)
2. `{!! !!}` kullanımından kaçının, gerekirse htmlspecialchars() kullanın
3. Kullanıcı girdilerini her zaman validate ve sanitize edin
4. Content Security Policy (CSP) header'ları ekleyin
5. JavaScript'e veri aktarırken @json() kullanın

### CSRF Koruması
1. Tüm state-changing route'lara "web" middleware ekleyin
2. Form'larda @csrf direktifi kullanın
3. AJAX isteklerinde X-CSRF-TOKEN header'ı gönderin
4. API route'larında Sanctum token authentication kullanın
5. SameSite cookie ayarlarını yapılandırın

### Genel Güvenlik
1. HTTPS kullanın (production'da zorunlu)
2. Güvenlik header'ları ekleyin (X-Frame-Options, X-Content-Type-Options, vb.)
3. Rate limiting uygulayın
4. Input validation her zaman backend'de yapın
5. Hassas bilgileri loglamayın
6. Düzenli güvenlik güncellemeleri yapın
7. Error reporting'i production'da kapatın

## Teknik Detaylar

### Dosya Yapısı

```
app/
├── Services/
│   └── SecurityScanService.php      # Ana tarama servisi
├── Console/Commands/
│   └── SecurityScan.php              # CLI komutu
└── Http/Controllers/Admin/
    └── SecurityController.php        # Admin panel controller

resources/views/admin/security/
└── index.blade.php                   # Admin panel view

routes/
└── admin-security.php                # Güvenlik route'ları
```

### API Endpoint'leri

```php
// Güvenlik tarama sayfası
GET /admin/security

// Güvenlik taraması çalıştır
POST /admin/security/scan?type={sql|xss|csrf|all}

// Güvenlik önerileri
GET /admin/security/recommendations
```

### Tarama Algoritması

1. **SQL Injection**: Regex pattern matching ile raw query kullanımlarını tespit eder
2. **XSS**: Blade dosyalarında escape edilmemiş değişken kullanımlarını bulur
3. **CSRF**: Route'ları ve form'ları analiz ederek token kontrolü yapar

## Performans

- Ortalama tarama süresi: 2-5 saniye
- Taranan dosya sayısı: ~200+ dosya
- Bellek kullanımı: ~50MB

## Güvenlik Notları

⚠️ **Önemli**: Bu tarama aracı otomatik tespit yapar ancak %100 doğruluk garantisi vermez. Manuel kod incelemesi de yapılmalıdır.

⚠️ **Dikkat**: Tarama sonuçları hassas bilgi içerebilir. Sadece yetkili personel erişebilmelidir.

## Gelecek Geliştirmeler

- [ ] Otomatik düzeltme önerileri
- [ ] Zamanlanmış taramalar
- [ ] Email bildirimleri
- [ ] Detaylı raporlama (PDF export)
- [ ] Güvenlik skoru hesaplama
- [ ] Tarihsel trend analizi

## Lisans

Bu güvenlik tarama sistemi PUBG Mobile Topluluk Platformu'nun bir parçasıdır.

---

**Son Güncelleme**: 29 Kasım 2024
**Versiyon**: 1.0.0
