# 🔐 Veritabanı Yedekleme ve Geri Yükleme Kılavuzu

## 📋 İçindekiler
1. [Otomatik Yedek Alma](#otomatik-yedek-alma)
2. [Manuel Yedek Alma](#manuel-yedek-alma)
3. [Veritabanını Geri Yükleme](#veritabanını-geri-yükleme)
4. [Zamanlanmış Yedekleme](#zamanlanmış-yedekleme)
5. [Sorun Giderme](#sorun-giderme)

---

## 🔄 Otomatik Yedek Alma

### Yöntem 1: Laravel Komutu (Önerilen)

```bash
# Varsayılan konuma yedek al (storage/backups)
php artisan db:backup

# Özel konuma yedek al
php artisan db:backup --path="F:\Pubg\database-backups"
```

**Özellikler:**
- ✅ SQL dump oluşturur
- ✅ MySQL çalışmıyorsa dosya yedeği alır
- ✅ 30 günden eski yedekleri otomatik temizler
- ✅ Dosya boyutunu gösterir

---

### Yöntem 2: Batch Script

```bash
# Windows'ta çift tıkla veya CMD'den çalıştır
backup-database.bat
```

**Yedek Konumu:** `F:\Pubg\database-backups\`

---

## 📥 Manuel Yedek Alma

### SQL Dump (MySQL Çalışıyorsa)

```bash
# CMD veya PowerShell
G:\xampp\mysql\bin\mysqldump.exe -u root pubg_community > yedek.sql
```

### Dosya Yedeği (MySQL Çalışmıyorsa)

```bash
# PowerShell
Copy-Item -Path "G:\xampp\mysql\data\pubg_community" -Destination "F:\Pubg\yedek" -Recurse
```

---

## 🔙 Veritabanını Geri Yükleme

### Yöntem 1: Batch Script (Kolay)

```bash
# SQL dosyasından geri yükle
restore-database.bat "F:\Pubg\database-backups\pubg_community_20251202.sql"
```

**UYARI:** Bu işlem mevcut veritabanını silecek!

---

### Yöntem 2: Manuel Geri Yükleme

#### SQL Dump'tan:

```bash
# 1. Veritabanını sil ve yeniden oluştur
G:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE IF EXISTS pubg_community; CREATE DATABASE pubg_community CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Yedekten geri yükle
G:\xampp\mysql\bin\mysql.exe -u root pubg_community < yedek.sql
```

#### Dosya Yedeğinden:

```bash
# 1. MySQL'i durdur
G:\xampp\mysql_stop.bat

# 2. Mevcut veritabanı klasörünü sil
Remove-Item "G:\xampp\mysql\data\pubg_community" -Recurse -Force

# 3. Yedek klasörü kopyala
Copy-Item -Path "F:\Pubg\yedek\pubg_community" -Destination "G:\xampp\mysql\data\" -Recurse

# 4. MySQL'i başlat
G:\xampp\mysql_start.bat
```

---

## ⏰ Zamanlanmış Yedekleme

### Windows Task Scheduler ile Otomatik Yedek

1. **Task Scheduler'ı Aç:**
   - `Win + R` → `taskschd.msc`

2. **Yeni Görev Oluştur:**
   - `Create Basic Task` → İsim: "PUBG DB Backup"

3. **Trigger (Tetikleyici):**
   - Günlük, saat 03:00

4. **Action (Eylem):**
   - Program: `php`
   - Arguments: `artisan db:backup --path="F:\Pubg\database-backups"`
   - Start in: `F:\Pubg\pubg-community`

5. **Kaydet ve Test Et**

---

### Laravel Scheduler ile (Önerilen)

**Kernel.php'ye ekle:**

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Her gün saat 03:00'te yedek al
    $schedule->command('db:backup --path="F:\Pubg\database-backups"')
        ->dailyAt('03:00')
        ->timezone('Europe/Istanbul');
}
```

**Scheduler'ı çalıştır:**

```bash
# Windows Task Scheduler'a ekle
php artisan schedule:run
```

---

## 🛠️ Sorun Giderme

### MySQL Başlamıyor

**Çözüm 1: Port Kontrolü**
```bash
# 3306 portunu kullanan programı bul
netstat -ano | findstr :3306

# Process'i sonlandır
taskkill /PID [PID_NUMARASI] /F
```

**Çözüm 2: Hata Loglarını Kontrol Et**
```bash
# MySQL hata logu
notepad G:\xampp\mysql\data\mysql_error.log
```

**Çözüm 3: MySQL'i Sıfırla**
```bash
# 1. MySQL'i durdur
G:\xampp\mysql_stop.bat

# 2. my.ini dosyasını kontrol et
notepad G:\xampp\mysql\bin\my.ini

# 3. MySQL'i başlat
G:\xampp\mysql_start.bat
```

---

### Yedek Alınamıyor

**Hata:** `mysqldump: command not found`

**Çözüm:**
```bash
# Tam path kullan
G:\xampp\mysql\bin\mysqldump.exe -u root pubg_community > yedek.sql
```

---

### Geri Yükleme Hatası

**Hata:** `ERROR 1064: Syntax error`

**Çözüm:**
```bash
# Karakter setini belirt
G:\xampp\mysql\bin\mysql.exe -u root --default-character-set=utf8mb4 pubg_community < yedek.sql
```

---

## 📊 Yedek Stratejisi (Önerilen)

### Günlük Yedek
- **Zaman:** Her gün 03:00
- **Saklama:** 7 gün
- **Konum:** `F:\Pubg\database-backups\daily\`

### Haftalık Yedek
- **Zaman:** Her Pazar 03:00
- **Saklama:** 4 hafta
- **Konum:** `F:\Pubg\database-backups\weekly\`

### Aylık Yedek
- **Zaman:** Her ayın 1'i 03:00
- **Saklama:** 12 ay
- **Konum:** `F:\Pubg\database-backups\monthly\`

---

## 🔐 Güvenlik İpuçları

1. ✅ Yedekleri farklı bir diske kaydet (örn: D:, E:)
2. ✅ Bulut depolama kullan (Google Drive, Dropbox)
3. ✅ Yedekleri şifrele (7-Zip ile)
4. ✅ Düzenli olarak geri yükleme testi yap
5. ✅ Kritik değişikliklerden önce manuel yedek al

---

## 📞 Acil Durum

### Veritabanı Tamamen Bozuldu

1. **Panik yapma!** 😊
2. **Son yedeği bul:** `F:\Pubg\database-backups\`
3. **Geri yükle:** `restore-database.bat [yedek_dosyası]`
4. **Test et:** `php artisan db:show`

---

## 📝 Yedek Dosya Formatları

### SQL Dump (.sql)
- ✅ Taşınabilir
- ✅ Metin tabanlı
- ✅ Versiyon kontrolü kolay
- ❌ Büyük dosyalar

### Dosya Yedeği (klasör)
- ✅ Hızlı
- ✅ Tam yedek
- ❌ Taşınabilir değil
- ❌ MySQL versiyonuna bağımlı

---

## 🎯 Hızlı Komutlar

```bash
# Yedek al
php artisan db:backup

# Geri yükle
restore-database.bat [dosya]

# MySQL durumunu kontrol et
php artisan db:show

# Migration'ları çalıştır
php artisan migrate

# Seeder'ları çalıştır
php artisan db:seed
```

---

## ✅ Mevcut Yedekler

**Alınan Yedek:**
- 📁 Konum: `F:\Pubg\database-backup-2025-12-02-083418`
- 📊 Boyut: 7.34 MB
- 📅 Tarih: 2 Aralık 2025, 08:34
- ✅ Durum: Başarılı

---

**Son Güncelleme:** 2 Aralık 2025  
**Versiyon:** 1.0
