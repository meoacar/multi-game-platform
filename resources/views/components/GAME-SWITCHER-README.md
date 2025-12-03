# Game Switcher Component

## Genel Bakış

Game Switcher, kullanıcıların farklı oyunlar arasında kolayca geçiş yapmasını sağlayan bir Blade component'idir.

## Özellikler

- ✅ Aktif oyun gösterimi
- ✅ Dropdown ile oyun listesi
- ✅ Oyun logoları ve açıklamaları
- ✅ Responsive tasarım (desktop ve mobil)
- ✅ Alpine.js ile interaktif UI
- ✅ Otomatik oyun URL'i oluşturma
- ✅ Cache desteği (performans)
- ✅ Tek oyun varsa basit gösterim

## Kullanım

### Temel Kullanım

```blade
<x-game-switcher />
```

Component otomatik olarak:
1. GameService'den aktif oyunları çeker
2. Mevcut oyunu session'dan alır
3. Dropdown menüsünü oluşturur
4. Oyun değiştirme linklerini hazırlar

### Navbar'a Entegrasyon

```blade
<!-- Desktop Menu -->
<div class="hidden lg:flex items-center space-x-1">
    <x-game-switcher />
    <!-- Diğer menü öğeleri -->
</div>

<!-- Mobile Menu -->
<div class="lg:hidden">
    <x-game-switcher />
    <!-- Diğer menü öğeleri -->
</div>
```

## Gereksinimler

### Backend

- ✅ `App\Services\GameService` - Oyun yönetimi servisi
- ✅ `App\Models\Game` - Oyun modeli
- ✅ Session'da `game_id` ve `game` değerleri

### Frontend

- ✅ Alpine.js (CDN veya npm)
- ✅ Tailwind CSS
- ✅ SVG icon desteği

## Component Davranışı

### Çoklu Oyun Durumu (2+ oyun)

Dropdown menü gösterilir:
- Aktif oyun vurgulanır (yeşil nokta)
- Tüm aktif oyunlar listelenir
- Her oyun için logo, isim ve açıklama gösterilir
- Hover efektleri aktif

### Tek Oyun Durumu (1 oyun)

Basit gösterim:
- Sadece oyun logosu ve ismi gösterilir
- Dropdown menü gösterilmez
- Statik görünüm

### Oyun Yok Durumu (0 oyun)

Component render edilmez.

## Oyun Değiştirme Akışı

1. Kullanıcı dropdown'dan oyun seçer
2. `GameService::getGameUrl($game)` ile subdomain URL'i oluşturulur
3. Kullanıcı ilgili subdomain'e yönlendirilir
4. `DetectGame` middleware oyunu algılar
5. Session güncellenir
6. Kullanıcı yeni oyunun sayfasını görür

## Styling

Component Tailwind CSS kullanır:

### Renkler
- Background: `bg-gray-800/95`
- Hover: `hover:bg-white/5`
- Border: `border-white/10`
- Text: `text-gray-300`, `text-white`
- Accent: `text-orange-400`

### Efektler
- Backdrop blur: `backdrop-blur-xl`
- Transitions: `transition-all`
- Shadows: `shadow-2xl`
- Animations: `animate-pulse` (aktif oyun)

## Performans

### Cache Kullanımı

GameService otomatik olarak cache kullanır:
- Oyun listesi: 1 saat (3600 saniye)
- Mevcut oyun: 1 saat (3600 saniye)

### Optimizasyonlar

- Lazy loading: Dropdown sadece açıldığında render edilir
- Alpine.js: Minimal JavaScript footprint
- SVG icons: Hafif ve ölçeklenebilir

## Accessibility

- ✅ `aria-label`: Buton açıklaması
- ✅ `aria-expanded`: Dropdown durumu
- ✅ Keyboard navigation: Tab ve Enter desteği
- ✅ Screen reader uyumlu

## Özelleştirme

### Logo Değiştirme

```php
// Game model'de
$game->logo = 'games/pubg-logo.png';
$game->save();
```

### Tema Rengi

```php
// Game settings'de
$game->settings = [
    'theme_color' => '#FF6B00',
    // ...
];
```

### Dropdown Genişliği

```blade
<!-- Component içinde -->
<div class="w-72"> <!-- Varsayılan: 18rem -->
```

## Troubleshooting

### Component Görünmüyor

1. Alpine.js yüklü mü kontrol edin:
```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

2. GameService doğru çalışıyor mu:
```php
$gameService = app(GameService::class);
dd($gameService->getActiveGames());
```

3. Aktif oyun var mı:
```php
Game::where('status', 'active')->count();
```

### Dropdown Açılmıyor

1. Alpine.js console hatalarını kontrol edin
2. `x-data` directive'i doğru mu kontrol edin
3. Browser console'da JavaScript hataları var mı bakın

### Oyun Değiştirme Çalışmıyor

1. GameService::getGameUrl() doğru URL üretiyor mu:
```php
$game = Game::first();
dd(app(GameService::class)->getGameUrl($game));
```

2. DetectGame middleware aktif mi:
```php
// routes/web.php
Route::middleware(['detect.game'])->group(function () {
    // ...
});
```

## İlgili Dosyalar

- Component: `resources/views/components/game-switcher.blade.php`
- Service: `app/Services/GameService.php`
- Model: `app/Models/Game.php`
- Middleware: `app/Http/Middleware/DetectGame.php`
- Navbar: `resources/views/components/navbar.blade.php`

## Requirements

Bu component aşağıdaki requirement'ları karşılar:

- **Requirement 7.1**: Platform SHALL provide a game switcher component in the navigation bar
- **Requirement 7.5**: Platform SHALL display the current game context clearly in the UI

## Gelecek İyileştirmeler

- [ ] Oyun istatistikleri gösterimi (oyuncu sayısı, aktif turnuva)
- [ ] Favori oyun işaretleme
- [ ] Son ziyaret edilen oyunlar listesi
- [ ] Oyun arama özelliği (çok sayıda oyun için)
- [ ] Keyboard shortcuts (Ctrl+G gibi)
- [ ] Oyun önizleme (hover'da daha fazla bilgi)

## Lisans

Bu component PUBG Mobile Topluluk Platformu'nun bir parçasıdır.
