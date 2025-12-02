<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Admin yetkilerini oluştur ve rollere ata.
     * 
     * Yetki Grupları:
     * - dashboard: Dashboard erişimi
     * - users: Kullanıcı yönetimi
     * - content: İçerik yönetimi
     * - moderation: Moderasyon
     * - analytics: Analitik
     * - settings: Sistem ayarları
     * - roles: Rol ve yetki yönetimi
     * - logs: Log yönetimi
     * - system: Sistem yönetimi
     */
    public function run(): void
    {
        $permissions = $this->getPermissions();

        // Yetkileri oluştur
        foreach ($permissions as $permissionData) {
            AdminPermission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }

        $this->command->info('✅ ' . count($permissions) . ' yetki başarıyla oluşturuldu!');

        // Rollere yetki ata
        $this->assignPermissionsToRoles();
    }

    /**
     * Tüm yetkileri tanımla
     */
    private function getPermissions(): array
    {
        return [
            // ==========================================
            // DASHBOARD (3 yetki)
            // ==========================================
            [
                'name' => 'Dashboard Görüntüleme',
                'slug' => 'dashboard.view',
                'group' => 'dashboard',
                'description' => 'Admin paneli dashboard\'unu görüntüleyebilir',
            ],
            [
                'name' => 'Dashboard İstatistikleri',
                'slug' => 'dashboard.stats',
                'group' => 'dashboard',
                'description' => 'Dashboard istatistiklerini görebilir',
            ],
            [
                'name' => 'Dashboard Grafikleri',
                'slug' => 'dashboard.charts',
                'group' => 'dashboard',
                'description' => 'Dashboard grafiklerini görebilir',
            ],

            // ==========================================
            // KULLANICI YÖNETİMİ (11 yetki)
            // ==========================================
            [
                'name' => 'Kullanıcı Listeleme',
                'slug' => 'users.view',
                'group' => 'users',
                'description' => 'Kullanıcı listesini görüntüleyebilir',
            ],
            [
                'name' => 'Kullanıcı Detay',
                'slug' => 'users.show',
                'group' => 'users',
                'description' => 'Kullanıcı detaylarını görüntüleyebilir',
            ],
            [
                'name' => 'Kullanıcı Düzenleme',
                'slug' => 'users.edit',
                'group' => 'users',
                'description' => 'Kullanıcı bilgilerini düzenleyebilir',
            ],
            [
                'name' => 'Kullanıcı Silme',
                'slug' => 'users.delete',
                'group' => 'users',
                'description' => 'Kullanıcıları silebilir',
            ],
            [
                'name' => 'Kullanıcı Banlama',
                'slug' => 'users.ban',
                'group' => 'users',
                'description' => 'Kullanıcıları banlayabilir/ban kaldırabilir',
            ],
            [
                'name' => 'Kullanıcı XP Yönetimi',
                'slug' => 'users.xp',
                'group' => 'users',
                'description' => 'Kullanıcılara manuel XP ekleyebilir/çıkarabilir',
            ],
            [
                'name' => 'Kullanıcı Rozet Yönetimi',
                'slug' => 'users.badges',
                'group' => 'users',
                'description' => 'Kullanıcılara rozet verebilir/alabilir',
            ],
            [
                'name' => 'Kullanıcı Şifre Sıfırlama',
                'slug' => 'users.reset-password',
                'group' => 'users',
                'description' => 'Kullanıcı şifrelerini sıfırlayabilir',
            ],
            [
                'name' => 'Toplu Kullanıcı İşlemleri',
                'slug' => 'users.bulk-actions',
                'group' => 'users',
                'description' => 'Toplu kullanıcı işlemleri yapabilir',
            ],
            [
                'name' => 'Kullanıcı Export',
                'slug' => 'users.export',
                'group' => 'users',
                'description' => 'Kullanıcı listesini export edebilir',
            ],
            [
                'name' => 'Kullanıcı Rol Yönetimi',
                'slug' => 'users.manage_roles',
                'group' => 'users',
                'description' => 'Kullanıcılara admin rolü verebilir/alabilir',
            ],

            // ==========================================
            // PROFİL YÖNETİMİ (4 yetki)
            // ==========================================
            [
                'name' => 'Profil Listeleme',
                'slug' => 'profiles.view',
                'group' => 'profiles',
                'description' => 'Profilleri görüntüleyebilir',
            ],
            [
                'name' => 'Profil Düzenleme',
                'slug' => 'profiles.edit',
                'group' => 'profiles',
                'description' => 'Profilleri düzenleyebilir',
            ],
            [
                'name' => 'Profil Doğrulama',
                'slug' => 'profiles.verify',
                'group' => 'profiles',
                'description' => 'Profilleri doğrulayabilir',
            ],
            [
                'name' => 'Profil Silme',
                'slug' => 'profiles.delete',
                'group' => 'profiles',
                'description' => 'Profilleri silebilir',
            ],

            // ==========================================
            // CİHAZ YÖNETİMİ (4 yetki)
            // ==========================================
            [
                'name' => 'Cihaz Listeleme',
                'slug' => 'devices.view',
                'group' => 'devices',
                'description' => 'Cihazları görüntüleyebilir',
            ],
            [
                'name' => 'Cihaz Düzenleme',
                'slug' => 'devices.edit',
                'group' => 'devices',
                'description' => 'Cihazları düzenleyebilir',
            ],
            [
                'name' => 'Cihaz Silme',
                'slug' => 'devices.delete',
                'group' => 'devices',
                'description' => 'Cihazları silebilir',
            ],
            [
                'name' => 'Cihaz Oluşturma',
                'slug' => 'devices.create',
                'group' => 'devices',
                'description' => 'Yeni cihaz ekleyebilir',
            ],

            // ==========================================
            // XP & BADGES YÖNETİMİ (3 yetki)
            // ==========================================
            [
                'name' => 'XP Görüntüleme',
                'slug' => 'xp.view',
                'group' => 'xp',
                'description' => 'XP ve rozet bilgilerini görüntüleyebilir',
            ],
            [
                'name' => 'XP Yönetimi',
                'slug' => 'xp.manage',
                'group' => 'xp',
                'description' => 'XP ve rozetleri yönetebilir',
            ],
            [
                'name' => 'Rozet Yönetimi',
                'slug' => 'xp.badges',
                'group' => 'xp',
                'description' => 'Rozetleri oluşturabilir/düzenleyebilir/silebilir',
            ],

            // ==========================================
            // OYUN YÖNETİMİ (3 yetki)
            // ==========================================
            [
                'name' => 'Oyun Listeleme',
                'slug' => 'games.view',
                'group' => 'games',
                'description' => 'Oyunları görüntüleyebilir',
            ],
            [
                'name' => 'Oyun Yönetimi',
                'slug' => 'games.manage',
                'group' => 'games',
                'description' => 'Oyunları oluşturabilir/düzenleyebilir/silebilir',
            ],
            [
                'name' => 'Oyun Silme',
                'slug' => 'games.delete',
                'group' => 'games',
                'description' => 'Oyunları silebilir',
            ],

            // ==========================================
            // SAYFA YÖNETİMİ (CMS) (3 yetki)
            // ==========================================
            [
                'name' => 'Sayfa Listeleme',
                'slug' => 'pages.view',
                'group' => 'pages',
                'description' => 'Sayfaları görüntüleyebilir',
            ],
            [
                'name' => 'Sayfa Yönetimi',
                'slug' => 'pages.manage',
                'group' => 'pages',
                'description' => 'Sayfaları oluşturabilir/düzenleyebilir/silebilir',
            ],
            [
                'name' => 'Sayfa Yayınlama',
                'slug' => 'pages.publish',
                'group' => 'pages',
                'description' => 'Sayfaları yayınlayabilir/yayından kaldırabilir',
            ],

            // ==========================================
            // MENÜ YÖNETİMİ (5 yetki)
            // ==========================================
            [
                'name' => 'Menü Listeleme',
                'slug' => 'menus.view',
                'group' => 'menus',
                'description' => 'Menüleri görüntüleyebilir',
            ],
            [
                'name' => 'Menü Oluşturma',
                'slug' => 'menus.create',
                'group' => 'menus',
                'description' => 'Yeni menü oluşturabilir',
            ],
            [
                'name' => 'Menü Düzenleme',
                'slug' => 'menus.edit',
                'group' => 'menus',
                'description' => 'Menüleri ve menü öğelerini düzenleyebilir',
            ],
            [
                'name' => 'Menü Silme',
                'slug' => 'menus.delete',
                'group' => 'menus',
                'description' => 'Menüleri silebilir',
            ],
            [
                'name' => 'Menü Yönetimi',
                'slug' => 'menus.manage',
                'group' => 'menus',
                'description' => 'Tüm menü işlemlerini yapabilir',
            ],

            // ==========================================
            // İÇERİK YÖNETİMİ (15 yetki)
            // ==========================================
            // LFG İlanları
            [
                'name' => 'LFG Listeleme',
                'slug' => 'lfg.view',
                'group' => 'content',
                'description' => 'LFG ilanlarını görüntüleyebilir',
            ],
            [
                'name' => 'LFG Düzenleme',
                'slug' => 'lfg.edit',
                'group' => 'content',
                'description' => 'LFG ilanlarını düzenleyebilir',
            ],
            [
                'name' => 'LFG Silme',
                'slug' => 'lfg.delete',
                'group' => 'content',
                'description' => 'LFG ilanlarını silebilir',
            ],

            // Klanlar
            [
                'name' => 'Klan Listeleme',
                'slug' => 'clans.view',
                'group' => 'content',
                'description' => 'Klanları görüntüleyebilir',
            ],
            [
                'name' => 'Klan Düzenleme',
                'slug' => 'clans.edit',
                'group' => 'content',
                'description' => 'Klanları düzenleyebilir',
            ],
            [
                'name' => 'Klan Silme',
                'slug' => 'clans.delete',
                'group' => 'content',
                'description' => 'Klanları silebilir',
            ],
            [
                'name' => 'Klan Doğrulama',
                'slug' => 'clans.verify',
                'group' => 'content',
                'description' => 'Klanları doğrulayabilir (verified badge)',
            ],

            // Rehberler
            [
                'name' => 'Rehber Listeleme',
                'slug' => 'guides.view',
                'group' => 'content',
                'description' => 'Rehberleri görüntüleyebilir',
            ],
            [
                'name' => 'Rehber Oluşturma',
                'slug' => 'guides.create',
                'group' => 'content',
                'description' => 'Yeni rehber oluşturabilir',
            ],
            [
                'name' => 'Rehber Düzenleme',
                'slug' => 'guides.edit',
                'group' => 'content',
                'description' => 'Rehberleri düzenleyebilir',
            ],
            [
                'name' => 'Rehber Silme',
                'slug' => 'guides.delete',
                'group' => 'content',
                'description' => 'Rehberleri silebilir',
            ],
            [
                'name' => 'Rehber Yayınlama',
                'slug' => 'guides.publish',
                'group' => 'content',
                'description' => 'Rehberleri yayınlayabilir/yayından kaldırabilir',
            ],

            // Topluluk Gönderileri
            [
                'name' => 'Gönderi Listeleme',
                'slug' => 'community.view',
                'group' => 'content',
                'description' => 'Topluluk gönderilerini görüntüleyebilir',
            ],
            [
                'name' => 'Gönderi Düzenleme',
                'slug' => 'community.edit',
                'group' => 'content',
                'description' => 'Topluluk gönderilerini düzenleyebilir',
            ],
            [
                'name' => 'Gönderi Silme',
                'slug' => 'community.delete',
                'group' => 'content',
                'description' => 'Topluluk gönderilerini silebilir',
            ],

            // ==========================================
            // MODERASYON (11 yetki)
            // ==========================================
            [
                'name' => 'Rapor Görüntüleme',
                'slug' => 'reports.view',
                'group' => 'moderation',
                'description' => 'Raporları görüntüleyebilir',
            ],
            [
                'name' => 'Rapor Yönetimi',
                'slug' => 'reports.manage',
                'group' => 'moderation',
                'description' => 'Raporları yönetebilir (onayla/reddet)',
            ],
            [
                'name' => 'Rapor İşleme',
                'slug' => 'reports.process',
                'group' => 'moderation',
                'description' => 'Raporları işleyebilir (onayla/reddet)',
            ],
            [
                'name' => 'Rapor Silme',
                'slug' => 'reports.delete',
                'group' => 'moderation',
                'description' => 'Raporları silebilir',
            ],
            [
                'name' => 'Moderasyon Kuyruğu',
                'slug' => 'moderation.queue',
                'group' => 'moderation',
                'description' => 'Moderasyon kuyruğunu görüntüleyebilir',
            ],
            [
                'name' => 'İçerik Onaylama',
                'slug' => 'moderation.approve',
                'group' => 'moderation',
                'description' => 'Bekleyen içerikleri onaylayabilir',
            ],
            [
                'name' => 'İçerik Reddetme',
                'slug' => 'moderation.reject',
                'group' => 'moderation',
                'description' => 'Bekleyen içerikleri reddedebilir',
            ],
            [
                'name' => 'Otomatik Moderasyon Kuralları',
                'slug' => 'moderation.rules',
                'group' => 'moderation',
                'description' => 'Otomatik moderasyon kurallarını yönetebilir',
            ],
            [
                'name' => 'Yorum Moderasyonu',
                'slug' => 'comments.moderate',
                'group' => 'moderation',
                'description' => 'Yorumları moderasyon yapabilir',
            ],
            [
                'name' => 'Spam İşaretleme',
                'slug' => 'moderation.spam',
                'group' => 'moderation',
                'description' => 'İçerikleri spam olarak işaretleyebilir',
            ],
            [
                'name' => 'İçerik Gizleme',
                'slug' => 'moderation.hide',
                'group' => 'moderation',
                'description' => 'İçerikleri gizleyebilir/gösterebilir',
            ],

            // ==========================================
            // ANALİTİK (5 yetki)
            // ==========================================
            [
                'name' => 'Analitik Görüntüleme',
                'slug' => 'analytics.view',
                'group' => 'analytics',
                'description' => 'Analitik sayfalarını görüntüleyebilir',
            ],
            [
                'name' => 'Kullanıcı Analitiği',
                'slug' => 'analytics.users',
                'group' => 'analytics',
                'description' => 'Kullanıcı analitiğini görüntüleyebilir',
            ],
            [
                'name' => 'İçerik Analitiği',
                'slug' => 'analytics.content',
                'group' => 'analytics',
                'description' => 'İçerik analitiğini görüntüleyebilir',
            ],
            [
                'name' => 'Platform Analitiği',
                'slug' => 'analytics.platform',
                'group' => 'analytics',
                'description' => 'Platform analitiğini görüntüleyebilir',
            ],
            [
                'name' => 'Rapor Export',
                'slug' => 'analytics.export',
                'group' => 'analytics',
                'description' => 'Analitik raporlarını export edebilir',
            ],

            // ==========================================
            // AYARLAR (9 yetki)
            // ==========================================
            [
                'name' => 'Ayarlar Görüntüleme',
                'slug' => 'settings.view',
                'group' => 'settings',
                'description' => 'Sistem ayarlarını görüntüleyebilir',
            ],
            [
                'name' => 'Ayarlar Yönetimi',
                'slug' => 'settings.manage',
                'group' => 'settings',
                'description' => 'Sistem ayarlarını düzenleyebilir',
            ],
            [
                'name' => 'Genel Ayarlar',
                'slug' => 'settings.general',
                'group' => 'settings',
                'description' => 'Genel ayarları düzenleyebilir',
            ],
            [
                'name' => 'Güvenlik Ayarları',
                'slug' => 'settings.security',
                'group' => 'settings',
                'description' => 'Güvenlik ayarlarını düzenleyebilir',
            ],
            [
                'name' => 'İçerik Ayarları',
                'slug' => 'settings.content',
                'group' => 'settings',
                'description' => 'İçerik ayarlarını düzenleyebilir',
            ],
            [
                'name' => 'Bildirim Ayarları',
                'slug' => 'settings.notifications',
                'group' => 'settings',
                'description' => 'Bildirim ayarlarını düzenleyebilir',
            ],
            [
                'name' => 'XP Ayarları',
                'slug' => 'settings.xp',
                'group' => 'settings',
                'description' => 'XP ve gamification ayarlarını düzenleyebilir',
            ],
            [
                'name' => 'API Ayarları',
                'slug' => 'settings.api',
                'group' => 'settings',
                'description' => 'API ayarlarını düzenleyebilir',
            ],
            [
                'name' => 'Bakım Modu',
                'slug' => 'settings.maintenance',
                'group' => 'settings',
                'description' => 'Bakım modunu açabilir/kapatabilir',
            ],

            // ==========================================
            // ROL VE YETKİ YÖNETİMİ (6 yetki)
            // ==========================================
            [
                'name' => 'Rol Listeleme',
                'slug' => 'roles.view',
                'group' => 'roles',
                'description' => 'Rolleri görüntüleyebilir',
            ],
            [
                'name' => 'Rol Oluşturma',
                'slug' => 'roles.create',
                'group' => 'roles',
                'description' => 'Yeni rol oluşturabilir',
            ],
            [
                'name' => 'Rol Düzenleme',
                'slug' => 'roles.edit',
                'group' => 'roles',
                'description' => 'Rolleri düzenleyebilir',
            ],
            [
                'name' => 'Rol Silme',
                'slug' => 'roles.delete',
                'group' => 'roles',
                'description' => 'Rolleri silebilir (sistem rolleri hariç)',
            ],
            [
                'name' => 'Yetki Görüntüleme',
                'slug' => 'permissions.view',
                'group' => 'roles',
                'description' => 'Yetkileri görüntüleyebilir',
            ],
            [
                'name' => 'Yetki Atama',
                'slug' => 'permissions.assign',
                'group' => 'roles',
                'description' => 'Rollere ve kullanıcılara yetki atayabilir',
            ],

            // ==========================================
            // LOG YÖNETİMİ (5 yetki)
            // ==========================================
            [
                'name' => 'Log Görüntüleme',
                'slug' => 'logs.view',
                'group' => 'logs',
                'description' => 'Logları görüntüleyebilir',
            ],
            [
                'name' => 'Log Yönetimi',
                'slug' => 'logs.manage',
                'group' => 'logs',
                'description' => 'Logları yönetebilir (temizleme, vb.)',
            ],
            [
                'name' => 'Admin Aktivite Logları',
                'slug' => 'logs.admin-activity',
                'group' => 'logs',
                'description' => 'Admin aktivite loglarını görüntüleyebilir',
            ],
            [
                'name' => 'Sistem Logları',
                'slug' => 'logs.system',
                'group' => 'logs',
                'description' => 'Sistem loglarını görüntüleyebilir',
            ],
            [
                'name' => 'Log Temizleme',
                'slug' => 'logs.delete',
                'group' => 'logs',
                'description' => 'Logları temizleyebilir',
            ],

            // ==========================================
            // WİDGET YÖNETİMİ (5 yetki)
            // ==========================================
            [
                'name' => 'Widget Listeleme',
                'slug' => 'widgets.view',
                'group' => 'widgets',
                'description' => 'Widget\'ları görüntüleyebilir',
            ],
            [
                'name' => 'Widget Oluşturma',
                'slug' => 'widgets.create',
                'group' => 'widgets',
                'description' => 'Yeni widget oluşturabilir',
            ],
            [
                'name' => 'Widget Düzenleme',
                'slug' => 'widgets.edit',
                'group' => 'widgets',
                'description' => 'Widget\'ları düzenleyebilir',
            ],
            [
                'name' => 'Widget Silme',
                'slug' => 'widgets.delete',
                'group' => 'widgets',
                'description' => 'Widget\'ları silebilir',
            ],
            [
                'name' => 'Widget Yönetimi',
                'slug' => 'widgets.manage',
                'group' => 'widgets',
                'description' => 'Tüm widget işlemlerini yapabilir',
            ],

            // ==========================================
            // BANNER YÖNETİMİ (6 yetki)
            // ==========================================
            [
                'name' => 'Banner Listeleme',
                'slug' => 'banners.view',
                'group' => 'banners',
                'description' => 'Banner\'ları görüntüleyebilir',
            ],
            [
                'name' => 'Banner Oluşturma',
                'slug' => 'banners.create',
                'group' => 'banners',
                'description' => 'Yeni banner oluşturabilir',
            ],
            [
                'name' => 'Banner Düzenleme',
                'slug' => 'banners.edit',
                'group' => 'banners',
                'description' => 'Banner\'ları düzenleyebilir',
            ],
            [
                'name' => 'Banner Silme',
                'slug' => 'banners.delete',
                'group' => 'banners',
                'description' => 'Banner\'ları silebilir',
            ],
            [
                'name' => 'Banner A/B Test',
                'slug' => 'banners.ab-test',
                'group' => 'banners',
                'description' => 'Banner A/B testleri oluşturabilir ve yönetebilir',
            ],
            [
                'name' => 'Banner İstatistikleri',
                'slug' => 'banners.statistics',
                'group' => 'banners',
                'description' => 'Banner istatistiklerini görüntüleyebilir',
            ],

            // ==========================================
            // SİSTEM YÖNETİMİ (8 yetki)
            // ==========================================
            [
                'name' => 'Sistem Bilgileri',
                'slug' => 'system.info',
                'group' => 'system',
                'description' => 'Sistem bilgilerini görüntüleyebilir',
            ],
            [
                'name' => 'Cache Yönetimi',
                'slug' => 'system.cache',
                'group' => 'system',
                'description' => 'Cache\'i yönetebilir (temizleme, vb.)',
            ],
            [
                'name' => 'Yedekleme',
                'slug' => 'system.backup',
                'group' => 'system',
                'description' => 'Sistem yedeği alabilir',
            ],
            [
                'name' => 'Yedek Geri Yükleme',
                'slug' => 'system.restore',
                'group' => 'system',
                'description' => 'Yedekten geri yükleme yapabilir',
            ],
            [
                'name' => 'Veritabanı Yönetimi',
                'slug' => 'system.database',
                'group' => 'system',
                'description' => 'Veritabanı işlemleri yapabilir',
            ],
            [
                'name' => 'Performans İzleme',
                'slug' => 'system.performance',
                'group' => 'system',
                'description' => 'Sistem performansını izleyebilir',
            ],
            [
                'name' => 'Bildirim Gönderimi',
                'slug' => 'system.notifications',
                'group' => 'system',
                'description' => 'Toplu bildirim gönderebilir',
            ],
            [
                'name' => 'CMS Yönetimi',
                'slug' => 'system.cms',
                'group' => 'system',
                'description' => 'CMS (sayfa, menü, widget, banner) yönetebilir',
            ],

            // ==========================================
            // YEDEKLEME YÖNETİMİ (6 yetki)
            // ==========================================
            [
                'name' => 'Yedekleme Görüntüleme',
                'slug' => 'backup.view',
                'group' => 'backup',
                'description' => 'Yedekleri görüntüleyebilir',
            ],
            [
                'name' => 'Yedekleme Oluşturma',
                'slug' => 'backup.create',
                'group' => 'backup',
                'description' => 'Yeni yedek oluşturabilir',
            ],
            [
                'name' => 'Yedekleme Geri Yükleme',
                'slug' => 'backup.restore',
                'group' => 'backup',
                'description' => 'Yedekten geri yükleme yapabilir',
            ],
            [
                'name' => 'Yedekleme İndirme',
                'slug' => 'backup.download',
                'group' => 'backup',
                'description' => 'Yedekleri indirebilir',
            ],
            [
                'name' => 'Yedekleme Silme',
                'slug' => 'backup.delete',
                'group' => 'backup',
                'description' => 'Yedekleri silebilir',
            ],
            [
                'name' => 'Yedekleme Tam Yönetimi',
                'slug' => 'backup.manage',
                'group' => 'backup',
                'description' => 'Tüm yedekleme işlemlerini yapabilir',
            ],
        ];
    }

    /**
     * Rollere yetki ata
     */
    private function assignPermissionsToRoles(): void
    {
        $superAdmin = AdminRole::where('slug', 'super_admin')->first();
        $moderator = AdminRole::where('slug', 'moderator')->first();
        $contentManager = AdminRole::where('slug', 'content_manager')->first();

        if (!$superAdmin || !$moderator || !$contentManager) {
            $this->command->error('❌ Roller bulunamadı! Önce AdminRoleSeeder\'ı çalıştırın.');
            return;
        }

        // Süper Admin - TÜM YETKİLER
        $allPermissions = AdminPermission::all()->pluck('id')->toArray();
        $superAdmin->permissions()->sync($allPermissions);
        $this->command->info('✅ Süper Admin\'e ' . count($allPermissions) . ' yetki atandı (TÜM YETKİLER)');

        // Moderatör - Moderasyon, kullanıcı yönetimi, içerik görüntüleme
        $moderatorPermissions = AdminPermission::whereIn('group', [
            'dashboard',
            'users',
            'moderation',
            'analytics',
            'logs',
        ])->orWhereIn('slug', [
            'lfg.view',
            'lfg.edit',
            'lfg.delete',
            'clans.view',
            'clans.edit',
            'clans.delete',
            'guides.view',
            'community.view',
            'community.edit',
            'community.delete',
            'profiles.view',
            'profiles.verify',
            'devices.view',
            'reports.view',
            'reports.manage',
        ])->pluck('id')->toArray();
        $moderator->permissions()->sync($moderatorPermissions);
        $this->command->info('✅ Moderatör\'e ' . count($moderatorPermissions) . ' yetki atandı');

        // İçerik Yöneticisi - İçerik oluşturma, düzenleme, yayınlama
        $contentManagerPermissions = AdminPermission::whereIn('group', [
            'dashboard',
            'content',
            'widgets',
            'banners',
        ])->orWhereIn('slug', [
            'users.view',
            'users.show',
            'analytics.view',
            'analytics.content',
            'system.cms',
            'pages.view',
            'pages.manage',
            'pages.publish',
            'menus.view',
            'menus.create',
            'menus.edit',
            'menus.delete',
            'menus.manage',
            'guides.create',
            'guides.edit',
            'guides.publish',
        ])->pluck('id')->toArray();
        $contentManager->permissions()->sync($contentManagerPermissions);
        $this->command->info('✅ İçerik Yöneticisi\'ne ' . count($contentManagerPermissions) . ' yetki atandı');

        $this->command->info('');
        $this->command->info('🎉 Tüm yetkiler başarıyla rollere atandı!');
    }
}
