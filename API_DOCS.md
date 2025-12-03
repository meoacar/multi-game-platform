# 📡 API Dokümantasyonu

Takım Sistemi Multi-Game Platform RESTful API v1

**Base URL**: `/api/v1`  
**Authentication**: Bearer Token (Laravel Sanctum)  
**Response Format**: JSON

## 🆕 Multi-Game Platform Güncellemesi

**Önemli:** 2025-12-03 tarihinden itibaren, tüm oyuna özel endpoint'ler `game_id` veya `game_slug` parametresi gerektirir.

### Oyun Parametreleri

Tüm oyuna özel endpoint'lerde şu parametrelerden biri kullanılmalıdır:

- **`game_slug`** (önerilen): Oyun slug'ı (örn: `pubg`, `cod`)
- **`game_id`**: Oyun ID'si (örn: `1`, `2`)

**Örnek:**
```http
GET /api/v1/tournaments?game_slug=pubg
GET /api/v1/clans?game_id=1
```

### Endpoint Kategorileri

#### 🎮 Oyuna Özel Endpoint'ler
Bu endpoint'ler `game_slug` veya `game_id` parametresi gerektirir:
- Tournaments
- Clans
- LFG Posts
- Squads
- Community Posts
- Guide Posts

#### 🌐 Cross-Game Endpoint'ler
Bu endpoint'ler oyun parametresi gerektirmez:
- Authentication (register, login, logout)
- User Profile
- Messages
- Notifications
- Friendships

### Migration Guide

Eski API kullanımından yeni API'ye geçiş için: [API-MIGRATION-GUIDE.md](API-MIGRATION-GUIDE.md)

---

## 🔐 Authentication

### Register
```http
POST /api/v1/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201)**:
```json
{
  "success": true,
  "message": "Kayıt başarılı",
  "data": {
    "user": { ... },
    "token": "1|abc123..."
  }
}
```

### Login
```http
POST /api/v1/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

### Get Current User
```http
GET /api/v1/me
Authorization: Bearer {token}
```

---

## 🎮 Games

### List Games
```http
GET /api/v1/games
```

**Query Parameters**:
- `status` (optional): `active` | `inactive` - Oyun durumu filtresi
- `is_active` (optional): `1` | `0` - Backward compatibility için aktif/inaktif filtresi
- `with_stats` (optional): `1` | `0` - İstatistiklerle birlikte getir

**Response (200)**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "PUBG Mobile",
      "slug": "pubg",
      "logo": "storage/games/pubg-logo.png",
      "icon": "storage/games/pubg-icon.png",
      "description": "Battle Royale oyunu",
      "status": "active",
      "is_active": true,
      "settings": {
        "theme_color": "#FF6B00",
        "secondary_color": "#FFB800",
        "max_team_size": 4,
        "platforms": ["Android", "iOS"],
        "features": {
          "tournaments": true,
          "clans": true,
          "lfg": true,
          "matchmaking": true
        }
      },
      "order": 1,
      "created_at": "2025-12-01T00:00:00.000000Z",
      "updated_at": "2025-12-01T00:00:00.000000Z",
      "stats": {
        "tournaments_count": 10,
        "clans_count": 50,
        "lfg_posts_count": 100,
        "badges_count": 25,
        "guides_count": 30,
        "community_posts_count": 200
      }
    }
  ],
  "meta": {
    "total": 1,
    "active_count": 1,
    "inactive_count": 0
  }
}
```

**Examples**:
```bash
# Tüm oyunları listele
curl -X GET "https://takimsistemi.com/api/v1/games"

# Sadece aktif oyunları listele
curl -X GET "https://takimsistemi.com/api/v1/games?status=active"

# İstatistiklerle birlikte listele
curl -X GET "https://takimsistemi.com/api/v1/games?with_stats=1"

