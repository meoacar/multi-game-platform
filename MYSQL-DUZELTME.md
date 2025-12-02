# 🔧 MySQL Düzeltme Kılavuzu

## 📋 Durum
- ✅ Yeni XAMPP kuruldu: `C:\xampp`
- ✅ Eski veritabanları güvende: `G:\xampp\mysql\`
- ❌ MySQL bozuldu (sistem veritabanları uyumsuz)

---

## ✅ Çözüm: Temiz Kurulum

### Adım 1: MySQL'i Temiz Başlat

1. **XAMPP Control Panel'i aç**
2. **MySQL'i başlat** (Start butonu)
3. MySQL yeşil olmalı ✅

---

### Adım 2: Veritabanlarını Oluştur

**phpMyAdmin'i aç:** http://localhost/phpmyadmin

Her veritabanı için:
1. Sol tarafta "New" tıkla
2. Veritabanı adını yaz
3. Collation: `utf8mb4_unicode_ci`
4. "Create" tıkla

**Oluşturulacak veritabanları:**
- pubg_community
- kadinatlasi
- kadinlar_atlasi
- laravel
- zayiflamaplan

---

### Adım 3: PUBG Community Projesini Hazırla

```bash
cd F:\Pubg\pubg-community

# .env dosyasını kontrol et
notepad .env
```

**.env içeriği şöyle olmalı:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pubg_community
DB_USERNAME=root
DB_PASSWORD=
```

---

### Adım 4: Migration'ları Çalıştır

```bash
# Cache temizle
C:\xampp\php\php.exe artisan config:clear
C:\xampp\php\php.exe artisan cache:clear

# Migration'ları çalıştır (tüm tabloları oluşturur)
C:\xampp\php\php.exe artisan migrate:fresh

# Seeder'ları çalıştır (örnek veriler)
C:\xampp\php\php.exe artisan db:seed
```

---

### Adım 5: Test Et

```bash
# Veritabanı bağlantısını test et
C:\xampp\php\php.exe artisan db:show

# Sunucuyu başlat
C:\xampp\php\php.exe artisan serve

# Tarayıcıda aç
http://localhost:8000
```

---

## 🎯 Test Kullanıcıları

Migration + Seed sonrası:

**Admin:**
- Email: admin@pubg.com
- Şifre: password

**Normal Kullanıcı:**
- Email: user@pubg.com
- Şifre: password

---

## 🔄 Diğer Projeler İçin

### kadinatlasi, laravel, zayiflamaplan

Her proje için:

1. **Veritabanını oluştur** (phpMyAdmin'den)
2. **Proje klasörüne git**
3. **Migration çalıştır:**
   ```bash
   C:\xampp\php\php.exe artisan migrate:fresh --seed
   ```

---

## ⚠️ Eski Verileri Geri Yüklemek İsterseniz

Eğer eski verilere ihtiyacın varsa (kullanıcılar, ilanlar, vb.):

### Yöntem 1: mysqldump ile (Eski MySQL Çalışıyorsa)

```bash
# Eski XAMPP'in MySQL'ini başlat (G:\xampp)
# Her veritabanı için dump al:
G:\xampp\mysql\bin\mysqldump.exe -u root pubg_community > pubg_backup.sql

# Yeni MySQL'e import et:
C:\xampp\mysql\bin\mysql.exe -u root pubg_community < pubg_backup.sql
```

### Yöntem 2: phpMyAdmin ile

1. Eski veritabanı klasörünü geçici MySQL'e kopyala
2. phpMyAdmin'den Export yap (SQL)
3. Yeni MySQL'de Import yap

---

## 🆘 Sorun Giderme

### MySQL Başlamıyor

**Hata:** Port 3306 kullanımda

```bash
# Port'u kontrol et
netstat -ano | findstr :3306

# Process'i sonlandır
taskkill /PID [PID] /F
```

### Migration Hatası

**Hata:** `Class not found`

```bash
# Composer'ı güncelle
composer install
composer dump-autoload
```

### Veritabanı Bağlantı Hatası

```bash
# Config cache'i temizle
C:\xampp\php\php.exe artisan config:clear

# .env dosyasını kontrol et
notepad .env
```

---

## ✅ Başarı Kontrol Listesi

- [ ] XAMPP kuruldu (C:\xampp)
- [ ] MySQL başlatıldı (yeşil ✅)
- [ ] phpMyAdmin açılıyor (http://localhost/phpmyadmin)
- [ ] pubg_community veritabanı oluşturuldu
- [ ] .env dosyası doğru
- [ ] Migration'lar çalıştı
- [ ] Seeder'lar çalıştı
- [ ] php artisan serve çalışıyor
- [ ] http://localhost:8000 açılıyor
- [ ] Login yapılabiliyor (admin@pubg.com / password)

---

## 📞 Yardım

Sorun devam ederse:
1. XAMPP error log'larını kontrol et: `C:\xampp\mysql\data\mysql_error.log`
2. Laravel log'larını kontrol et: `storage/logs/laravel.log`

---

**Başarılar! 🚀**
