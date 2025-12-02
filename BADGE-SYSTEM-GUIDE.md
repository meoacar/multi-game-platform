# 🏆 Başarı Sistemi (Badge/Achievement System) - Kullanım Kılavuzu

## 📋 Genel Bakış

PUBG Mobile Topluluk Platformu için kapsamlı bir başarı/rozet sistemi geliştirilmiştir. Sistem, kullanıcıların platform içindeki aktivitelerini ödüllendirmek ve motivasyonu artırmak için tasarlanmıştır.

## 🎯 Özellikler

### ✅ Temel Özellikler
- **52 Adet Rozet**: 6 farklı kategoride çeşitli başarı rozetleri
- **4 Nadirlik Seviyesi**: Common, Rare, Epic, Legendary
- **Gizli Rozetler**: Sürpriz rozetler (is_hidden)
- **Progress Tracking**: Kullanıcı ilerlemesi takibi
- **Otomatik Kilitleme**: Event-driven otomatik rozet açma
- **Admin Yönetimi**: Tam CRUD + istatistikler
- **API Desteği**: Mobil uygulama için hazır

### 📊 Rozet Kategorileri

#### 1. 🎮 Oyun (Gameplay) - 6 Rozet
- Ace Oyuncu
- Conqueror Efsanesi
- TPP Uzmanı
- FPP Ustası
- Solo Savaşçı
- Squad Lideri

#### 2. 👥 Sosyal (Social) - 6 Rozet
- İlk Arkadaşını Buldu
- Sosyal Kelebek (50+ arkadaş)
- Popüler Oyuncu (100+ arkadaş)
- Mesaj Makinesi (1000+ mesaj)
- Yorum Ustası (100+ yorum)
- Beğeni Kralı (500+ beğeni)

#### 3. 📝 İçerik (Content) - 10 Rozet
- İlk İlanını Açtı
- Aktif İlan Sahibi (25+ ilan)
- Klan Kurucusu
- Klan Lideri (25+ üye)
- Rehber Yazarı
- Üretken Yazar (10+ rehber)
- Popüler Rehber Yazarı (1000+ görüntülenme)
- Cihaz Uzmanı
- Takım Kurucusu
- Etkinlik Organizatörü

#### 4. ⭐ Özel (Special) - 5 Rozet
- Topluluğa İlk Adım
- Beta Kullanıcısı (ilk 100 üye) 🔒
- Kurucu Üye (ilk 1000 üye)
- Efsane Katkıcı 🔒
- Topluluk Kahramanı 🔒

#### 5. 🔥 Aktivite (Activity) - 10 Rozet
- 7 Günlük Seri
- 30 Günlük Seri
- 100 Günlük Seri
- Sabah Kuşu (sabah 6-9 arası 30 giriş) 🔒
- Gece Baykuşu (gece 00-03 arası 30 giriş) 🔒
- Hafta Sonu Savaşçısı
- Aktif Oyuncu (300 XP)
- Topluluk Çekirdeği (600 XP)
- Efsane Oyuncu (1500 XP)
- Turnuva Şampiyonu

#### 6. 🛡️ Moderasyon (Moderation) - 3 Rozet
- İlk Rapor
- Topluluk Koruyucusu (10+ doğru rapor)
- Yardım Meleği (50+ yardımcı yanıt)

🔒 = Gizli rozet (kullanıcılar göremez)

## 🔧 Teknik Detaylar

### Database Schema

#### badges tablosu
```sql
- id
- name (string)
- slug (string, unique)
- description (text)
- icon (string) - emoji veya icon class
- category (enum: gameplay, social, content, special, activity, moderation)
- rarity (enum: common, rare, epic, legendary)
- is_hidden (boolean) - Gizli rozet mi?
- is_active (boolean) - Aktif mi?
- sort_order (integer) - Sıralama
- xp_required (integer, nullable) - Gereken XP
- timestamps
```

#### user_badges pivot tablosu
```sql
- user_id
- badge_id
- unlocked_at (timestamp, nullable) - Açılma tarihi
- progress (integer) - Mevcut ilerleme
- progress_max (integer, nullable) - Hedef ilerleme
- timestamps
```

### BadgeService Metodları

```php
// Tüm rozetleri kontrol et ve kilidi aç
$badgeService->checkAndUnlockAllBadges($user);

// Belirli rozetleri kilitle
$badgeService->unlockBadges($user, ['first-step', 'first-friend']);

// Tek rozet kilitle
$badgeService->unlockBadge($user, 'first-lfg');

// Progress güncelle
$badgeService->updateBadgeProgress($user, 'social-butterfly', 45, 50);

// Kullanıcı istatistikleri
$stats = $badgeService->getUserBadgeStats($user);
// Returns: ['total' => 52, 'unlocked' => 10, 'in_progress' => 5, 'locked' => 37, 'completion_percentage' => 19.23]

// Kategoriye göre rozetler
$badges = $badgeService->getBadgesByCategory('social');

// Nadirliğe göre rozetler
$badges = $badgeService->getBadgesByRarity('legendary');
```

## 🌐 API Endpoints

### Public Endpoints

```
GET /api/v1/badges
- Tüm aktif rozetleri listele
- Query params: ?category=social&rarity=epic

GET /api/v1/badges/category/{category}
- Kategoriye göre rozetler

GET /api/v1/badges/rarity/{rarity}
- Nadirliğe göre rozetler

GET /api/v1/badges/{slug}
- Rozet detayı + kaç kullanıcı açmış
```

### Protected Endpoints (Auth Required)

```
GET /api/v1/me/badges
- Kullanıcının rozetleri
- Returns: {unlocked: [], in_progress: [], stats: {}}
```

## 🔐 Admin Panel