# Backward compatibility - aktif oyunlar
curl -X GET "https://takimsistemi.com/api/v1/games?is_active=1"
```

### Get Game by ID or Slug
```http
GET /api/v1/games/{idOrSlug}
```

**Path Parameters**:
- `idOrSlug`: Oyun ID'si (örn: `1`) veya slug'ı (örn: `pubg`)

**Query Parameters**:
- `with_stats` (optional): `1` | `0` - İstatistiklerle birlikte getir

**Response (200)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "PUBG Mobile",
    "slug": "pubg",
    "logo": "storage/games/pubg-logo.png",
    "icon": "storage/games/pubg-icon.png",
    "description": "Battle Royale oyunu",
    "status": "active",
    "is_active": true,
    "settings": {
      "theme_color": "#FF6B00",
      "secondary_color": "#FFB800",
      "max_team_size": 4,
      "platforms": ["Android", "iOS"],
      "features": {
        "tournaments": true,
        "clans": true,
        "lfg": true,
        "matchmaking": true
      }
    },
    "order": 1,
    "created_at": "2025-12-01T00:00:00.000000Z",
    "updated_at": "2025-12-01T00:00:00.000000Z",
    "stats": {
      "tournaments_count": 10,
      "clans_count": 50,
      "lfg_posts_count": 100,
      "badges_count": 25,
      "guides_count": 30,
      "community_posts_count": 200
    }
  }
}
```

**Response (404)** - Oyun bulunamadı:
```json
{
  "message": "No query results for model [App\\Models\\Game]."
}
```

**Examples**:
```bash
# ID ile oyun detayı
curl -X GET "https://takimsistemi.com/api/v1/games/1"

# Slug ile oyun detayı
curl -X GET "https://takimsistemi.com/api/v1/games/pubg"

# İstatistiklerle birlikte
curl -X GET "https://takimsistemi.com/api/v1/games/pubg?with_stats=1"
```

---

## 👤 Profile

### Get User Profile
```http
GET /api/v1/users/{id}/profile
```

### Update Profile
```http
PUT /api/v1/me/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "nickname": "ProPlayer",
  "pubg_id": "123456",
  "rank": "Ace",
  "server_region": "EU",
  "city": "İstanbul",
  "age_range": "18-24",
  "play_style": "try-hard",
  "bio": "Competitive player"
}
```

---

## 📱 Device

### List Devices
```http
GET /api/v1/devices?device_name=Poco
```

### Get My Device
```http
GET /api/v1/me/device
Authorization: Bearer {token}
```

### Update Device
```http
PUT /api/v1/me/device
Authorization: Bearer {token}
Content-Type: application/json

{
  "device_name": "Poco X6 Pro",
  "graphics_settings": "HDR + Extreme",
  "fps_setting": "90 FPS",
  "gyro_enabled": true,
  "sensitivity_settings": {
    "general": 80,
    "ads": 60,
    "gyro": 300
  }
}
```

---

## 🔍 LFG (Looking For Group)

⚠️ **Oyuna Özel Endpoint** - `game_slug` veya `game_id` parametresi gerektirir

### List LFG Posts
```http
GET /api/v1/lfg?game_slug=pubg&city=İstanbul&status=open
```

**Query Parameters**:
- `game_slug` (required): Oyun slug'ı (örn: `pubg`, `cod`)
- `game_id` (alternative): Oyun ID'si (örn: `1`, `2`)
- `city` (optional): Şehir filtresi
- `play_style_tag` (optional): Oyun stili filtresi
- `status` (optional): `open`, `closed` - İlan durumu

**Examples**:
```bash
# PUBG LFG ilanları
curl -X GET "https://takimsistemi.com/api/v1/lfg?game_slug=pubg"

# COD Mobile LFG ilanları (İstanbul)
curl -X GET "https://takimsistemi.com/api/v1/lfg?game_slug=cod&city=İstanbul"

# Game ID ile
curl -X GET "https://takimsistemi.com/api/v1/lfg?game_id=1&status=open"
```

### Get LFG Post
```http
GET /api/v1/lfg/{id}
```

### Create LFG Post
```http
POST /api/v1/lfg
Authorization: Bearer {token}
Content-Type: application/json

{
  "game_id": 1,
  "title": "Akşam 22-01 Ace rank squad",
  "description": "Mikrofon şart, try-hard oynuyoruz",
  "min_rank": "Diamond",
  "max_rank": "Conqueror",
  "mode": "Squad TPP",
  "microphone_required": true,
  "city": "İstanbul",
  "play_style_tag": "try-hard"
}
```

