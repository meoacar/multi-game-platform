# 🎮 Kullanıcı Onboarding Sistemi - Spec

## 📋 Genel Bakış
Yeni kayıt olan kullanıcılar için 4 adımlık hızlı onboarding süreci.

## 🎯 Hedef
- Profil tamamlama oranını artırmak
- Kullanıcıyı platforma hızlıca adapte etmek
- İlk etkileşimi sağlamak

## 📊 Adımlar

### Adım 1: Profil Bilgileri (Zorunlu)
- PUBG ID
- Oyuncu Seviyesi (1-100)
- Tier (Bronze, Silver, Gold, Platinum, Diamond, Crown, Ace, Conqueror)
- Ana Sunucu (Avrupa, Asya, Amerika)

### Adım 2: Oyun Tercihleri
- Favori Mod (TPP/FPP)
- Favori Oyun Tipi (Solo, Duo, Squad)
- Aktif Oyun Saatleri (Sabah, Öğlen, Akşam, Gece)
- Dil Tercihi (Türkçe, İngilizce)

### Adım 3: İlgi Alanları
- [ ] LFG - Takım Arkadaşı Bul
- [ ] Klan - Klan Ara/Oluştur
- [ ] Turnuva - Yarışmalara Katıl
- [ ] Sosyal - Toplulukla Etkileş

### Adım 4: Bildirimler & Tamamlama
- Push notification izni
- Email bildirimleri (opsiyonel)
- Hoşgeldin mesajı
- İlk rozet: "Hoşgeldin Çaylağı" 🎖️

## 🗄️ Veritabanı Değişiklikleri

### users tablosuna eklenecek kolonlar:
```sql
- onboarding_completed (boolean, default: false)
- onboarding_step (integer, default: 0)
- profile_completion (integer, default: 0) // 0-100 arası
- pubg_id (string, nullable)
- player_level (integer, nullable)
- player_tier (string, nullable)
- main_server (string, nullable)
- favorite_mode (string, nullable) // TPP/FPP
- favorite_type (string, nullable) // Solo/Duo/Squad
- active_hours (json, nullable) // ["morning", "evening"]
- interests (json, nullable) // ["lfg", "clan", "tournament"]
- push_enabled (boolean, default: false)
```

## 📁 Dosya Yapısı

```
app/Http/Controllers/
└── OnboardingController.php

app/Models/
└── User.php (güncellenecek)

resources/views/onboarding/
├── layout.blade.php
├── step1.blade.php (profil)
├── step2.blade.php (tercihler)
├── step3.blade.php (ilgi alanları)
└── step4.blade.php (tamamlama)

routes/web.php
└── onboarding rotaları

database/migrations/
└── xxxx_add_onboarding_fields_to_users_table.php
```

## 🎨 UI/UX Özellikleri

- ✅ Progress bar (1/4, 2/4, 3/4, 4/4)
- ✅ "Atla" butonu (her adımda)
- ✅ "Geri" butonu (2. adımdan itibaren)
- ✅ Otomatik kaydetme
- ✅ Mobil responsive
- ✅ PUBG temalı renkler ve ikonlar
- ✅ Smooth geçişler

## 🔄 Akış

```
Kayıt Tamamlandı
    ↓
Onboarding Başlat?
    ↓
Adım 1 → Adım 2 → Adım 3 → Adım 4
    ↓
Dashboard'a Yönlendir
    ↓
Hoşgeldin Popup (rozet göster)
```

## 🎁 Gamification

- **İlk Rozet**: "Hoşgeldin Çaylağı" (onboarding tamamlama)
- **Profil Tamamlama**: %0 → %100 göstergesi
- **İlk XP**: +50 XP (profil tamamlama bonusu)

## 🚀 Geliştirme Sırası

1. Migration oluştur (onboarding kolonları)
2. OnboardingController oluştur
3. View dosyaları oluştur (4 adım + layout)
4. Route tanımlamaları
5. User model güncelle
6. Kayıt sonrası yönlendirme ekle
7. Test et

## ⚙️ Teknik Detaylar

### Controller Metodları:
```php
- index() // Mevcut adımı göster
- step1() // Profil bilgileri
- step2() // Oyun tercihleri
- step3() // İlgi alanları
- step4() // Tamamlama
- skip() // Onboarding'i atla
- complete() // Onboarding'i tamamla
```

### Middleware:
```php
- CheckOnboarding // Onboarding tamamlanmamışsa yönlendir
```

## 📝 Notlar

- Onboarding opsiyonel (atlanabilir)
- Atlanırsa profil %50 tamamlanmış sayılır
- Daha sonra profil sayfasından tamamlanabilir
- Her adım ayrı ayrı kaydedilir (kullanıcı çıksa bile kaldığı yerden devam eder)

## ✅ Kabul Kriterleri

- [ ] Yeni kullanıcı kayıt olduktan sonra onboarding'e yönlendirilir
- [ ] 4 adım sorunsuz çalışır
- [ ] "Atla" butonu çalışır
- [ ] Progress bar doğru gösterir
- [ ] Veriler doğru kaydedilir
- [ ] Mobil uyumlu
- [ ] Tamamlama sonrası rozet verilir
- [ ] Dashboard'a yönlendirme yapılır

---

**Tahmini Süre**: 4-6 saat
**Öncelik**: Orta
**Bağımlılıklar**: User authentication sistemi
