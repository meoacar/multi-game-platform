# Veri Bütünlüğü Kontrol Kılavuzu

## Genel Bakış

Multi-game platform migration'ı sonrasında mevcut kullanıcı verilerinin ve ilişkilerinin korunduğunu doğrulamak için araçlar ve testler.

## Veri Bütünlüğü Kontrol Aracı

### Kullanım

```bash
# Veri bütünlüğünü kontrol et
php artisan multi-game:verify-data

# Detaylı rapor ile kontrol et
php artisan multi-game:verify-data --detailed

# Tespit edilen sorunları otomatik düzelt
php artisan multi-game:verify-data --fix
```

### Kontrol Edilen Öğeler

#### 1. Kullanıcı Verileri
- ✓ Tüm kullanıcıların profile'ı var mı?
- ✓ Email adresleri unique mi?
- ✓ Kullanıcı sayısı korunmuş mu?

#### 2. İlişkiler
- ✓ User -> Profile ilişkisi çalışıyor mu?
- ✓ Tournament -> User (organizer) ilişkisi çalışıyor mu?
- ✓ Clan -> User (leader) ilişkisi çalışıyor mu?
- ✓ Orphaned records var mı?

#### 3. Oyuna Özel Veriler
- ✓ Tüm tournaments'ların game_id'si var mı?
- ✓ Tüm clans'ların game_id'si var mı?
- ✓ Tüm lfg_posts'ların game_id'si var mı?
- ✓ Tüm community_posts'ların game_id'si var mı?
- ✓ Tüm guide_posts'ların game_id'si var mı?

#### 4. Cross-Game Özellikler
- ✓ Friendships game_id içermiyor mu? (cross-game olmalı)
- ✓ Messages game_id içermiyor mu? (cross-game olmalı)
- ✓ Users game_id içermiyor mu? (cross-game olmalı)

#### 5. Orphaned Records
- ✓ Geçersiz game_id'li kayıtlar var mı?
- ✓ Tüm foreign key'ler geçerli mi?

## Otomatik Düzeltmeler

`--fix` flag'i ile çalıştırıldığında, aşağıdaki sorunlar otomatik düzeltilir:

1. **Profile'sız Kullanıcılar**: Eksik profile'lar otomatik oluşturulur
2. **Orphaned Profiles**: Sahibi olmayan profile'lar silinir
3. **Eksik game_id'ler**: Mevcut kayıtlara game_id=1 (PUBG) atanır

## Test Senaryoları

### Çalıştırma

```bash
php artisan test --filter=DataIntegrityTest
```

### Test Edilen Senaryolar

1. **Kullanıcı Verilerinin Korunması**
   - Migration sonrası kullanıcı verileri değişmemeli
   - Profile ilişkileri korunmalı

2. **Kullanıcı İlişkilerinin Korunması**
   - Arkadaşlıklar korunmalı
   - Mesajlar korunmalı
   - Cross-game özellikler çalışmalı

3. **Game ID Ataması**
   - Oyuna özel varlıklar game_id almalı
   - Game ilişkileri çalışmalı

4. **Foreign Key Bütünlüğü**
   - İlişkiler korunmalı
   - Cascade delete'ler çalışmalı

5. **Sorun Tespiti**
   - Profile'sız kullanıcılar tespit edilmeli
   - Eksik game_id'ler tespit edilmeli

6. **Otomatik Düzeltme**
   - Profile'sız kullanıcılar düzeltilmeli
   - Eksik game_id'ler düzeltilmeli

7. **Başarı Durumu**
   - Sorun yoksa başarılı rapor vermeli

8. **Cross-Game Özelliklerin Korunması**
   - Arkadaşlıklar oyunlar arası çalışmalı
   - Mesajlar oyunlar arası çalışmalı

## Migration Öncesi Kontrol Listesi

Migration'ı çalıştırmadan önce:

- [ ] Veritabanı yedeği alındı mı?
- [ ] Test ortamında migration test edildi mi?
- [ ] Veri bütünlüğü kontrol aracı test edildi mi?
- [ ] Rollback planı hazır mı?

## Migration Sonrası Kontrol Listesi

Migration'dan sonra:

- [ ] `php artisan multi-game:verify-data` çalıştırıldı mı?
- [ ] Tüm kontroller başarılı mı?
- [ ] Kullanıcı verileri korunmuş mu?
- [ ] İlişkiler çalışıyor mu?
- [ ] Cross-game özellikler çalışıyor mu?

## Sorun Giderme

### Profile'sız Kullanıcılar

```bash
# Tespit et
php artisan multi-game:verify-data

# Düzelt
php artisan multi-game:verify-data --fix
```

### Eksik game_id'ler

```bash
# Tespit et
php artisan multi-game:verify-data

# Düzelt (game_id=1 atar)
php artisan multi-game:verify-data --fix
```

### Orphaned Records

```bash
# Tespit et
php artisan multi-game:verify-data --detailed

# Manuel kontrol
SELECT * FROM profiles WHERE user_id NOT IN (SELECT id FROM users);
SELECT * FROM tournaments WHERE game_id NOT IN (SELECT id FROM games);
```

## Önemli Notlar

1. **Yedekleme**: Her zaman migration öncesi tam veritabanı yedeği alın
2. **Test Ortamı**: Önce test ortamında deneyin
3. **Rollback**: Sorun çıkarsa rollback planınız hazır olsun
4. **Monitoring**: Migration sonrası sistemi yakından izleyin
5. **Cross-Game**: Friendships ve Messages tablolarında game_id OLMAMALI

## İletişim

Sorun yaşarsanız:
1. Veri bütünlüğü raporunu kaydedin
2. Hata loglarını kontrol edin
3. Geliştirici ekiple iletişime geçin
