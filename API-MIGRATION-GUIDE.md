# API Migration Guide - Multi-Game Platform

## Genel Bakış

Bu doküman, PUBG Community platformunun multi-game platformuna geçişi ile birlikte API endpoint'lerinde yapılan değişiklikleri açıklar.

**Önemli Tarihler:**
- **Deprecation Date:** 2025-12-02 (Eski endpoint'ler kullanımdan kaldırıldı)
- **Sunset Date:** 2026-06-02 (Eski endpoint'ler tamamen kaldırılacak)

## Değişiklik Özeti

Multi-game platform güncellemesi ile birlikte, tüm oyuna özel API endpoint'leri artık `game_id` veya `game_slug` parametresi gerektirir. Bu değişiklik, platformun birden fazla oyunu desteklemesini sağlar.

## Deprecated Endpoint'ler

### 1. LFG (Looking for Group) Endpoint'leri

#### ❌ Eski Endpoint'ler
```http
GET  /api/v1/lfg-posts
GET  /api/v1/lfg-posts/{id}
POST /api/v1/create-lfg
PUT  /api/v1/update-lfg/{id}
DELETE /api/v1/delete-lfg/{id}
```

#### ✅ Yeni Endpoint'ler
```http
GET  /api/v1/lfg?game_slug=pubg
GET  /api/v1/lfg/{id}?game_slug=pubg
POST /api/v1/lfg
PUT  /api/v1/lfg/{id}
DELETE /api/v1/lfg/{id}
```

**Örnek Kullanım:**
```javascript
// Eski
fetch('/api/v1/lfg-posts')

// Yeni
fetch('/api/v1/lfg?game_slug=pubg')
// veya
fetch('/api/v1/lfg?game_id=1')
```

**POST/PUT İstekleri için:**
```javascript
// Eski
fetch('/api/v1/create-lfg', {
  method: 'POST',
  body: JSON.stringify({
    title: 'Takım arıyorum',
    description: 'Ranked için takım lazım'
  })
})

// Yeni - game_id body'de gönderilir
fetch('/api/v1/lfg', {
  method: 'POST',
  body: JSON.stringify({
    game_id: 1, // veya game_slug: 'pubg'
    title: 'Takım arıyorum',
    description: 'Ranked için takım lazım'
  })
})
```

---

### 2. Clan Endpoint'leri

#### ❌ Eski Endpoint'ler
```http
GET  /api/v1/clan-list
GET  /api/v1/clan-detail/{id}
POST /api/v1/create-clan
PUT  /api/v1/update-clan/{id}
```

#### ✅ Yeni Endpoint'ler
```http
GET  /api/v1/clans?game_slug=pubg
GET  /api/v1/clans/{id}?game_slug=pubg
POST /api/v1/clans
PUT  /api/v1/clans/{id}
```

---

### 3. Guide Endpoint'leri

#### ❌ Eski Endpoint'ler
```http
GET    /api/v1/guide-list
GET    /api/v1/guide-detail/{guide}
POST   /api/v1/create-guide
PUT    /api/v1/update-guide/{guide}
DELETE /api/v1/delete-guide/{guide}
```

#### ✅ Yeni Endpoint'ler
```http
GET    /api/v1/guides?game_slug=pubg
GET    /api/v1/guides/{guide}?game_slug=pubg
POST   /api/v1/guides
PUT    /api/v1/guides/{guide}
DELETE /api/v1/guides/{guide}
```

---

### 4. Community Post Endpoint'leri

#### ❌ Eski Endpoint'ler
```http
GET    /api/v1/posts
GET    /api/v1/posts/{communityPost}
POST   /api/v1/create-post
DELETE /api/v1/delete-post/{communityPost}
```

#### ✅ Yeni Endpoint'ler
```http
GET    /api/v1/community-posts?game_slug=pubg
GET    /api/v1/community-posts/{communityPost}?game_slug=pubg
POST   /api/v1/community-posts
DELETE /api/v1/community-posts/{communityPost}
```

---

### 5. Tournament Endpoint'leri

#### ❌ Eski Endpoint'ler
```http
GET  /api/v1/tournament-list
GET  /api/v1/tournament-detail/{id}
POST /api/v1/create-tournament
POST /api/v1/join-tournament/{id}
```

#### ✅ Yeni Endpoint'ler
```http
GET  /api/v1/tournaments?game_slug=pubg
GET  /api/v1/tournaments/{id}?game_slug=pubg
POST /api/v1/tournaments
POST /api/v1/tournaments/{id}/register
```

---

### 6. Squad (Team) Endpoint'leri

#### ❌ Eski Endpoint'ler
```http
GET    /api/v1/team-list
GET    /api/v1/team-detail/{id}
POST   /api/v1/create-team
PUT    /api/v1/update-team/{id}
DELETE /api/v1/delete-team/{id}
```

#### ✅ Yeni Endpoint'ler
```http
GET    /api/v1/squads?game_slug=pubg
GET    /api/v1/squads/{id}?game_slug=pubg
POST   /api/v1/squads
PUT    /api/v1/squads/{id}
DELETE /api/v1/squads/{id}
```

---

## Deprecation Warning'leri

Eski endpoint'leri kullandığınızda, response'da aşağıdaki bilgiler yer alır:

### Response Headers
```http
X-API-Deprecated: true
X-API-Deprecation-Date: 2025-12-02
X-API-Sunset-Date: 2026-06-02
X-API-New-Endpoint: /api/v1/lfg?game_slug=pubg
```

### Response Body
```json
{
  "data": [...],
  "_deprecated": {
    "message": "Bu endpoint kullanımdan kaldırılmıştır.",
    "deprecation_date": "2025-12-02",
    "sunset_date": "2026-06-02",
    "new_endpoint": "/api/v1/lfg?game_slug=pubg",
    "migration_guide": "https://takimsistemi.com/docs/api/migration-guide"
  }
}
```

---

## Oyun Parametreleri

Yeni API endpoint'leri iki şekilde oyun belirtmeyi destekler:

### 1. Game Slug (Önerilen)
```http
GET /api/v1/lfg?game_slug=pubg
GET /api/v1/clans?game_slug=cod-mobile
GET /api/v1/tournaments?game_slug=valorant
```

**Avantajları:**
- Daha okunabilir
- URL'de oyun adı görünür
- SEO dostu

### 2. Game ID
```http
GET /api/v1/lfg?game_id=1
GET /api/v1/clans?game_id=2
GET /api/v1/tournaments?game_id=3
```

**Avantajları:**
- Daha kısa
- Performans açısından hafif daha hızlı

---

## Desteklenen Oyunlar

Aktif oyunları listelemek için:

```http
GET /api/v1/games
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "PUBG Mobile",
      "slug": "pubg",
      "logo": "/storage/games/pubg-logo.png",
      "status": "active"
    },
    {
      "id": 2,
      "name": "Call of Duty Mobile",
      "slug": "cod-mobile",
      "logo": "/storage/games/cod-logo.png",
      "status": "active"
    }
  ]
}
```

---

## Migration Checklist

### Mobil Uygulama Geliştiricileri İçin

- [ ] Tüm API endpoint URL'lerini güncelle
- [ ] GET isteklerine `game_slug` veya `game_id` parametresi ekle
- [ ] POST/PUT isteklerinin body'sine `game_id` ekle
- [ ] Oyun seçimi için UI ekle
- [ ] Seçilen oyunu local storage'da sakla
- [ ] Deprecation warning'lerini logla
- [ ] Test ortamında tüm endpoint'leri test et
- [ ] Production'a geçmeden önce kullanıcıları bilgilendir

### Web Geliştiricileri İçin

- [ ] API client'ı güncelle
- [ ] Oyun context'ini tüm API isteklerine ekle
- [ ] Error handling'i güncelle (yeni error response'ları için)
- [ ] Deprecation warning'lerini console'da göster
- [ ] Test coverage'ı güncelle

---

## Örnek Migration Senaryosu

### Senaryo: LFG Listesi Çekme

#### Eski Kod (JavaScript)
```javascript
async function fetchLfgPosts() {
  const response = await fetch('/api/v1/lfg-posts', {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  return data;
}
```

#### Yeni Kod (JavaScript)
```javascript
async function fetchLfgPosts(gameSlug = 'pubg') {
  const response = await fetch(`/api/v1/lfg?game_slug=${gameSlug}`, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  
  // Deprecation warning kontrolü
  if (data._deprecated) {
    console.warn('API Deprecation Warning:', data._deprecated);
  }
  
  return data;
}
```

#### Yeni Kod (React Native)
```javascript
import AsyncStorage from '@react-native-async-storage/async-storage';

async function fetchLfgPosts() {
  // Kullanıcının seçtiği oyunu al
  const selectedGame = await AsyncStorage.getItem('selected_game') || 'pubg';
  
  const response = await fetch(`/api/v1/lfg?game_slug=${selectedGame}`, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  
  const data = await response.json();
  
  // Deprecation warning'i logla
  if (response.headers.get('X-API-Deprecated') === 'true') {
    console.warn('Deprecated API endpoint used');
    console.warn('New endpoint:', response.headers.get('X-API-New-Endpoint'));
    console.warn('Sunset date:', response.headers.get('X-API-Sunset-Date'));
  }
  
  return data;
}
```

---

## Sık Sorulan Sorular

### 1. Eski endpoint'ler ne zaman tamamen kaldırılacak?
**Cevap:** 2026-06-02 tarihinde (6 ay sonra) eski endpoint'ler tamamen kaldırılacak.

### 2. Birden fazla oyun için aynı anda veri çekebilir miyim?
**Cevap:** Hayır, her istek tek bir oyun için veri döner. Birden fazla oyun için ayrı istekler yapmanız gerekir.

### 3. game_id ve game_slug'ı aynı anda gönderebilir miyim?
**Cevap:** Evet, ancak `game_slug` önceliklidir. Her ikisini de gönderirseniz `game_slug` kullanılır.

### 4. Cross-game özellikler var mı?
**Cevap:** Evet! Mesajlaşma, arkadaşlık ve bildirimler oyunlar arası çalışır. Bu endpoint'ler oyun parametresi gerektirmez.

### 5. Eski endpoint'leri kullanmaya devam edersem ne olur?
**Cevap:** Şu an çalışmaya devam eder ancak deprecation warning'leri alırsınız. 2026-06-02'den sonra 404 hatası alırsınız.

---

## Destek

Sorularınız için:
- **Email:** api-support@takimsistemi.com
- **Discord:** https://discord.gg/takimsistemi
- **Dokümantasyon:** https://docs.takimsistemi.com

---

## Changelog

### 2025-12-02
- Multi-game platform güncellemesi
- Tüm oyuna özel endpoint'ler için game parametresi zorunlu hale getirildi
- Eski endpoint'ler deprecated olarak işaretlendi
- Deprecation warning sistemi eklendi

---

**Son Güncelleme:** 2025-12-02
