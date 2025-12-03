# 📋 Multi-Game Platform Deployment Checklist

## 🔴 KRİTİK: Deployment Öncesi (T-24 saat)

- [ ] **Kullanıcılara duyuru yapıldı** (bakım saati bildirildi)
- [ ] **Veritabanı yedeği alındı** (`backup-database.bat`)
- [ ] **Yedek dosyası doğrulandı** (dosya boyutu ve içeriği kontrol edildi)
- [ ] **Disk alanı kontrol edildi** (minimum 2GB boş alan)
- [ ] **Test ortamında deployment test edildi**
- [ ] **Rollback planı hazır**

## 🟡 Deployment Öncesi (T-1 saat)

- [ ] **Tüm ekip üyeleri hazır**
- [ ] **Bakım sayfası hazır**
- [ ] **Monitoring araçları aktif**
- [ ] **Son veritabanı yedeği alındı**
- [ ] **Git repository güncel** (`git pull origin main`)

## 🟢 Deployment Adımları

### 1. Hazırlık (5 dakika)

- [ ] Terminal/CMD açıldı
- [ ] Proje dizinine gidildi: `cd F:\Pubg\pubg-community`
- [ ] PHP versiyonu kontrol edildi: `php -v`
- [ ] Composer kontrol edildi: `composer --version`
- [ ] MySQL çalışıyor: `php artisan tinker --execute="DB::connection()->getPdo();"`

### 2. Yedekleme (2 dakika)

- [ ] Yedek alındı: `backup-database.bat`
- [ ] Yedek dosyası oluşturuldu: `database-backups/pubg_community_*.sql`
- [ ] Dosya boyutu kontrol edildi (>0 bytes)

### 3. Maintenance Modu (1 dakika)

- [ ] Maintenance modu aktif: `php artisan down --retry=60`
- [ ] Web sitesi bakım sayfası gösteriyor

### 4. Kod Güncelleme (3 dakika)

- [ ] Git pull: `git pull origin main`
- [ ] Composer install: `composer install --no-dev --optimize-autoloader`
- [ ] Hata yok

### 5. Cache Temizleme (1 dakika)

- [ ] Config cache: `php artisan config:clear`
- [ ] Route cache: `php artisan route:clear`
- [ ] View cache: `php artisan view:clear`
- [ ] Application cache: `php artisan cache:clear`

### 6. Migration (5 dakika) ⚠️ KRİTİK

- [ ] Migration durumu kontrol edildi: `php artisan migrate:status`
- [ ] Migration çalıştırıldı: `php artisan migrate --force`
- [ ] Hata yok
- [ ] games tablosu oluşturuldu
- [ ] game_id kolonları eklendi

**Eğer hata varsa:**
- [ ] Rollback yapıldı: `rollback-multi-game.bat`
- [ ] Deployment durduruldu

### 7. Seeder (2 dakika)

- [ ] GameSeeder çalıştırıldı: `php artisan db:seed --class=GameSeeder --force`
- [ ] PUBG oyunu oluşturuldu
- [ ] Mevcut veriler game_id=1 aldı

### 8. Cache Optimize (2 dakika)

- [ ] Config cache: `php artisan config:cache`
- [ ] Route cache: `php artisan route:cache`
- [ ] View cache: `php artisan view:cache`

### 9. Doğrulama (5 dakika) ⚠️ KRİTİK

- [ ] Doğrulama script'i çalıştırıldı: `verify-deployment.bat`
- [ ] Tüm kontroller başarılı
- [ ] Hata sayısı: 0
- [ ] Uyarı sayısı: kabul edilebilir

**Doğrulama Kontrolleri:**
- [ ] Veritabanı bağlantısı: OK
- [ ] games tablosu: EXISTS
- [ ] game_id kolonları: EXISTS
- [ ] Foreign keys: EXISTS
- [ ] Mevcut veriler: KORUNMUŞ
- [ ] Game model: ÇALIŞIYOR
- [ ] GameScope: ÇALIŞIYOR
- [ ] Config dosyaları: MEVCUT
- [ ] Middleware: MEVCUT
- [ ] Service sınıfları: MEVCUT

### 10. Maintenance Modunu Kapat (1 dakika)

- [ ] Maintenance modu kapatıldı: `php artisan up`
- [ ] Web sitesi açılıyor

## 🔵 Deployment Sonrası (İlk 15 dakika)

