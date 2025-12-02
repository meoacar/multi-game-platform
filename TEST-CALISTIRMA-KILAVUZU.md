# Test Çalıştırma Kılavuzu

## 🚀 Hızlı Başlangıç

### 1. Test Veritabanını Hazırla

Test'ler otomatik olarak `RefreshDatabase` trait'i kullanarak her test öncesi veritabanını temizler ve migration'ları çalıştırır.

**Önemli**: `.env.testing` dosyası oluşturun (opsiyonel):

```env
APP_ENV=testing
DB_CONNECTION=mysql
DB_DATABASE=pubg_community_test
```

### 2. Tüm Testleri Çalıştır

```bash
cd pubg-community
php artisan test
```

### 3. Belirli Test Gruplarını Çalıştır

#### Sadece Feature Tests
```bash
php artisan test --testsuite=Feature
```

#### Sadece Unit Tests
```bash
php artisan test --testsuite=Unit
```

### 4. Belirli Test Dosyalarını Çalıştır

#### Auth Testleri
```bash
php artisan test tests/Feature/AuthTest.php
```

#### Profile Testleri
```bash
php artisan test tests/Feature/ProfileTest.php
```

#### LFG Testleri
```bash
php artisan test tests/Feature/LfgTest.php
```

#### Clan Testleri
```bash
php artisan test tests/Feature/ClanTest.php
```

### 5. Detaylı Çıktı ile Çalıştır

```bash
php artisan test --verbose
```

### 6. Parallel Test Çalıştırma (Hızlı)

```bash
php artisan test --parallel
```

---

## 📊 Test Yapısı

```
tests/
├── Feature/              # API ve Web endpoint testleri
│   ├── AuthTest.php
│   ├── ProfileTest.php
│   ├── LfgTest.php
│   ├── DeviceTest.php
│   ├── ClanTest.php
│   ├── GameTest.php
│   ├── MiddlewareTest.php
│   ├── WebHomeTest.php
│   ├── WebLfgTest.php
│   └── WebClanTest.php
│
├── Unit/                 # Model ve Service testleri
│   ├── UserModelTest.php
│   ├── ProfileModelTest.php
│   ├── LfgPostModelTest.php
│   ├── ClanModelTest.php
│   ├── AuthServiceTest.php
│   └── ProfileServiceTest.php
│
└── TestCase.php
```

---

## ✅ Test Checklist

### Testleri Çalıştırmadan Önce

- [ ] Composer bağımlılıkları yüklü (`composer install`)
- [ ] `.env` dosyası yapılandırılmış
- [ ] Veritabanı bağlantısı çalışıyor
- [ ] Migration'lar hazır
- [ ] Factory'ler oluşturulmuş

### İlk Test Çalıştırması

```bash
# 1. Composer bağımlılıklarını kontrol et
composer install

# 2. Test veritabanını oluştur (opsiyonel)
php artisan db:create pubg_community_test

# 3. Testleri çalıştır
php artisan test
```

---

## 🎯 Beklenen Sonuçlar

### Başarılı Test Çıktısı

```
PASS  Tests\Feature\AuthTest
✓ kullanici kayit olabilir
✓ kayit icin gecerli email gereklidir
✓ kayit icin sifre onay gereklidir
✓ kullanici giris yapabilir
✓ yanlis sifre ile giris yapilamaz
✓ kullanici cikis yapabilir
✓ banli kullanici giris yapamaz

PASS  Tests\Feature\ProfileTest
✓ kullanici kendi profilini gorebilir
✓ kullanici profilini guncelleyebilir
✓ misafir kullanici profil guncelleyemez
✓ profil guncelleme icin nickname gereklidir

... (diğer testler)

Tests:    80 passed
Duration: 18s
```

---

## 🐛 Hata Durumunda

### Test Başarısız Olursa

1. **Hata mesajını oku**: PHPUnit detaylı hata mesajı verir
2. **Stack trace'i incele**: Hangi satırda hata olduğunu gösterir
3. **Veritabanını kontrol et**: Migration'lar çalışmış mı?
4. **Factory'leri kontrol et**: Gerekli factory'ler var mı?

### Yaygın Hatalar ve Çözümleri

#### 1. Database Connection Error
```
SQLSTATE[HY000] [1049] Unknown database
```

**Çözüm**: Test veritabanını oluştur
```bash
mysql -u root -p
CREATE DATABASE pubg_community_test;
```

#### 2. Class Not Found
```
Class 'Database\Factories\ProfileFactory' not found
```

**Çözüm**: Factory dosyasını oluştur veya kontrol et

#### 3. Migration Error
```
SQLSTATE[42S02]: Base table or view not found
```

**Çözüm**: Migration'ları kontrol et
```bash
php artisan migrate:fresh
```

#### 4. Token Mismatch
```
Unauthenticated
```

**Çözüm**: Test'te `actingAs($user, 'sanctum')` kullanıldığından emin ol

---

## 📈 Test Coverage Raporu

### Coverage Oluştur (PHPUnit)

```bash
php artisan test --coverage
```

### Detaylı HTML Coverage Raporu

```bash
php artisan test --coverage-html coverage
```

Rapor `coverage/index.html` dosyasında oluşturulur.

---

## 🔧 Test Konfigürasyonu

### phpunit.xml

Test konfigürasyonu `phpunit.xml` dosyasında:

```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    <testsuite name="Feature">
        <directory>tests/Feature</directory>
    </testsuite>
</testsuites>
```

### Test Environment

Test'ler otomatik olarak `.env.testing` dosyasını kullanır (varsa).

---

## 💡 Test Yazma İpuçları

### 1. Test İsimlendirme

```php
/** @test */
public function kullanici_kayit_olabilir()
{
    // Test kodu
}
```

### 2. AAA Pattern

```php
// Arrange (Hazırlık)
$user = User::factory()->create();

// Act (İşlem)
$response = $this->actingAs($user)->getJson('/api/v1/me');

// Assert (Doğrulama)
$response->assertStatus(200);
```

### 3. Factory Kullanımı

```php
// Tek kayıt
$user = User::factory()->create();

// Çoklu kayıt
$users = User::factory()->count(5)->create();

// Özel değerlerle
$user = User::factory()->create(['status' => 'banned']);
```

### 4. Database Assertions

```php
// Kayıt var mı?
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);

// Kayıt yok mu?
$this->assertDatabaseMissing('users', ['email' => 'deleted@example.com']);

// Soft delete
$this->assertSoftDeleted('users', ['id' => $userId]);
```

---

## 🎨 Test Best Practices

1. **Her test bağımsız olmalı**: Testler birbirini etkilememeli
2. **RefreshDatabase kullan**: Her test temiz veritabanı ile başlamalı
3. **Factory kullan**: Hard-coded değerler yerine factory kullan
4. **Meaningful assertions**: Doğru assertion'ları kullan
5. **Test isimleri açıklayıcı olmalı**: Ne test edildiği anlaşılmalı

---

## 📝 Test Sonuçlarını Kaydet

### Test Raporunu Dosyaya Yaz

```bash
php artisan test > test-results.txt
```

### JSON Format

```bash
php artisan test --log-junit test-results.xml
```

---

## 🚦 CI/CD Entegrasyonu

### GitHub Actions Örneği

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

---

## 📚 Ek Kaynaklar

- [Laravel Testing Docs](https://laravel.com/docs/11.x/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Factories](https://laravel.com/docs/11.x/eloquent-factories)
- [HTTP Tests](https://laravel.com/docs/11.x/http-tests)

---

**Son Güncelleme**: 23 Kasım 2025
**Test Durumu**: ✅ 80+ test hazır
