# 🧪 Push Notification Test Kılavuzu

## Hızlı Test Adımları

### 1. Migration Çalıştır
```bash
php artisan migrate
```

**Beklenen Çıktı:**
```
Migrating: 2025_11_30_042132_add_fcm_token_to_users_table
Migrated:  2025_11_30_042132_add_fcm_token_to_users_table (XX.XXms)
```

---

### 2. Queue Worker Başlat
```bash
php artisan queue:work --queue=notifications
```

**Beklenen Çıktı:**
```
[2025-11-30 12:00:00] Processing: App\Jobs\SendPushNotificationJob
[2025-11-30 12:00:01] Processed:  App\Jobs\SendPushNotificationJob
```

---

### 3. .env Dosyasını Güncelle

```env
# Firebase Cloud Messaging (FCM)
FCM_SERVER_KEY=AAAAxxx...xxxxx
FCM_SENDER_ID=123456789012
```

**Not:** Firebase Console'dan Server Key ve Sender ID'yi al.

---

### 4. Tinker ile Test

```bash
php artisan tinker
```

#### Test 1: FCM Token Kaydet
```php
$user = User::first();
$user->update([
    'fcm_token' => 'test_fcm_token_12345',
    'device_type' => 'android',
    'fcm_token_updated_at' => now()
]);

echo "FCM Token kaydedildi: " . $user->fcm_token;
```

#### Test 2: Push Notification Gönder
```php
$fcmService = app(\App\Services\FcmService::class);

$result = $fcmService->sendToUser(
    $user,
    '🔔 Test Bildirimi',
    'Bu bir test bildirimidir!',
    ['type' => 'test', 'timestamp' => now()->toIso8601String()]
);

echo $result ? "Bildirim gönderildi!" : "Bildirim gönderilemedi!";
```

#### Test 3: Job Kuyruğa Ekle
```php
\App\Jobs\SendPushNotificationJob::dispatch(
    $user,
    '🎮 Yeni İlan!',
    'Senin için yeni bir takım ilanı var!',
    ['type' => 'lfg', 'id' => 123]
);

echo "Job kuyruğa eklendi!";
```

#### Test 4: Segment'e Gönder
```php
$count = $fcmService->sendToSegment(
    ['user_type' => 'active'],
    '⚡ Hızlı Maç!',
    'Şu an 50+ oyuncu online!',
    ['type' => 'quick_match']
);

echo "{$count} kullanıcıya bildirim gönderildi!";
```

---

### 5. API ile Test

#### Test 1: FCM Token Kaydet
```bash
curl -X POST http://localhost:8000/api/v1/fcm/token \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "fcm_token": "test_fcm_token_12345",
    "device_type": "android"
  }'
```

**Beklenen Response:**
```json
{
  "success": true,
  "message": "FCM token başarıyla kaydedildi",
  "data": {
    "fcm_token": "test_fcm_token_12345",
    "device_type": "android",
    "updated_at": "2025-11-30T12:00:00.000000Z"
  }
}
```

#### Test 2: Test Push Gönder
```bash
curl -X POST http://localhost:8000/api/v1/fcm/test \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Beklenen Response:**
```json
{
  "success": true,
  "message": "Test bildirimi gönderildi"
}
```

#### Test 3: Token Durumu Kontrol
```bash
curl -X GET http://localhost:8000/api/v1/fcm/status \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Beklenen Response:**
```json
{
  "success": true,
  "data": {
    "has_token": true,
    "device_type": "android",
    "updated_at": "2025-11-30T12:00:00.000000Z"
  }
}
```

---

### 6. Admin Panel ile Test

1. Admin paneline giriş yap: `/admin`
2. **Bildirimler > Toplu Bildirim Oluştur** sayfasına git
3. Formu doldur:
   - **Başlık:** Test Push Notification
   - **Mesaj:** Bu bir test bildirimidir!
   - **Tip:** Push
   - **Hedef Kitle:** Özel Seçim (kendi kullanıcını seç)
4. **Kaydet** butonuna tıkla
5. Bildirim detay sayfasında **Gönder** butonuna tıkla

**Beklenen Sonuç:**
- ✅ "Toplu bildirim başarıyla gönderildi! 🚀" mesajı
- ✅ İstatistiklerde gönderilen sayısı artmış olmalı

---

### 7. Log Kontrolü

```bash
tail -f storage/logs/laravel.log
```

**Başarılı Gönderim:**
```
[2025-11-30 12:00:00] local.INFO: Push notification başarıyla gönderildi {"user_id":1,"title":"Test Bildirimi"}
```

**Başarısız Gönderim:**
```
[2025-11-30 12:00:00] local.ERROR: Push notification gönderim hatası {"user_id":1,"error":"..."}
```

---

### 8. Veritabanı Kontrolü

```bash
php artisan tinker
```

```php
// FCM token'ı olan kullanıcılar
User::whereNotNull('fcm_token')->count();

// Son güncellenen token'lar
User::whereNotNull('fcm_token')
    ->orderBy('fcm_token_updated_at', 'desc')
    ->take(5)
    ->get(['id', 'name', 'device_type', 'fcm_token_updated_at']);

// Queue'daki job'lar
DB::table('jobs')->where('queue', 'notifications')->count();

// Başarısız job'lar
DB::table('failed_jobs')->count();
```

---

## ✅ Başarı Kriterleri

- [ ] Migration başarıyla çalıştı
- [ ] FCM token kaydedildi
- [ ] Queue worker çalışıyor
- [ ] Push notification job kuyruğa eklendi
- [ ] API endpoint'leri çalışıyor
- [ ] Admin panelden bildirim gönderilebiliyor
- [ ] Log dosyasında başarılı gönderim kaydı var
- [ ] Veritabanında fcm_token alanı dolu

---

## ❌ Sorun Giderme

### Problem: "FCM Server Key tanımlı değil"
**Çözüm:**
```bash
php artisan config:clear
php artisan cache:clear
```

### Problem: Queue job işlenmiyor
**Çözüm:**
```bash
# Queue worker'ı yeniden başlat
php artisan queue:restart
php artisan queue:work --queue=notifications
```

### Problem: Token kaydedilmiyor
**Çözüm:**
```bash
# Migration'ı kontrol et
php artisan migrate:status

# Gerekirse rollback ve tekrar migrate
php artisan migrate:rollback --step=1
php artisan migrate
```

---

## 📊 Performans Testi

### 100 Kullanıcıya Gönderim
```php
$users = User::whereNotNull('fcm_token')->take(100)->get();

$start = microtime(true);
$count = $fcmService->sendToUsers($users, 'Test', 'Mesaj');
$duration = microtime(true) - $start;

echo "100 kullanıcıya {$duration} saniyede gönderildi";
```

**Beklenen Süre:** < 5 saniye (queue ile)

---

## 🎉 Test Tamamlandı!

Tüm testler başarılıysa Push Notification sistemi hazır!

**Sonraki Adım:** Mobil uygulamada FCM entegrasyonu yap ve gerçek cihazda test et.
