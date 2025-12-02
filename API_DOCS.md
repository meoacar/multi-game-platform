# 📡 API Dokümantasyonu

PUBG Mobile Topluluk Platformu RESTful API v1

**Base URL**: `/api/v1`  
**Authentication**: Bearer Token (Laravel Sanctum)  
**Response Format**: JSON

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

### Get Game
```http
GET /api/v1/games/{id}
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

### List LFG Posts
```http
GET /api/v1/lfg?game_id=1&city=İstanbul&status=open
```

**Query Parameters**:
- `game_id` (optional)
- `city` (optional)
- `play_style_tag` (optional)
- `status` (optional): open, closed

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
