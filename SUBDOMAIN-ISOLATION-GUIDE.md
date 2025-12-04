# 🎮 Subdomain İzolasyon Sistemi

## 📋 Genel Bakış

Her subdomain (pubg.squadbul.com, valorant.squadbul.com vb.) kendi içeriğine sahiptir:
- Klanlar
- LFG İlanları
- Rehberler
- Turnuvalar
- Topluluk Gönderileri
- Yorumlar
- **Profiller** (Her kullanıcı her oyun için farklı profile sahip)

## 🔧 Teknik Yapı

### 1. Migration Dosyaları

**Hazırlanan Migration'lar:**
- `2025_12_04_000001_add_game_id_to_profiles_table.php` - Profillere game_id ekleme
- `2025_12_04_000002_update_guide_posts_game_id_not_null.php` - Rehberleri oyuna özel yapma
- `2025_12_04_000003_add_game_id_to_comments_table.php` - Yorumlara game_id ekleme

**⚠️ ÖNEMLİ:** Migration'ları çalıştırmadan önce veritabanı yedeği al!

### 2. Global Scope Sistemi

**Dosyalar:**
- `app/Models/Scopes/GameScope.php` - Otomatik filtreleme scope'u
- `app/Models/Traits/HasGameScope.php` - Model trait'i

**Nasıl Çalışır:**
```php
// Otomatik olarak mevcut oyuna göre filtreler
$clans = Clan::all(); // Sadece mevcut oyunun klanları

// Tüm oyunları görmek için
$allClans = Clan::withoutGameScope()->get();

// Belirli bir oyun için
$pubgClans = Clan::forGame(1)->get();
```

### 3. Middleware

**DetectGame Middleware** (Güncellendi)
- Subdomain'den oyunu algılar
- Session'a `game_id` kaydeder
- Config'e `app.current_game_id` kaydeder (Global Scope için)
- View'lara `$currentGame` değişkenini paylaşır

### 4. Model Güncellemeleri

**HasGameScope Trait Kullanan Model'ler:**
- ✅ Clan
- ✅ LfgPost
- ✅ GuidePost
- ✅ CommunityPost
- ✅ Tournament
- ✅ Comment
- ✅ Profile (YENİ!)

**User Model:**
```php
// Mevcut oyun için profil
$user->profile; 

// Tüm profiller
$user->profiles;

// Belirli oyun için profil
$user->profileForGame(2); // Valorant
```

## 📊 Veritabanı Yapısı

### Profiles Tablosu
```
- user_id (FK)
- game_id (FK) - YENİ!
- nickname
- rank
- ... diğer alanlar

UNIQUE KEY: (user_id, game_id)
```

**Örnek:**
```
user_id | game_id | nickname    | rank
--------|---------|-------------|--------
1       | 1       | ProPlayer   | Diamond
1       | 2       | ProPlayer   | Gold
2       | 1       | GamerX      | Platinum
```

## 🚀 Kullanım Örnekleri

### Controller'da
```php
// Otomatik olarak mevcut oyuna göre filtrelenir
public function index()
{
    $clans = Clan::with('leader')->paginate(20);
    return view('clans.index', compact('clans'));
}

// Tüm oyunlar için (admin paneli)
public function adminIndex()
{
    $clans = Clan::withoutGameScope()
        ->with('game', 'leader')
        ->paginate(20);
    return view('admin.clans.index', compact('clans'));
}
```

### Yeni Kayıt Oluştururken
```php
// game_id otomatik olarak eklenir
$clan = Clan::create([
    'name' => 'Elite Squad',
    'description' => '...',
    // game_id otomatik eklenir (session'dan)
]);
```

### Profil Oluşturma
```php
// Her oyun için ayrı profil
$user->profiles()->create([
    'game_id' => 1, // PUBG
    'nickname' => 'ProPlayer',
    'rank' => 'Diamond',
]);

$user->profiles()->create([
    'game_id' => 2, // Valorant
    'nickname' => 'ProPlayer',
    'rank' => 'Gold',
]);
```

## 🔍 Test Senaryoları

### 1. Subdomain İzolasyonu
```bash
# PUBG subdomain
https://pubg.squadbul.com/clans
# Sadece PUBG klanlarını gösterir

# Valorant subdomain
https://valorant.squadbul.com/clans
# Sadece Valorant klanlarını gösterir
```

### 2. Profil İzolasyonu
```php
// PUBG subdomain'de
$user->profile; // PUBG profili

// Valorant subdomain'de
$user->profile; // Valorant profili
```

### 3. Ana Sayfa
```bash
# Ana domain
https://squadbul.com
# Tüm oyunların genel bilgileri
# game_id = null
```

## ⚠️ Dikkat Edilmesi Gerekenler

1. **Migration Sırası:** Migration'ları sırayla çalıştır
2. **Veritabanı Yedeği:** Mutlaka yedek al
3. **Test:** Önce test ortamında dene
4. **Cache:** Migration'dan sonra cache'i temizle
5. **Mevcut Veriler:** Migration'lar mevcut verileri game_id=1 (PUBG) ile güncelleyecek

## 🎯 Sonraki Adımlar

1. ✅ Migration dosyalarını çalıştır
2. ✅ Cache'i temizle
3. ✅ Test et (her subdomain'de)
4. ✅ Controller'ları kontrol et
5. ✅ View'ları kontrol et
6. ✅ API endpoint'lerini test et

## 📝 Migration Çalıştırma

```bash
# Yedek al
php artisan backup:database

# Migration'ları çalıştır
php artisan migrate

# Cache temizle
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 🐛 Sorun Giderme

### Profil Bulunamıyor
```php
// Eğer kullanıcının o oyun için profili yoksa
if (!$user->profile) {
    // Yeni profil oluştur
    $user->profiles()->create([
        'game_id' => session('game_id'),
        'nickname' => $user->name,
    ]);
}
```

### Tüm Oyunları Görmek
```php
// Global scope'u kaldır
Model::withoutGameScope()->get();
```

### Belirli Oyun İçin
```php
// Belirli oyun için filtrele
Model::forGame($gameId)->get();
```
