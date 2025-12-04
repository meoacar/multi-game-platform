# 🎮 Oyuna Özel Onboarding Sistemi

## 📋 Genel Bakış

Her oyun için ayrı onboarding süreci:
- Kullanıcı kayıt olur
- Ana sayfada oyun seçer
- Seçtiği oyun için onboarding başlar
- Her oyun için farklı sorular ve profil bilgileri

## 🔄 Yeni Akış

### 1. Kayıt Sonrası
```
Kayıt → Email Doğrulama → Ana Sayfa (squadbul.com)
```

### 2. Ana Sayfa (Oyun Seçimi)
```
squadbul.com
- PUBG Mobile [Oyna]
- Valorant [Oyna]
- League of Legends [Oyna]
- CS2 [Oyna]
```

### 3. Oyun Seçimi
```
Kullanıcı "PUBG Mobile" seçer
→ pubg.squadbul.com'a yönlendirilir
→ Profil yoksa onboarding başlar
```

### 4. Onboarding (Oyuna Özel)
```
PUBG Mobile Onboarding:
Step 1: Oyuncu Bilgileri (Nickname, PUBG ID)
Step 2: Rütbe ve Sunucu (Rank, Server Region)
Step 3: Oyun Tercihleri (Favori Haritalar, Mod)
Step 4: Kişisel Bilgiler (Şehir, Yaş, Oyun Tarzı)

Valorant Onboarding:
Step 1: Oyuncu Bilgileri (Riot ID, Tag)
Step 2: Rank ve Rol (Rank, Main Role)
Step 3: Agent Tercihleri (Main Agents)
Step 4: Kişisel Bilgiler (Şehir, Yaş, Oyun Tarzı)
```

### 5. Çoklu Oyun Desteği
```
Kullanıcı valorant.squadbul.com'a gider
→ "Valorant için profilin yok, oluşturalım mı?"
→ Valorant onboarding başlar
```

## 🗄️ Veritabanı Değişiklikleri

### Migration: `move_onboarding_to_profiles`

**Kaldırılan (users tablosundan):**
- onboarding_completed
- onboarding_step
- profile_completion
- pubg_id
- player_level
- player_tier
- main_server
- favorite_mode
- favorite_type
- active_hours
- interests

**Eklenen (profiles tablosuna):**
- onboarding_completed (boolean)
- onboarding_step (integer)

## 🔧 Kod Değişiklikleri

### 1. CheckOnboarding Middleware
```php
// Artık profile göre kontrol eder
$profile = $user->profileForGame(session('game_id'))->first();

if (!$profile) {
    // Profil yok → Onboarding'e yönlendir
    return redirect()->route('onboarding.start');
}

if (!$profile->onboarding_completed) {
    // Onboarding tamamlanmamış
    return redirect()->route('onboarding.step', $profile->onboarding_step);
}
```

### 2. Profile Model
```php
// Yeni metodlar
$profile->updateOnboardingStep(2);
$profile->completeOnboarding();
$profile->hasCompletedOnboarding(); // boolean
```

### 3. User Model
```php
// Kaldırılan metodlar
// hasCompletedOnboarding() - Artık Profile'da
// updateOnboardingStep() - Artık Profile'da
// getProfileCompletionAttribute() - Kaldırıldı
```

### 4. OnboardingController
```php
// Yeni route'lar
GET /onboarding/start - Onboarding başlat
GET /onboarding/step/{step} - Belirli adım

// Oyuna göre view seçimi
onboarding.games.pubg-mobile.step1
onboarding.games.valorant.step1
// Yoksa genel: onboarding.step1
```

### 5. GameSelectionController (YENİ)
```php
GET / - Ana sayfa (oyun seçimi)
GET /play/{slug} - Oyun seç ve subdomain'e yönlendir
```

## 📁 View Yapısı