### Manuel Testler

- [ ] **Ana sayfa açılıyor:** `http://takimsistemi.com`
- [ ] **PUBG subdomain çalışıyor:** `http://pubg.takimsistemi.com`
- [ ] **Kullanıcı girişi yapılabiliyor**
- [ ] **Dashboard açılıyor**
- [ ] **Turnuva listesi görünüyor**
- [ ] **Klan listesi görünüyor**
- [ ] **LFG postları görünüyor**
- [ ] **Oyun değiştirici çalışıyor**
- [ ] **Profil sayfası açılıyor**
- [ ] **Mesajlaşma çalışıyor**

### Veri Kontrolleri

- [ ] Kullanıcı sayısı korunmuş: `php artisan tinker --execute="App\Models\User::count()"`
- [ ] Turnuva sayısı korunmuş: `php artisan tinker --execute="App\Models\Tournament::withoutGlobalScope(App\Models\Scopes\GameScope::class)->count()"`
- [ ] Klan sayısı korunmuş: `php artisan tinker --execute="App\Models\Clan::withoutGlobalScope(App\Models\Scopes\GameScope::class)->count()"`

### Log Kontrolleri

- [ ] Laravel log kontrol edildi: `storage/logs/laravel.log`
- [ ] Kritik hata yok
- [ ] Veritabanı hatası yok
- [ ] Migration hatası yok

### Performans Kontrolleri

- [ ] Sayfa yüklenme süresi: <3 saniye
- [ ] Veritabanı sorgu sayısı: normal
- [ ] Memory kullanımı: normal
- [ ] CPU kullanımı: normal

## 🟣 Deployment Sonrası (İlk 1 saat)

- [ ] **Kullanıcı geri bildirimleri izleniyor**
- [ ] **Log dosyaları sürekli kontrol ediliyor**
- [ ] **Performans metrikleri izleniyor**
- [ ] **Hata raporları kontrol ediliyor**

## 🔴 Acil Durum: Rollback Gerekirse

### Hızlı Rollback

1. [ ] `rollback-multi-game.bat` çalıştır
2. [ ] Doğrulama yap
3. [ ] Kullanıcılara bilgi ver

### Veritabanı Geri Yükleme

1. [ ] `restore-database.bat [backup_file]` çalıştır
2. [ ] Doğrulama yap
3. [ ] Kullanıcılara bilgi ver

## 📊 Deployment Metrikleri

| Metrik | Hedef | Gerçekleşen | Durum |
|--------|-------|-------------|-------|
| Toplam Süre | <30 dakika | ___ dakika | ⬜ |
| Downtime | <10 dakika | ___ dakika | ⬜ |
| Hata Sayısı | 0 | ___ | ⬜ |
| Uyarı Sayısı | <3 | ___ | ⬜ |
| Veri Kaybı | 0 | ___ | ⬜ |

## 📝 Notlar

### Deployment Sırasında Karşılaşılan Sorunlar

```
[Buraya deployment sırasında karşılaşılan sorunları yazın]
```

### Çözümler

```
[Buraya uygulanan çözümleri yazın]
```

### İyileştirme Önerileri

```
[Buraya gelecek deployment'lar için önerileri yazın]
```

## ✅ Deployment Tamamlandı

- [ ] **Tüm kontroller başarılı**
- [ ] **Kullanıcılara bilgi verildi**
- [ ] **Dokümantasyon güncellendi**
- [ ] **Ekip bilgilendirildi**
- [ ] **Monitoring devam ediyor**

---

**Deployment Tarihi:** _______________  
**Deployment Saati:** _______________  
**Deployment Yapan:** _______________  
**Deployment Süresi:** ___ dakika  
**Downtime:** ___ dakika  
**Durum:** ⬜ Başarılı / ⬜ Başarısız / ⬜ Rollback

---

## 🎉 Başarılı Deployment Sonrası

Deployment başarılı olduysa:

1. ✅ Kullanıcılara "Sistem aktif" duyurusu yap
2. ✅ Ekibe teşekkür et
3. ✅ Deployment raporunu hazırla
4. ✅ Öğrenilen dersleri dokümante et
5. ✅ Bir sonraki deployment için iyileştirmeler planla

**Tebrikler! 🚀**

---

**Versiyon:** 1.0.0  
**Son Güncelleme:** 2025-12-03