**Note:** `game_id` alanı zorunludur. Mevcut oyun bağlamından otomatik alınmaz.
```

### Update LFG Post
```http
PUT /api/v1/lfg/{id}
Authorization: Bearer {token}
```

### Delete LFG Post
```http
DELETE /api/v1/lfg/{id}
Authorization: Bearer {token}
```

### Apply to LFG
```http
POST /api/v1/lfg/{id}/apply
Authorization: Bearer {token}
Content-Type: application/json

{
  "message": "Merhaba, katılmak istiyorum"
}
```

### Get Applications
```http
GET /api/v1/lfg/{id}/applications
Authorization: Bearer {token}
```

### Accept Application
```http
PATCH /api/v1/lfg-applications/{id}/accept
Authorization: Bearer {token}
```

### Reject Application
```http
PATCH /api/v1/lfg-applications/{id}/reject
Authorization: Bearer {token}
```

---

## 🛡️ Clans

### List Clans
```http
GET /api/v1/clans?game_id=1&city=İstanbul
```

### Get Clan
```http
GET /api/v1/clans/{id}
```

### Create Clan
```http
POST /api/v1/clans
Authorization: Bearer {token}
Content-Type: application/json

{
  "game_id": 1,
  "name": "Elite Squad",
  "description": "Competitive clan",
  "requirements": "Ace+ rank, daily active",
  "min_rank": "Ace",
  "city": "İstanbul"
}
```

### Apply to Clan
```http
POST /api/v1/clans/{id}/apply
Authorization: Bearer {token}
Content-Type: application/json

{
  "message": "Merhaba, klanınıza katılmak istiyorum"
}
```

---

## 📚 Guides

### List Guides
```http
GET /api/v1/guides?game_id=1
```

### Get Guide
```http
GET /api/v1/guides/{id}
```

### Create Guide
```http
POST /api/v1/guides
Authorization: Bearer {token}
Content-Type: application/json

{
  "game_id": 1,
  "title": "Recoil Kontrol Rehberi",
  "content": "# Giriş\n\nRecoil kontrolü...",
  "is_published": true
}
```

### Like Guide
```http
POST /api/v1/guides/{id}/like
Authorization: Bearer {token}
```

---

## 👥 Squads

### List Squads
```http
GET /api/v1/squads?game_id=1
```

### Get Squad
```http
GET /api/v1/squads/{id}
```

### Create Squad
```http
POST /api/v1/squads
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Pro Team",
  "game_id": 1,
  "description": "Competitive squad",
  "max_members": 5
}
```

### Invite Member
```http
POST /api/v1/squads/{id}/invite
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 123
}
```

---

## 🏆 Tournaments

### List Tournaments
```http
GET /api/v1/tournaments?status=registration_open&game_id=1
```

### Get Tournament
```http
GET /api/v1/tournaments/{id}
```

### Create Tournament
```http
POST /api/v1/tournaments
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "PUBG Mobile Championship",
  "game_id": 1,
  "description": "Competitive tournament",
  "rules": "Standard PUBG rules apply",
  "prize_pool": "10,000 TL",
  "max_teams": 16,
  "team_size": 4,
  "registration_starts_at": "2025-12-01 00:00:00",
  "registration_ends_at": "2025-12-15 23:59:59",
  "tournament_starts_at": "2025-12-20 18:00:00",
  "tournament_ends_at": "2025-12-20 22:00:00"
}
```

### Register to Tournament
```http
POST /api/v1/tournaments/{id}/register
Authorization: Bearer {token}
Content-Type: application/json

{
  "team_name": "Elite Squad",
  "members": [1, 2, 3, 4]
}
```

---

## 💬 Messages

### List Messages
```http
GET /api/v1/messages
Authorization: Bearer {token}
```

### Get Conversation
```http
GET /api/v1/messages/{userId}
Authorization: Bearer {token}
```

### Send Message
```http
POST /api/v1/messages
Authorization: Bearer {token}
Content-Type: application/json