```
resources/views/
├── game-selection/
│   └── index.blade.php (Ana sayfa - oyun seçimi)
├── onboarding/
│   ├── games/
│   │   ├── pubg-mobile/
│   │   │   ├── step1.blade.php
│   │   │   ├── step2.blade.php
│   │   │   ├── step3.blade.php
│   │   │   └── step4.blade.php
│   │   ├── valorant/
│   │   │   ├── step1.blade.php
│   │   │   ├── step2.blade.php
│   │   │   ├── step3.blade.php
│   │   │   └── step4.blade.php
│   │   └── ...
│   ├── step1.blade.php (Genel - fallback)
│   ├── step2.blade.php
│   ├── step3.blade.php
│   └── step4.blade.php
```

## 🎯 Kullanım Senaryoları

### Senaryo 1: Yeni Kullanıcı
```
1. Kayıt olur
2. squadbul.com'da oyun seçer (PUBG)
3. pubg.squadbul.com'a yönlendirilir
4. Onboarding başlar (4 adım)
5. Tamamlar → Ana sayfaya gider
```

### Senaryo 2: Mevcut Kullanıcı, Yeni Oyun
```
1. PUBG'de aktif kullanıcı
2. valorant.squadbul.com'a gider
3. "Valorant için profilin yok" mesajı
4. Valorant onboarding başlar
5. Tamamlar → Valorant ana sayfasına gider
```

### Senaryo 3: Çoklu Oyun Kullanıcısı
```
1. PUBG ve Valorant profili var
2. İstediği subdomain'e gidebilir
3. Her oyunda farklı profil bilgileri
4. Onboarding tamamlanmış
```

## 🚀 Route'lar

### Ana Domain (squadbul.com)
```php
GET / → GameSelectionController@index (Oyun seçimi)
GET /play/{slug} → GameSelectionController@select (Subdomain'e yönlendir)
```

### Subdomain (pubg.squadbul.com)
```php
GET /onboarding/start → OnboardingController@start
GET /onboarding/step/{step} → OnboardingController@step
POST /onboarding/step/{step} → OnboardingController@saveStep
```

## ✅ Tamamlanan İşler

1. ✅ Migration oluşturuldu ve çalıştırıldı
2. ✅ User model'den onboarding kaldırıldı
3. ✅ Profile model'e onboarding eklendi
4. ✅ CheckOnboarding middleware güncellendi
5. ✅ GameSelectionController oluşturuldu
6. ✅ OnboardingController güncellendi
7. ✅ Route'lar eklendi

## 📝 Yapılması Gerekenler

1. ⏳ Ana sayfa view'ı oluştur (game-selection/index.blade.php)
2. ⏳ Oyuna özel onboarding view'ları oluştur
3. ⏳ OnboardingService'i güncelle (profile bazlı)
4. ⏳ Form Request'leri güncelle
5. ⏳ Mevcut onboarding view'larını güncelle

## 🎨 Örnek View (Ana Sayfa)

```blade
<!-- resources/views/game-selection/index.blade.php -->
<div class="game-selection">
    <h1>Hangi Oyunu Oynamak İstersin?</h1>
    
    <div class="games-grid">
        @foreach($games as $game)
        <div class="game-card">
            <img src="{{ $game->logo_url }}" alt="{{ $game->name }}">
            <h3>{{ $game->name }}</h3>
            <p>{{ $game->description }}</p>
            
            @auth
                @if($game->has_profile)
                    <a href="{{ route('game.select', $game->slug) }}" class="btn btn-success">
                        Devam Et
                    </a>
                @else
                    <a href="{{ route('game.select', $game->slug) }}" class="btn btn-primary">
                        Başla
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">
                    Giriş Yap
                </a>
            @endauth
        </div>
        @endforeach
    </div>
</div>
```

## 🔍 Test Senaryoları

1. **Yeni kullanıcı kaydı**
   - Kayıt ol
   - Ana sayfada oyun seç
   - Onboarding tamamla

2. **Çoklu oyun profili**
   - PUBG profili oluştur
   - Valorant subdomain'ine git
   - Valorant profili oluştur

3. **Onboarding yarıda bırakma**
   - Onboarding başlat
   - 2. adımda çık
   - Tekrar gir → 2. adımdan devam et
