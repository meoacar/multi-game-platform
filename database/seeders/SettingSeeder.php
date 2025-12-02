<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Genel Ayarlar
            ['key' => 'site_name', 'value' => 'PUBG Mobile Topluluk', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Türkiye\'nin En Büyük PUBG Mobile Topluluğu', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'PUBG Mobile oyuncularını bir araya getiren Türk topluluğu', 'type' => 'string', 'group' => 'general'],
            ['key' => 'homepage_announcement', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_favicon', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'info@pubgmobile.com', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'social_facebook', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'social_twitter', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'social_instagram', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'social_discord', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'social_youtube', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'google_analytics_id', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'facebook_pixel_id', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'Europe/Istanbul', 'type' => 'string', 'group' => 'general'],
            ['key' => 'language', 'value' => 'tr', 'type' => 'string', 'group' => 'general'],
            ['key' => 'default_city', 'value' => 'İstanbul', 'type' => 'string', 'group' => 'general'],
            
            // Kayıt Ayarları
            ['key' => 'registration_open', 'value' => '1', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'require_email_verification', 'value' => '1', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'require_phone_verification', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'minimum_age', 'value' => '13', 'type' => 'integer', 'group' => 'security'],
            
            // Güvenlik Ayarları
            ['key' => 'recaptcha_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'recaptcha_site_key', 'value' => '', 'type' => 'string', 'group' => 'security'],
            ['key' => 'recaptcha_secret_key', 'value' => '', 'type' => 'string', 'group' => 'security'],
            ['key' => 'password_min_length', 'value' => '8', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'password_require_uppercase', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'password_require_lowercase', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'password_require_numbers', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'password_require_special', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'require_2fa_admin', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'lockout_duration', 'value' => '15', 'type' => 'integer', 'group' => 'security'],
            
            // İçerik Ayarları
            ['key' => 'content_moderation', 'value' => '0', 'type' => 'boolean', 'group' => 'content'],
            ['key' => 'auto_publish', 'value' => '1', 'type' => 'boolean', 'group' => 'content'],
            ['key' => 'max_upload_size', 'value' => '5', 'type' => 'integer', 'group' => 'content'],
            ['key' => 'allowed_file_types', 'value' => 'jpg,jpeg,png,gif,pdf', 'type' => 'string', 'group' => 'content'],
            ['key' => 'daily_post_limit', 'value' => '10', 'type' => 'integer', 'group' => 'content'],
            ['key' => 'daily_comment_limit', 'value' => '50', 'type' => 'integer', 'group' => 'content'],
            ['key' => 'spam_protection', 'value' => '1', 'type' => 'boolean', 'group' => 'content'],
            ['key' => 'profanity_filter', 'value' => '1', 'type' => 'boolean', 'group' => 'content'],
            ['key' => 'profanity_words', 'value' => '', 'type' => 'string', 'group' => 'content'],
            ['key' => 'auto_moderate_reports', 'value' => '5', 'type' => 'integer', 'group' => 'content'],
            
            // Bildirim Ayarları
            ['key' => 'email_notifications_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'sms_notifications_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'push_notifications_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'notification_frequency', 'value' => 'instant', 'type' => 'string', 'group' => 'notification'],
            ['key' => 'smtp_host', 'value' => '', 'type' => 'string', 'group' => 'notification'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'integer', 'group' => 'notification'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'string', 'group' => 'notification'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'string', 'group' => 'notification'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'notification'],
            ['key' => 'smtp_from_address', 'value' => '', 'type' => 'string', 'group' => 'notification'],
            ['key' => 'smtp_from_name', 'value' => 'PUBG Mobile Topluluk', 'type' => 'string', 'group' => 'notification'],
            ['key' => 'notification_new_user', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'notification_new_content', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'notification_new_report', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'notification_new_application', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            
            // XP Sistemi Ayarları
            ['key' => 'xp_system_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'xp'],
            ['key' => 'xp_profile_complete', 'value' => '50', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_post_create', 'value' => '10', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_comment_create', 'value' => '5', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_guide_create', 'value' => '25', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_clan_create', 'value' => '30', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_daily_login', 'value' => '5', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_per_level', 'value' => '100', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_level_multiplier', 'value' => '1.5', 'type' => 'string', 'group' => 'xp'],
            ['key' => 'leaderboard_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'xp'],
            ['key' => 'badges_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'xp'],
            ['key' => 'leaderboard_update_frequency', 'value' => '60', 'type' => 'integer', 'group' => 'xp'],
            
            // Eski XP ayarları (geriye dönük uyumluluk için)
            ['key' => 'xp_profile_completed', 'value' => '50', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_created_lfg_post', 'value' => '30', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_created_guide', 'value' => '40', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_first_login_of_day', 'value' => '10', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_sent_first_message', 'value' => '15', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_made_first_friend', 'value' => '20', 'type' => 'integer', 'group' => 'xp'],
            ['key' => 'xp_created_clan', 'value' => '100', 'type' => 'integer', 'group' => 'xp'],
            
            // Bakım Modu
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'maintenance'],
            ['key' => 'maintenance_message', 'value' => 'Sitemiz şu anda bakımdadır. Lütfen daha sonra tekrar deneyin.', 'type' => 'string', 'group' => 'maintenance'],
            ['key' => 'maintenance_eta', 'value' => '', 'type' => 'string', 'group' => 'maintenance'],
            ['key' => 'maintenance_allowed_ips', 'value' => '', 'type' => 'string', 'group' => 'maintenance'],
            
            // API Ayarları
            ['key' => 'api_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'api'],
            ['key' => 'api_rate_limit', 'value' => '60', 'type' => 'integer', 'group' => 'api'],
            ['key' => 'api_rate_limit_guest', 'value' => '20', 'type' => 'integer', 'group' => 'api'],
            ['key' => 'api_rate_limit_period', 'value' => '1', 'type' => 'integer', 'group' => 'api'],
            ['key' => 'api_require_authentication', 'value' => '0', 'type' => 'boolean', 'group' => 'api'],
            ['key' => 'webhooks_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'api'],
            ['key' => 'webhook_secret', 'value' => '', 'type' => 'string', 'group' => 'api'],
            ['key' => 'api_cors_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'api'],
            ['key' => 'api_cors_origins', 'value' => '*', 'type' => 'string', 'group' => 'api'],
            ['key' => 'api_docs_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'api'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