### Routes

```
GET  /admin/badges              - Rozet listesi (filtreleme, arama)
GET  /admin/badges/stats        - İstatistikler
GET  /admin/badges/create       - Yeni rozet formu
POST /admin/badges              - Rozet kaydet
GET  /admin/badges/{id}/edit    - Rozet düzenle
PUT  /admin/badges/{id}         - Rozet güncelle
DELETE /admin/badges/{id}       - Rozet sil
POST /admin/badges/{id}/toggle-active - Aktif/Pasif

POST /admin/badges/assign-to-user    - Kullanıcıya rozet ver
POST /admin/badges/remove-from-user  - Kullanıcıdan rozet al
```

### Admin Özellikleri

- ✅ Filtreleme (kategori, nadirlik, durum)
- ✅ Arama (isim, slug, açıklama)
- ✅ CRUD işlemleri
- ✅ Aktif/Pasif toggle
- ✅ Kullanıcıya manuel rozet verme/alma
- ✅ İstatistikler:
  - Toplam/Aktif/Gizli rozet sayıları
  - Kategoriye göre dağılım
  - Nadirliğe göre dağılım
  - En çok açılan rozetler (Top 10)
  - En az açılan rozetler (Top 10)

## 🎨 Frontend Kullanımı

### Blade Template'de Rozet Gösterimi

```blade
@foreach($user->badges as $badge)
<div class="badge-item">
    <span class="badge-icon">{{ $badge->icon }}</span>
    <div class="badge-info">
        <h4>{{ $badge->name }}</h4>
        <p>{{ $badge->description }}</p>
        <span class="badge-rarity {{ $badge->rarity }}">
            {{ ucfirst($badge->rarity) }}
        </span>
    </div>
</div>
@endforeach
```

### Progress Bar Örneği

```blade
@if($badge->pivot->progress_max)
<div class="progress-bar">
    <div class="progress" style="width: {{ ($badge->pivot->progress / $badge->pivot->progress_max) * 100 }}%"></div>
    <span>{{ $badge->pivot->progress }} / {{ $badge->pivot->progress_max }}</span>
</div>
@endif
```

## 🔄 Event-Driven Rozet Açma

Sistem, kullanıcı aktivitelerine göre otomatik rozet açar. Event listener'lar:

```php
// ProfileCompleted event
event(new ProfileCompleted($user, $profile));
// → "Topluluğa İlk Adım" rozeti açılır

// LfgPostCreated event
event(new LfgPostCreated($lfgPost, $user));
// → "İlk İlanını Açtı" rozeti açılır

// FriendRequestAccepted event
event(new FriendRequestAccepted($friendship, $user1, $user2));
// → "İlk Arkadaşını Buldu" rozeti açılır
```

## 📝 Yeni Rozet Ekleme

### 1. Seeder'a Ekle

```php
// database/seeders/BadgeSeeder.php
[
    'name' => 'Yeni Rozet',
    'slug' => 'new-badge',
    'description' => 'Açıklama',
    'icon' => '🎯',
    'category' => 'special',
    'rarity' => 'rare',
    'is_hidden' => false,
    'is_active' => true,
    'sort_order' => 100,
    'xp_required' => 50,
]
```

### 2. BadgeService'e Kontrol Ekle

```php
// app/Services/BadgeService.php
public function checkAndUnlockAllBadges(User $user): array
{
    // ... mevcut kontroller
    
    // Yeni rozet kontrolü
    if ($this->checkNewBadgeCondition($user)) {
        $unlockedBadges[] = 'new-badge';
    }
    
    return $this->unlockBadges($user, $unlockedBadges);
}

private function checkNewBadgeCondition(User $user): bool
{
    // Koşul kontrolü
    return $user->someCondition === true;
}
```

### 3. Event Listener Ekle (Opsiyonel)

```php
// app/Listeners/CheckNewBadge.php
public function handle($event)
{
    $badgeService = app(BadgeService::class);
    $badgeService->unlockBadge($event->user, 'new-badge');
}
```

## 🧪 Test Etme

```bash
# Seeder'ı çalıştır
php artisan db:seed --class=BadgeSeeder

# Tinker ile test
php artisan tinker

$user = User::first();
$badgeService = app(\App\Services\BadgeService::class);

# Tüm rozetleri kontrol et
$unlocked = $badgeService->checkAndUnlockAllBadges($user);

# Kullanıcının rozetleri
$user->badges;

# İstatistikler
$badgeService->getUserBadgeStats($user);
```

## 📊 İstatistikler

Admin panelinde şu istatistikler görüntülenir:

- **Genel**: Toplam, Aktif, Gizli rozet sayıları
- **Kategori Dağılımı**: Her kategoride kaç rozet var
- **Nadirlik Dağılımı**: Her nadirlik seviyesinde kaç rozet var
- **En Çok Açılan**: Hangi rozetler en popüler
- **En Az Açılan**: Hangi rozetler en nadir

## 🎯 Gelecek Geliştirmeler

- [ ] Rozet koleksiyonu showcase (profil sayfası)
- [ ] Rozet leaderboard (en çok rozete sahip kullanıcılar)
- [ ] Rozet bildirimleri (yeni rozet açıldığında)
- [ ] Rozet paylaşımı (sosyal medya)
- [ ] Sezon rozetleri (özel etkinlikler)
- [ ] Rozet kombinasyonları (set bonusları)
- [ ] Rozet marketplace (gelecekte)

## 📞 Destek

Sorularınız için:
- GitHub Issues
- Discord: #badge-system
- Email: support@pubgcommunity.com

---

**Son Güncelleme**: 1 Aralık 2024
**Versiyon**: 1.0.0
**Geliştirici**: PUBG Community Team
