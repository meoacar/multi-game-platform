# Oyuna Özel Tema Sistemi

Bu klasör, çoklu oyun platformu için oyuna özel tema CSS dosyalarını içerir.

## Nasıl Çalışır?

1. **DetectGame Middleware**: Her istekte subdomain'den oyunu algılar ve session'a kaydeder
2. **game-theme Component**: Session'daki oyun bilgisine göre ilgili tema CSS'ini dinamik olarak yükler
3. **CSS Variables**: Her oyun için özel renk paleti ve stil değişkenleri tanımlanır

## Tema Dosyası Oluşturma

Yeni bir oyun için tema oluşturmak için:

1. Bu klasörde `{oyun-slug}.css` dosyası oluşturun (örn: `cod.css`, `valorant.css`)
2. Aşağıdaki CSS variable'ları tanımlayın:

```css
:root {
    /* Ana Tema Renkleri */
    --game-primary: #YOUR_PRIMARY_COLOR;
    --game-primary-dark: #YOUR_PRIMARY_DARK;
    --game-primary-light: #YOUR_PRIMARY_LIGHT;
    
    --game-secondary: #YOUR_SECONDARY_COLOR;
    --game-secondary-dark: #YOUR_SECONDARY_DARK;
    --game-secondary-light: #YOUR_SECONDARY_LIGHT;
    
    /* Diğer renkler... */
}
```

3. Dosyayı `public/css/themes/` klasörüne kopyalayın

## Kullanılabilir CSS Classes

### Butonlar
- `.btn-game-primary` - Ana tema rengi ile gradient buton
- `.btn-game-secondary` - İkincil tema rengi ile gradient buton
- `.btn-game-outline` - Outline stil buton

### Kartlar
- `.card-game` - Standart oyun kartı
- `.card-game-featured` - Öne çıkan içerik kartı

### Badge'ler
- `.badge-game` - Dolu badge
- `.badge-game-outline` - Outline badge

### Input'lar
- `.input-game` - Oyun temalı input

### Alert'ler
- `.alert-game-success` - Başarı mesajı
- `.alert-game-warning` - Uyarı mesajı
- `.alert-game-danger` - Hata mesajı
- `.alert-game-info` - Bilgi mesajı

### Utility Classes
- `.bg-game-primary` - Ana tema arka plan rengi
- `.bg-game-secondary` - İkincil tema arka plan rengi
- `.bg-game-gradient` - Gradient arka plan
- `.text-game-primary` - Ana tema metin rengi
- `.text-game-secondary` - İkincil tema metin rengi
- `.border-game-primary` - Ana tema border rengi

### Animasyonlar
- `.animate-game-pulse` - Pulse animasyonu
- `.animate-game-glow` - Glow animasyonu
- `.animate-game-slide-in` - Slide-in animasyonu

## Blade Template'de Kullanım

Layout dosyanıza tema component'ini ekleyin:

```blade
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Oyuna Özel Tema -->
    <x-game-theme />
</head>
```

Component otomatik olarak:
- Oyuna özel CSS dosyasını yükler
- Theme color meta tag'lerini ayarlar
- JavaScript'e oyun bilgilerini aktarır

## JavaScript'te Oyun Bilgisine Erişim

```javascript
// Mevcut oyun bilgisi
console.log(window.currentGame);

// Örnek çıktı:
// {
//     id: 1,
//     name: 'PUBG Mobile',
//     slug: 'pubg',
//     themeColor: '#FF6B00',
//     settings: { ... }
// }
```

## Dinamik Tema Renkleri

Her oyun için `settings` JSON'unda tema renkleri saklanır:

```json
{
    "theme_color": "#FF6B00",
    "secondary_color": "#FFB800",
    "platforms": ["Android", "iOS"],
    "max_team_size": 4
}
```

Bu renkler otomatik olarak CSS variable'lara dönüştürülür:

```css
:root {
    --current-game-primary: #FF6B00;
    --current-game-secondary: #FFB800;
}
```

## Mevcut Temalar

- **PUBG Mobile** (`pubg.css`) - Turuncu/Sarı gradient tema

## Yeni Oyun Ekleme Checklist

- [ ] `{slug}.css` dosyası oluştur
- [ ] CSS variable'ları tanımla
- [ ] Dosyayı `public/css/themes/` klasörüne kopyala
- [ ] Game seeder'da tema renklerini ayarla
- [ ] Test et: `{slug}.takimsistemi.com`

## Performans Notları

- Tema CSS dosyaları sadece ilgili oyun subdomain'inde yüklenir
- Ana domain'de tema CSS'i yüklenmez
- CSS dosyaları browser cache'lenir
- Minimal dosya boyutu için sadece gerekli stiller eklenir

## Responsive Design

Tüm tema stilleri responsive olarak tasarlanmıştır:
- Mobil cihazlar için optimize edilmiş boyutlar
- Touch-friendly buton boyutları
- Tablet ve desktop için uyarlanmış layout'lar

## Dark Mode

Temalar otomatik olarak dark mode'u destekler:
- `prefers-color-scheme: dark` media query'si
- Arka plan renklerinin otomatik ayarlanması

## Browser Desteği

- Chrome/Edge: ✅ Tam destek
- Firefox: ✅ Tam destek
- Safari: ✅ Tam destek
- IE11: ❌ Desteklenmez (CSS Variables)

## Sorun Giderme

### Tema yüklenmiyor
1. `public/css/themes/{slug}.css` dosyasının var olduğunu kontrol edin
2. DetectGame middleware'in route'lara eklendiğini kontrol edin
3. Session'da game_id'nin set edildiğini kontrol edin

### Renkler yanlış görünüyor
1. CSS variable'ların doğru tanımlandığını kontrol edin
2. Browser cache'ini temizleyin
3. Game settings'deki renk kodlarını kontrol edin

### JavaScript'te currentGame null
1. DetectGame middleware'in çalıştığını kontrol edin
2. Geçerli bir oyun subdomain'inde olduğunuzu kontrol edin
3. game-theme component'inin yüklendiğini kontrol edin
