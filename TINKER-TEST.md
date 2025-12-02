# Tinker Test Komutları

## Tinker'ı Başlat
```bash
php artisan tinker
```

## Test 1: Oyunları Listele
```php
use App\Models\Game;
Game::all();
Game::active()->get();
```

## Test 2: Kullanıcıları Listele
```php
use App\Models\User;
User::all();
User::where('is_admin', true)->first();
```

## Test 3: İlişkileri Test Et

### User -> Profile İlişkisi
```php
$user = User::first();
$user->profile;
$user->profile->nickname;
$user->profile->rank;
```

### User -> Device İlişkisi
```php
$user = User::first();
$user->device;
$user->device->device_name;
$user->device->sensitivity_settings;
```

### Profile -> User İlişkisi (Ters)
```php
use App\Models\Profile;
$profile = Profile::first();
$profile->user;
$profile->user->name;
```

### Device -> User İlişkisi (Ters)
```php
use App\Models\Device;
$device = Device::first();
$device->user;
$device->user->email;
```

## Test 4: Helper Metodları Test Et

### User Helper Metodları
```php
$admin = User::where('email', 'admin@pubgcommunity.com')->first();
$admin->isAdmin(); // true
$admin->isActive(); // true
$admin->isBanned(); // false
```

### Profile Helper Metodları
```php
$profile = Profile::first();
$profile->getAvatarUrlAttribute();
$profile->incrementViews();
$profile->profile_views;
```

### Device Helper Metodları
```php
$device = Device::first();
$device->getSensitivity('general');
$device->getSensitivity('ads');
$device->getFormattedSensitivityAttribute();
```

## Test 5: Scope'ları Test Et

### Game Scope'ları
```php
Game::active()->ordered()->get();
```

## Test 6: Eager Loading Test Et
```php
// N+1 problemi olmadan
$users = User::with(['profile', 'device'])->get();
foreach($users as $user) {
    echo $user->name . ' - ' . $user->profile->nickname . "\n";
}
```

## Test 7: Admin Kullanıcı ile Giriş Simülasyonu
```php
$admin = User::where('email', 'admin@pubgcommunity.com')->first();
$admin->updateLastLogin();
$admin->last_login_at;
```

## Test 8: Hassasiyet Ayarlarını Güncelle
```php
$device = Device::first();
$device->setSensitivity('general', 95);
$device->getSensitivity('general');
```

## Test 9: Profil Tamamlanma Kontrolü
```php
$profile = Profile::first();
$profile->checkCompletion();
$profile->is_profile_completed;
```

## Test 10: Tüm İlişkileri Birlikte Test Et
```php
$user = User::with(['profile', 'device'])->first();
echo "Kullanıcı: " . $user->name . "\n";
echo "Nick: " . $user->profile->nickname . "\n";
echo "Rank: " . $user->profile->rank . "\n";
echo "Cihaz: " . $user->device->device_name . "\n";
echo "FPS: " . $user->device->fps_setting . "\n";
echo "Admin: " . ($user->isAdmin() ? 'Evet' : 'Hayır') . "\n";
```