{
  "receiver_id": 123,
  "content": "Merhaba!"
}
```

### Mark as Read
```http
PATCH /api/v1/messages/{id}/read
Authorization: Bearer {token}
```

---

## 🔔 Notifications

### List Notifications
```http
GET /api/v1/notifications
Authorization: Bearer {token}
```

### Get Unread Notifications
```http
GET /api/v1/notifications/unread
Authorization: Bearer {token}
```

### Get Unread Count
```http
GET /api/v1/notifications/unread-count
Authorization: Bearer {token}
```

### Mark as Read
```http
PATCH /api/v1/notifications/{id}/read
Authorization: Bearer {token}
```

### Mark All as Read
```http
PATCH /api/v1/notifications/mark-all-read
Authorization: Bearer {token}
```

---

## 👫 Friendships

### List Friends
```http
GET /api/v1/friends
Authorization: Bearer {token}
```

### Get Friend Requests
```http
GET /api/v1/friends/requests
Authorization: Bearer {token}
```

### Send Friend Request
```http
POST /api/v1/friends/request
Authorization: Bearer {token}
Content-Type: application/json

{
  "friend_id": 123
}
```

### Accept Request
```http
POST /api/v1/friends/{id}/accept
Authorization: Bearer {token}
```

### Reject Request
```http
POST /api/v1/friends/{id}/reject
Authorization: Bearer {token}
```

---

## 🎯 Matchmaking

### Join Queue
```http
POST /api/v1/matchmaking/join
Authorization: Bearer {token}
Content-Type: application/json

{
  "game_id": 1,
  "mode": "Squad",
  "min_rank": "Diamond",
  "max_rank": "Ace",
  "city": "İstanbul",
  "microphone_required": true,
  "play_style": "try-hard"
}
```

**Response (200)**:
```json
{
  "success": true,
  "message": "Kuyruğa eklendi",
  "data": {
    "queue": {
      "id": 1,
      "user_id": 123,
      "game_id": 1,
      "mode": "Squad",
      "status": "searching",
      "expires_at": "2025-12-01 12:35:00"
    }
  }
}
```

### Leave Queue
```http
POST /api/v1/matchmaking/leave
Authorization: Bearer {token}
```

**Response (200)**:
```json
{
  "success": true,
  "message": "Kuyruktan çıkıldı"
}
```

### Get Queue Status
```http
GET /api/v1/matchmaking/status
Authorization: Bearer {token}
```

**Response (200)**:
```json
{
  "success": true,
  "data": {
    "in_queue": true,
    "queue": {
      "id": 1,
      "status": "searching",
      "search_attempts": 3,
      "expires_at": "2025-12-01 12:35:00"
    },
    "match": null
  }
}
```

**Response (200) - Match Found**:
```json
{
  "success": true,
  "data": {
    "in_queue": false,
    "queue": null,
    "match": {
      "id": 1,
      "mode": "Squad",
      "status": "pending",
      "compatibility_score": 85,
      "expires_at": "2025-12-01 12:05:30",
      "users": [
        {
          "id": 123,
          "name": "ProPlayer",
          "profile": {
            "nickname": "ProPlayer",
            "rank": "Ace",
            "city": "İstanbul"
          }
        }
      ],
      "acceptance_status": {
        "123": null,
        "124": null,
        "125": null,
        "126": null
      }
    }
  }
}
```

### Accept Match
```http
POST /api/v1/matchmaking/matches/{id}/accept
Authorization: Bearer {token}
```

**Response (200)**:
```json
{
  "success": true,
  "message": "Eşleşme kabul edildi",
  "data": {
    "match": {
      "id": 1,
      "status": "pending",
      "acceptance_status": {
        "123": true,
        "124": null,
        "125": null,
        "126": null
      }
    }
  }
}
```

### Reject Match
```http
POST /api/v1/matchmaking/matches/{id}/reject
Authorization: Bearer {token}
```

**Response (200)**:
```json
{
  "success": true,
  "message": "Eşleşme reddedildi"
}
```

### Get Match History
```http
GET /api/v1/matchmaking/history
Authorization: Bearer {token}
```

**Query Parameters**:
- `result` (optional): accepted, rejected, expired, cancelled
- `per_page` (optional): default 20

**Response (200)**:
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "match_id": 1,
        "result": "accepted",
        "wait_time_seconds": 45,
        "compatibility_score": 85,
        "created_at": "2025-12-01 12:00:00"
      }
    ],
    "per_page": 20,
    "total": 10
  }
}
```

