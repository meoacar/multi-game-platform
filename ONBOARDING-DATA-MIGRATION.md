# Onboarding Data Migration Rehberi

## 📋 Genel Bakış

Bu dokümantasyon, onboarding sistemi eklendikten sonra **mevcut kullanıcılar** için yapılması gereken data migration işlemlerini açıklar.

## ⚠️ Önemli Notlar

- Bu migration **sadece bir kez** çalıştırılmalıdır
- Migration çalıştırılmadan önce **veritabanı yedeği** alınmalıdır
- Mevcut kullanıcılar onboarding sürecine **zorlanmayacak**
- Kullanıcılar isterlerse profil sayfasından bilgilerini tamamlayabilir

## 🚀 Adım Adım Kurulum

### 1. Migration'ı Çalıştır

Önce onboarding kolonlarını ekleyen migration'ı çalıştırın:

```bash
php artisan migrate
```

Bu komut `2025_12_01_000001_add_onboarding_columns_to_users_table.php` migration'ını çalıştırır ve users tablosuna şu kolonları ekler:

- `onboarding_completed` (boolean, default: false)
- `onboarding_step` (integer, default: 0)
- `profile_completion` (integer, default: 0)
- `pubg_id`, `player_level`, `player_tier`, `main_server`
- `favorite_mode`, `favorite_type`, `active_hours`
- `interests`, `push_enabled`

### 2. Mevcut Kullanıcıları Güncelle

Migration tamamlandıktan sonra, mevcut kullanıcıların onboarding durumunu güncelleyin:

```bash
php artisan db:seed --class=ExistingUsersOnboardingSeeder
```

Bu seeder şunları yapar:

✅ Tüm mevcut kullanıcıların `onboarding_completed` değerini `true` yapar
✅ `onboarding_step` değerini `4` (tamamlanmış) olarak ayarlar
✅ `profile_completion` değerini `50` (kısmi tamamlanma) olarak ayarlar

### 3. Sonuçları Kontrol Et

Seeder çalıştıktan sonra şu komutu çalıştırarak sonuçları kontrol edebilirsiniz:

```bash
php artisan tinker
```

Tinker'da:

```php
// Tüm kullanıcıların onboarding durumunu kontrol et
User::select('id', 'name', 'onboarding_completed', 'onboarding_step', 'profile_completion')->get();

// Onboarding tamamlamış kullanıcı sayısı
User::where('onboarding_completed', true)->count();

// Onboarding tamamlamamış kullanıcı sayısı (yeni kayıtlar)
User::where('onboarding_completed', false)->count();
```

## 📊 Beklenen Sonuçlar

### Mevcut Kullanıcılar İçin

- ✅ `onboarding_completed = true`
- ✅ `onboarding_step = 4`
- ✅ `profile_completion = 50`
- ✅ Dashboard ve diğer sayfalara erişebilir
- ✅ Onboarding sürecine zorlanmaz
- ✅ İsterlerse profil sayfasından bilgilerini tamamlayabilir

### Yeni Kullanıcılar İçin

- ⏳ `onboarding_completed = false`
- ⏳ `onboarding_step = 1`
- ⏳ `profile_completion = 0`
- ⏳ Kayıt sonrası onboarding sürecine yönlendirilir
- ⏳ 4 adımlı onboarding'i tamamlamalı veya atlayabilir

## 🎨 Kullanıcı Deneyimi

### Profil Sayfası

Mevcut kullanıcılar profil sayfalarında şu özellikleri görecek:

1. **Profil Tamamlanma Çubuğu**: Profil bilgilerinin ne kadarının dolu olduğunu gösterir
2. **"Profili Tamamla" Butonu**: Profile completion %100'den azsa görünür
3. **Profil Düzenleme Sayfası**: Tüm onboarding bilgilerini düzenleyebilir

### Profil Düzenleme

Kullanıcılar profil düzenleme sayfasında şu bilgileri ekleyebilir:

- 🎮 PUBG ID, seviye, tier, sunucu
- ⚙️ Favori mod, oyun tipi, aktif saatler
- 🎯 İlgi alanları (LFG, Klan, Turnuva, Sosyal)
- 🔔 Bildirim tercihleri

## 🔄 Rollback (Geri Alma)

Eğer bir sorun olursa migration'ı geri alabilirsiniz:

```bash
php artisan migrate:rollback --step=1
```

⚠️ **DİKKAT**: Bu komut onboarding kolonlarını tamamen siler!

## 📝 Notlar

- Seeder **idempotent**'tir (birden fazla çalıştırılabilir)
- Zaten `onboarding_completed = true` olan kullanıcıları tekrar güncellemez
- Yeni kayıt olan kullanıcılar otomatik olarak onboarding sürecine yönlendirilir
- Mevcut kullanıcılar isterlerse onboarding bilgilerini profil sayfasından ekleyebilir

## 🆘 Sorun Giderme

### Seeder Çalışmıyor

```bash
# Composer autoload'u yenile
composer dump-autoload

# Seeder'ı tekrar çalıştır
php artisan db:seed --class=ExistingUsersOnboardingSeeder
```

### Migration Hatası

```bash
# Migration durumunu kontrol et
php artisan migrate:status

# Eğer migration çalışmamışsa
php artisan migrate
```

### Kullanıcılar Hala Onboarding'e Yönlendiriliyor

```bash
# Kullanıcı durumunu kontrol et
php artisan tinker
User::find(USER_ID)->onboarding_completed; // true olmalı
```

## 📞 Destek

Herhangi bir sorun yaşarsanız:

1. `storage/logs/laravel.log` dosyasını kontrol edin
2. Veritabanı bağlantısını kontrol edin
3. Migration ve seeder dosyalarının doğru konumda olduğundan emin olun

---

**Son Güncelleme**: 1 Aralık 2025
**Versiyon**: 1.0.0
