# 🔔 Push Notification Kurulum Kılavuzu

## Firebase Cloud Messaging (FCM) Entegrasyonu

Bu kılavuz, PUBG Mobile Topluluk Platformu için Firebase Cloud Messaging (FCM) push notification sisteminin kurulumunu açıklar.

---

## 📋 İçindekiler

1. [Firebase Projesi Oluşturma](#1-firebase-projesi-oluşturma)
2. [FCM Server Key Alma](#2-fcm-server-key-alma)
3. [Laravel Konfigürasyonu](#3-laravel-konfigürasyonu)
4. [Migration Çalıştırma](#4-migration-çalıştırma)
5. [Mobil Uygulama Entegrasyonu](#5-mobil-uygulama-entegrasyonu)
6. [Test Etme](#6-test-etme)
7. [Kullanım Örnekleri](#7-kullanım-örnekleri)

---

## 1. Firebase Projesi Oluşturma

### Adım 1: Firebase Console'a Git
- [Firebase Console](https://console.firebase.google.com/) adresine git
- Google hesabınla giriş yap

### Adım 2: Yeni Proje Oluştur
1. "Add project" butonuna tıkla
2. Proje adı gir: `PUBG Mobile Community`
3. Google Analytics'i aktif et (opsiyonel)
4. "Create project" butonuna tıkla

### Adım 3: Android/iOS Uygulaması Ekle
1. Project Overview sayfasında Android/iOS ikonuna tıkla
2. Package name/Bundle ID gir
3. `google-services.json` (Android) veya `GoogleService-Info.plist` (iOS) dosyasını indir
4. Mobil uygulamana ekle

---

## 2. FCM Server Key Alma

### Adım 1: Project Settings'e Git
1. Firebase Console'da sol üstteki ⚙️ (Settings) ikonuna tıkla
2. "Project settings" seçeneğine tıkla

### Adım 2: Cloud Messaging Sekmesi
1. Üstteki "Cloud Messaging" sekmesine tıkla
2. "Cloud Messaging API (Legacy)" bölümünü bul
3. **Server key** değerini kopyala

### Adım 3: Sender ID'yi Al
- Aynı sayfada **Sender ID** değerini de kopyala

---

## 3. Laravel Konfigürasyonu

### Adım 1: .env Dosyasını Güncelle
`.env` dosyasına şu satırları ekle:

```env
# Firebase Cloud Messaging (FCM)
FCM_SERVER_KEY=your_server_key_here
FCM_SENDER_ID=your_sender_id_here
```

**Örnek:**
```env
FCM_SERVER_KEY=AAAAxxx...xxxxx
FCM_SENDER_ID=123456789012
```


---

## 4. Migration Çalıştırma

### FCM Token Alanlarını Ekle

```bash
php artisan migrate
```

Bu migration `users` tablosuna şu alanları ekler:
- `fcm_token` (string, 500) - FCM device token
- `device_type` (string, 20) - android, ios, web
- `fcm_token_updated_at` (timestamp) - Token güncellenme tarihi

---

## 5. Mobil Uygulama Entegrasyonu

### Android (Flutter)

#### 1. Firebase SDK Kurulumu
```yaml
# pubspec.yaml
dependencies:
  firebase_core: ^2.24.0
  firebase_messaging: ^14.7.0
```

#### 2. Firebase Başlatma
```dart
// main.dart
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  
  // FCM token al
  String? token = await FirebaseMessaging.instance.getToken();
  print('FCM Token: $token');
  
  runApp(MyApp());
}
```

#### 3. Token'ı Backend'e Gönder
```dart
Future<void> registerFcmToken() async {
  String? token = await FirebaseMessaging.instance.getToken();
  
  if (token != null) {
    final response = await http.post(
      Uri.parse('https://your-api.com/api/v1/fcm/token'),
      headers: {
        'Authorization': 'Bearer $userToken',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'fcm_token': token,
        'device_type': 'android', // veya 'ios'
      }),
    );
    
    if (response.statusCode == 200) {
      print('FCM token kaydedildi');
    }
  }
}
```

#### 4. Bildirim Dinleme
```dart
// Foreground bildirimleri
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  print('Bildirim alındı: ${message.notification?.title}');
  
  // Bildirim göster
  showNotification(message);
});

// Background/Terminated bildirimleri
FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
  print('Bildirime tıklandı: ${message.data}');
  
  // Sayfaya yönlendir
  navigateToPage(message.data);
});
```

### iOS (Flutter)

#### 1. Bildirim İzni İste
```dart
FirebaseMessaging messaging = FirebaseMessaging.instance;

NotificationSettings settings = await messaging.requestPermission(
  alert: true,
  badge: true,
  sound: true,
);

if (settings.authorizationStatus == AuthorizationStatus.authorized) {
  print('Bildirim izni verildi');
}
```

#### 2. APNs Token Yapılandırması
```dart
// iOS için APNs token gerekli
String? apnsToken = await FirebaseMessaging.instance.getAPNSToken();
print('APNs Token: $apnsToken');
```

---

## 6. Test Etme

### 1. API ile Test

#### FCM Token Kaydet
```bash
curl -X POST https://your-api.com/api/v1/fcm/token \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "fcm_token": "your_device_fcm_token",
    "device_type": "android"
  }'
```

#### Test Bildirimi Gönder
```bash
curl -X POST https://your-api.com/api/v1/fcm/test \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. Admin Panel ile Test

1. Admin paneline giriş yap
2. **Bildirimler > Toplu Bildirim Oluştur** sayfasına git
3. Bildirim tipini **Push** seç
4. Hedef kitleyi seç (kendin için "Özel Seçim")
5. Başlık ve mesaj yaz
6. **Gönder** butonuna tıkla

### 3. Tinker ile Test

```bash
php artisan tinker
```

```php
// Kullanıcıya push notification gönder
$user = User::find(1);
$fcmService = app(\App\Services\FcmService::class);

$fcmService->sendToUser(
    $user,
    '🎮 Yeni İlan!',
    'Senin için yeni bir takım ilanı var!',
    ['type' => 'lfg', 'id' => 123]
);
```

---

## 7. Kullanım Örnekleri

### Örnek 1: Tek Kullanıcıya Gönder

```php
use App\Services\FcmService;

$fcmService = app(FcmService::class);

$fcmService->sendToUser(
    $user,
    'Hoş Geldin!',
    'Platformumuza hoş geldin, profil tamamla!',
    ['type' => 'welcome']
);
```

### Örnek 2: Birden Fazla Kullanıcıya Gönder

```php
$users = User::whereNotNull('fcm_token')->take(10)->get();

$fcmService->sendToUsers(
    $users,
    '🎉 Yeni Özellik!',
    'Turnuva sistemi artık aktif!',
    ['type' => 'announcement']
);
```

### Örnek 3: Segment'e Göre Gönder

```php
$fcmService->sendToSegment(
    [
        'user_type' => 'active',
        'device_type' => 'android',
    ],
    '⚡ Hızlı Maç!',
    'Şu an 50+ oyuncu online, hemen katıl!',
    ['type' => 'quick_match']
);
```

### Örnek 4: Job ile Asenkron Gönder

```php
use App\Jobs\SendPushNotificationJob;

SendPushNotificationJob::dispatch(
    $user,
    'Başvurun Kabul Edildi!',
    'Klan başvurun kabul edildi, tebrikler!',
    ['type' => 'clan_accepted', 'clan_id' => 5]
);
```


---

## 8. API Endpoint'leri

### FCM Token Kaydet
```
POST /api/v1/fcm/token
Authorization: Bearer {token}

Body:
{
  "fcm_token": "string (max 500)",
  "device_type": "android|ios|web"
}

Response:
{
  "success": true,
  "message": "FCM token başarıyla kaydedildi",
  "data": {
    "fcm_token": "...",
    "device_type": "android",
    "updated_at": "2025-11-30T12:00:00Z"
  }
}
```

### FCM Token Sil
```
DELETE /api/v1/fcm/token
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "FCM token başarıyla silindi"
}
```

### Test Bildirimi Gönder
```
POST /api/v1/fcm/test
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "Test bildirimi gönderildi"
}
```

### FCM Token Durumu
```
GET /api/v1/fcm/status
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "has_token": true,
    "device_type": "android",
    "updated_at": "2025-11-30T12:00:00Z"
  }
}
```

---

## 9. Sorun Giderme

### Problem: "FCM Server Key tanımlı değil" hatası
**Çözüm:** `.env` dosyasında `FCM_SERVER_KEY` değerini kontrol et. Cache'i temizle:
```bash
php artisan config:clear
php artisan cache:clear
```

### Problem: Bildirim gönderilmiyor
**Çözüm:**
1. Queue worker'ın çalıştığından emin ol: `php artisan queue:work`
2. FCM token'ın doğru kaydedildiğini kontrol et
3. Firebase Console'da Cloud Messaging API'nin aktif olduğunu kontrol et
4. Log dosyalarını kontrol et: `storage/logs/laravel.log`

### Problem: Token geçersiz hatası
**Çözüm:** Kullanıcı uygulamayı silip yeniden yüklediğinde token değişir. Mobil uygulamada token yenilendiğinde backend'e tekrar gönder.

### Problem: iOS'ta bildirim gelmiyor
**Çözüm:**
1. APNs sertifikasının Firebase'e yüklendiğinden emin ol
2. iOS cihazda bildirim izinlerinin verildiğini kontrol et
3. Production/Development APNs sertifikasını doğru kullandığından emin ol

---

## 10. Güvenlik Notları

### ⚠️ Önemli Güvenlik Kuralları

1. **FCM Server Key'i Gizli Tut**
   - `.env` dosyasını asla Git'e commit etme
   - Server key'i frontend'de kullanma
   - Sadece backend'de kullan

2. **Token Doğrulama**
   - FCM token'ları sadece authenticated kullanıcılar kaydedebilir
   - Token'lar kullanıcıya özel, başkasının token'ını kaydedemez

3. **Rate Limiting**
   - API endpoint'lerine rate limiting ekle
   - Spam bildirimleri önle

4. **Token Temizleme**
   - Geçersiz token'ları otomatik temizle
   - 90 gün kullanılmayan token'ları sil

---

## 11. Performans İpuçları

### Queue Kullanımı
Push notification gönderimi asenkron olmalı:
```php
// ❌ Yanlış (senkron)
$fcmService->sendToUser($user, 'Başlık', 'Mesaj');

// ✅ Doğru (asenkron)
SendPushNotificationJob::dispatch($user, 'Başlık', 'Mesaj');
```

### Toplu Gönderim
Binlerce kullanıcıya gönderirken batch işleme kullan:
```php
User::whereNotNull('fcm_token')
    ->chunk(100, function ($users) use ($fcmService) {
        $fcmService->sendToUsers($users, 'Başlık', 'Mesaj');
    });
```

### Cache Kullanımı
Sık kullanılan verileri cache'le:
```php
$activeUsers = Cache::remember('active_users_with_fcm', 300, function () {
    return User::whereNotNull('fcm_token')
        ->where('last_login_at', '>=', now()->subDays(7))
        ->count();
});
```

---

## 12. Gelişmiş Özellikler

### Bildirim Kategorileri
```php
$fcmService->sendToUser(
    $user,
    'Yeni Mesaj',
    'Ahmet sana mesaj gönderdi',
    [
        'type' => 'message',
        'category' => 'social',
        'priority' => 'high',
        'sound' => 'message.mp3',
    ]
);
```

### Görsel Bildirimler
```php
$fcmService->sendToUser(
    $user,
    'Yeni İlan',
    'Senin için yeni bir takım ilanı var!',
    ['type' => 'lfg', 'id' => 123],
    'https://example.com/images/lfg-banner.jpg' // image
);
```

### Deep Linking
```php
$fcmService->sendToUser(
    $user,
    'Klan Daveti',
    'Seni klana davet etti!',
    ['type' => 'clan_invite', 'clan_id' => 5],
    null,
    'app://clan/5' // click_action
);
```

---

## 13. Monitoring ve Analytics

### Firebase Analytics Entegrasyonu
Firebase Console'da bildirim istatistiklerini görüntüle:
- Gönderilen bildirim sayısı
- Açılma oranı (open rate)
- Tıklama oranı (click rate)
- Cihaz dağılımı

### Laravel Logging
```php
Log::channel('fcm')->info('Push notification gönderildi', [
    'user_id' => $user->id,
    'title' => $title,
    'sent_at' => now(),
]);
```

---

## 14. Sonraki Adımlar

- [ ] Firebase Analytics entegrasyonu
- [ ] Bildirim kategorileri (social, system, marketing)
- [ ] Kullanıcı bildirim tercihleri (ayarlar sayfası)
- [ ] Bildirim geçmişi (admin panel)
- [ ] A/B testing (farklı başlık/mesaj testleri)
- [ ] Scheduled notifications (zamanlanmış bildirimler)
- [ ] Rich notifications (resim, buton, vb.)

---

## 📞 Destek

Sorun yaşarsan:
1. `storage/logs/laravel.log` dosyasını kontrol et
2. Firebase Console'da Cloud Messaging sekmesini kontrol et
3. Queue worker'ın çalıştığından emin ol: `php artisan queue:work`

---

**Push Notification Sistemi Hazır! 🎉**

Artık kullanıcılara gerçek zamanlı bildirimler gönderebilirsin!
