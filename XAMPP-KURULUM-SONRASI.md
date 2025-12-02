# 🔧 XAMPP Kurulum Sonrası Veritabanı Geri Yükleme

## ✅ Adım Adım Kılavuz

### 1. XAMPP'i Başlat
- XAMPP Control Panel'i aç
- **Apache** ve **MySQL**'i başlat
- Her ikisi de yeşil olmalı ✅

---

### 2. Veritabanını Oluştur

**Yöntem 1: phpMyAdmin (Kolay)**
1. Tarayıcıda aç: http://localhost/phpmyadmin
2. Sol tarafta "New" (Yeni) tıkla
3. Veritabanı adı: `pubg_community`
4. Collation: `utf8mb4_unicode_ci`
5. "Create" (Oluştur) tıkla

**Yöntem 2: CMD (Hızlı)**
```bash
G:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE pubg_community CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

### 3. Veritabanını Geri Yükle

#### Yöntem A: Dosya Yedeğinden (Hızlı)

```bash
# 1. MySQL'i durdur
G:\xampp\mysql_stop.bat

# 2. Yedek klasörü kopyala
Copy-Item -Path "F:\Pubg\database-backup-2025-12-02-083418\pubg_community" -Destination "G:\xampp\mysql\data\" -Recurse -Force

# 3. MySQL'i başlat
G:\xampp\mysql_start.bat
```

#### Yöntem B: Laravel Migration (Temiz Kurulum)

```bash
# Proje klasörüne git
cd F:\Pubg\pubg-community

# .env dosyasını kontrol et
notepad .env

# Migration'ları çalıştır
php artisan migrate:fresh

# Seeder'ları çalıştır
php artisan db:seed
```

---

### 4. Bağlantıyı Test Et

```bash
cd F:\Pubg\pubg-community
php artisan db:show
```

Çıktı şöyle olmalı:
```
MySQL ........................... 8.x.x
Database ........................ pubg_community
Host ............................ 127.0.0.1
Port ............................ 3306
Username ........................ root
```

---

### 5. Projeyi Başlat

```bash
# Laravel sunucusunu başlat
php artisan serve

# Tarayıcıda aç
http://localhost:8000
```

---

## 🔧 Sorun Giderme

### MySQL Başlamıyor

**Hata:** Port 3306 kullanımda

**Çözüm:**
```bash
# Port'u kullanan programı bul
netstat -ano | findstr :3306

# Process'i sonlandır (PID numarasını kullan)
taskkill /PID [PID_NUMARASI] /F

# MySQL'i tekrar başlat
```

---

### Veritabanı Bağlantı Hatası

**Hata:** `SQLSTATE[HY000] [2002]`

**Çözüm:**
1. `.env` dosyasını kontrol et:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pubg_community
DB_USERNAME=root
DB_PASSWORD=
```

2. Config cache'i temizle:
```bash
php artisan config:clear
php artisan cache:clear
```

---

### Migration Hatası

**Hata:** `Table already exists`

**Çözüm:**
```bash
# Tüm tabloları sil ve yeniden oluştur
php artisan migrate:fresh

# Verileri de ekle
php artisan migrate:fresh --seed
```

---

## 📋 Kontrol Listesi

- [ ] XAMPP kuruldu (Apache + MySQL)
- [ ] MySQL başlatıldı (yeşil ✅)
- [ ] Veritabanı oluşturuldu (`pubg_community`)
- [ ] Yedek geri yüklendi
- [ ] `.env` dosyası doğru
- [ ] `php artisan db:show` çalışıyor
- [ ] `php artisan serve` çalışıyor
- [ ] http://localhost:8000 açılıyor

---

## 🎯 Hızlı Komutlar

```bash
# XAMPP MySQL'i başlat
G:\xampp\mysql_start.bat

# Veritabanı durumunu kontrol et
php artisan db:show

# Migration'ları çalıştır
php artisan migrate

# Seeder'ları çalıştır
php artisan db:seed

# Sunucuyu başlat
php artisan serve
```

---

## 💾 Yedek Konumu

**Veritabanı Yedeği:**
- 📁 Klasör: `F:\Pubg\database-backup-2025-12-02-083418`
- 📊 Boyut: 7.34 MB
- 📅 Tarih: 2 Aralık 2025, 08:34

**Yedek İçeriği:**
- Tüm tablolar (35+ tablo)
- Tüm veriler (kullanıcılar, ilanlar, klanlar, vb.)
- Index'ler ve foreign key'ler

---

## 🆘 Acil Durum

Eğer hiçbir şey çalışmazsa:

1. **Temiz Kurulum:**
```bash
php artisan migrate:fresh --seed
```

2. **Test Kullanıcıları:**
- Admin: admin@pubg.com / password
- User: user@pubg.com / password

3. **Destek:**
- XAMPP Forum: https://community.apachefriends.org/
- Laravel Docs: https://laravel.com/docs

---

**Başarılar! 🚀**