---

## 🏅 Badges

### List Badges
```http
GET /api/v1/badges
```

### Get User Badges
```http
GET /api/v1/me/badges
Authorization: Bearer {token}
```

---

## ⚠️ Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": ["Email alanı gereklidir"]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Bu işlemi yapma yetkiniz yok"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Kayıt bulunamadı"
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "Sunucu hatası"
}
```

---

## 📊 Rate Limiting

- **API Endpoints**: 60 requests/minute
- **Auth Endpoints**: 5 requests/minute

---

## 🔄 Pagination

Tüm liste endpoint'leri sayfalama destekler:

```json
{
  "data": [...],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

---

**API Version**: 1.0.0  
**Last Updated**: 24 Kasım 2025


---

## 🎮 Multi-Game API Kullanım Örnekleri

### Senaryo 1: Tüm Oyunları Listele ve Bir Oyun Seç

```javascript
// 1. Tüm oyunları al
const gamesResponse = await fetch('https://takimsistemi.com/api/v1/games');
const games = await gamesResponse.json();

// 2. Kullanıcı PUBG'yi seçti
const selectedGame = games.data.find(g => g.slug === 'pubg');

// 3. PUBG turnuvalarını al
const tournamentsResponse = await fetch(
  `https://takimsistemi.com/api/v1/tournaments?game_slug=${selectedGame.slug}`
);
const tournaments = await tournamentsResponse.json();
```

### Senaryo 2: Oyuna Özel İçerik Oluşturma

```javascript
// 1. Oyun seç
const gameSlug = 'pubg'; // veya 'cod'

// 2. LFG ilanı oluştur
const response = await fetch('https://takimsistemi.com/api/v1/lfg', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    game_id: 1, // PUBG
    title: 'Akşam squad',
    description: 'Mikrofon şart',
    // ...
  })
});
```

### Senaryo 3: Cross-Game Mesajlaşma

```javascript
// Mesajlar oyun bağlamından bağımsızdır
const messagesResponse = await fetch('https://takimsistemi.com/api/v1/messages', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});

// Kullanıcı hangi oyunda olursa olsun tüm mesajları görür
const messages = await messagesResponse.json();
```

### Senaryo 4: Kullanıcının Tüm Oyunlardaki Aktivitesi

```javascript
// 1. Kullanıcı profili (cross-game)
const profileResponse = await fetch('https://takimsistemi.com/api/v1/me', {
  headers: { 'Authorization': `Bearer ${token}` }
});

// 2. PUBG turnuvaları
const pubgTournaments = await fetch(
  'https://takimsistemi.com/api/v1/tournaments?game_slug=pubg',
  { headers: { 'Authorization': `Bearer ${token}` } }
);

// 3. COD klanları
const codClans = await fetch(
  'https://takimsistemi.com/api/v1/clans?game_slug=cod',
  { headers: { 'Authorization': `Bearer ${token}` } }
);
```

---

## 📋 Best Practices

### 1. Oyun Parametresi Kullanımı

**✅ Önerilen:**
```javascript
// Slug kullan (daha okunabilir)
fetch('/api/v1/tournaments?game_slug=pubg')

// Cache'lenebilir oyun listesi
const games = await getGames(); // Cache'den al
const gameId = games.find(g => g.slug === 'pubg').id;
```

**❌ Önerilmeyen:**
```javascript
// Hard-coded ID kullanma
fetch('/api/v1/tournaments?game_id=1') // ID değişebilir
```

### 2. Error Handling

```javascript
async function getTournaments(gameSlug) {
  try {
    const response = await fetch(
      `/api/v1/tournaments?game_slug=${gameSlug}`
    );
    
    if (!response.ok) {
      if (response.status === 404) {
        throw new Error('Oyun bulunamadı');
      }
      throw new Error('API hatası');
    }
    
    return await response.json();
  } catch (error) {
    console.error('Tournament fetch error:', error);
    throw error;
  }
}
```

### 3. Caching Stratejisi

```javascript
// Oyun listesini cache'le (nadiren değişir)
const CACHE_TTL = 3600000; // 1 saat
let gamesCache = null;
let gamesCacheTime = 0;

async function getGames() {
  const now = Date.now();
  
  if (gamesCache && (now - gamesCacheTime) < CACHE_TTL) {
    return gamesCache;
  }
  
  const response = await fetch('/api/v1/games');
  gamesCache = await response.json();
  gamesCacheTime = now;
  
  return gamesCache;
}
```

### 4. Pagination

```javascript
// Sayfalama ile veri çekme
async function getAllTournaments(gameSlug) {
  let page = 1;
  let allTournaments = [];
  let hasMore = true;
  
  while (hasMore) {
    const response = await fetch(
      `/api/v1/tournaments?game_slug=${gameSlug}&page=${page}`
    );
    const data = await response.json();
    
    allTournaments = [...allTournaments, ...data.data];
    hasMore = data.meta.current_page < data.meta.last_page;
    page++;
  }
  
  return allTournaments;
}
```

---

## 🔒 Security Best Practices

### 1. Token Yönetimi

```javascript
// Token'ı güvenli sakla
localStorage.setItem('auth_token', token); // ❌ XSS riski
sessionStorage.setItem('auth_token', token); // ✅ Daha güvenli

// veya HTTP-only cookie kullan (en güvenli)
```

### 2. Input Validation

```javascript
// Kullanıcı girdilerini validate et
function validateGameSlug(slug) {
  const validSlugs = ['pubg', 'cod', 'valorant'];
  if (!validSlugs.includes(slug)) {
    throw new Error('Geçersiz oyun slug');
  }
  return slug;
}
```

### 3. Rate Limiting

```javascript
// Rate limit'e takılma
const delay = ms => new Promise(resolve => setTimeout(resolve, ms));

async function batchRequests(requests) {
  const results = [];
  
  for (const request of requests) {
    results.push(await request());
    await delay(100); // 100ms bekle
  }
  
  return results;
}
```

---

## 📊 Response Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | İstek başarılı |
| 201 | Created | Kaynak oluşturuldu |
| 204 | No Content | İstek başarılı, içerik yok |
| 400 | Bad Request | Geçersiz istek |
| 401 | Unauthorized | Kimlik doğrulama gerekli |
| 403 | Forbidden | Yetki yok |
| 404 | Not Found | Kaynak bulunamadı |
| 422 | Unprocessable Entity | Validation hatası |
| 429 | Too Many Requests | Rate limit aşıldı |
| 500 | Internal Server Error | Sunucu hatası |

---

## 🔄 Versioning

API versiyonlama URL'de belirtilir:

- **v1** (current): `/api/v1/...`
- **v2** (future): `/api/v2/...`

**Deprecation Policy:**
- Yeni versiyon yayınlandıktan 6 ay sonra eski versiyon deprecated olur
- Deprecated endpoint'ler response header'ında uyarı içerir
- 12 ay sonra eski versiyon tamamen kaldırılır

---

## 📞 Support

**Dokümantasyon:**
- Architecture: [MULTI-GAME-ARCHITECTURE.md](MULTI-GAME-ARCHITECTURE.md)
- Migration Guide: [MULTI-GAME-MIGRATION-GUIDE.md](MULTI-GAME-MIGRATION-GUIDE.md)
- API Migration: [API-MIGRATION-GUIDE.md](API-MIGRATION-GUIDE.md)

**İletişim:**
- GitHub Issues: [repo-url]/issues
- Email: api@takimsistemi.com
- Discord: [discord-server]

---

**API Version:** 1.0.0  
**Platform Version:** 2.0.0 (Multi-Game)  
**Son Güncelleme:** 2025-12-03  
**Yazar:** Takım Sistemi Development Team
