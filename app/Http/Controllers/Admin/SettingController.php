<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    /**
     * Ayarlar sayfasını göster
     */
    public function index()
    {
        // Cache'den ayarları al veya veritabanından çek
        $settings = Cache::remember('settings.all', 3600, function () {
            return Setting::all()->keyBy('key');
        });
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Ayarları güncelle
     */
    public function update(Request $request)
    {
        // Validasyon kuralları
        $validated = $request->validate([
            // Genel Ayarlar
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'homepage_announcement' => 'nullable|string',
            'site_logo' => 'nullable|url|max:500',
            'site_favicon' => 'nullable|url|max:500',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_discord' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'google_analytics_id' => 'nullable|string|max:50',
            'facebook_pixel_id' => 'nullable|string|max:50',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            
            // Kayıt ve Güvenlik Ayarları
            'registration_open' => 'boolean',
            'require_email_verification' => 'boolean',
            'require_phone_verification' => 'boolean',
            'recaptcha_enabled' => 'boolean',
            'recaptcha_site_key' => 'nullable|string|max:100',
            'recaptcha_secret_key' => 'nullable|string|max:100',
            'minimum_age' => 'nullable|integer|min:13|max:99',
            'password_min_length' => 'nullable|integer|min:6|max:32',
            'password_require_uppercase' => 'boolean',
            'password_require_lowercase' => 'boolean',
            'password_require_numbers' => 'boolean',
            'password_require_symbols' => 'boolean',
            'password_require_special' => 'boolean',
            'two_factor_required_for_admins' => 'boolean',
            'require_2fa_admin' => 'boolean',
            'session_lifetime' => 'nullable|integer|min:5|max:10080',
            'max_login_attempts' => 'nullable|integer|min:3|max:20',
            'lockout_duration' => 'nullable|integer|min:1|max:1440',
            
            // İçerik Ayarları
            'content_moderation' => 'boolean',
            'auto_publish' => 'boolean',
            'max_upload_size' => 'nullable|integer|min:1|max:50',
            'allowed_file_types' => 'nullable|string|max:500',
            'daily_post_limit' => 'nullable|integer|min:1|max:100',
            'daily_comment_limit' => 'nullable|integer|min:1|max:500',
            'spam_protection' => 'boolean',
            'profanity_filter' => 'boolean',
            'profanity_words' => 'nullable|string',
            'auto_moderate_reports' => 'nullable|integer|min:1|max:50',
            
            // Bildirim Ayarları
            'email_notifications' => 'boolean',
            'email_notifications_enabled' => 'boolean',
            'sms_notifications' => 'boolean',
            'sms_notifications_enabled' => 'boolean',
            'push_notifications' => 'boolean',
            'push_notifications_enabled' => 'boolean',
            'notification_frequency' => 'nullable|string|in:instant,hourly,daily,weekly',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,none,',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name' => 'nullable|string|max:255',
            'notification_new_user' => 'boolean',
            'notification_new_content' => 'boolean',
            'notification_new_report' => 'boolean',
            'notification_new_application' => 'boolean',
            
            // XP ve Gamification Ayarları
            'xp_enabled' => 'boolean',
            'xp_system_enabled' => 'boolean',
            'xp_register' => 'nullable|integer|min:0|max:10000',
            'xp_login' => 'nullable|integer|min:0|max:1000',
            'xp_daily_login' => 'nullable|integer|min:0|max:1000',
            'xp_post_create' => 'nullable|integer|min:0|max:1000',
            'xp_comment_create' => 'nullable|integer|min:0|max:1000',
            'xp_like_receive' => 'nullable|integer|min:0|max:1000',
            'xp_guide_create' => 'nullable|integer|min:0|max:1000',
            'xp_clan_create' => 'nullable|integer|min:0|max:1000',
            'xp_lfg_create' => 'nullable|integer|min:0|max:1000',
            'xp_profile_complete' => 'nullable|integer|min:0|max:1000',
            'xp_per_level' => 'nullable|integer|min:100|max:100000',
            'xp_level_multiplier' => 'nullable|numeric|min:1|max:10',
            'badges_enabled' => 'boolean',
            'leaderboard_enabled' => 'boolean',
            'leaderboard_update_frequency' => 'nullable|integer|min:1|max:1440',
            
            // Bakım Modu
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string',
            'maintenance_eta' => 'nullable|string|max:255',
            'maintenance_allowed_ips' => 'nullable|string',
            
            // API Ayarları
            'api_enabled' => 'boolean',
            'api_rate_limit' => 'nullable|integer|min:10|max:10000',
            'api_rate_limit_guest' => 'nullable|integer|min:5|max:1000',
            'api_rate_limit_period' => 'nullable|integer|min:1|max:1440',
            'api_require_authentication' => 'boolean',
            'webhook_enabled' => 'boolean',
            'webhooks_enabled' => 'boolean',
            'webhook_secret' => 'nullable|string|max:255',
            'cors_enabled' => 'boolean',
            'api_cors_enabled' => 'boolean',
            'cors_allowed_origins' => 'nullable|string',
            'api_cors_origins' => 'nullable|string',
            'api_docs_enabled' => 'boolean',
        ]);

        // Boolean alanları düzelt (checkbox gönderilmezse false)
        $booleanFields = [
            'registration_open', 'require_email_verification', 'require_phone_verification',
            'recaptcha_enabled', 'password_require_uppercase', 'password_require_lowercase',
            'password_require_numbers', 'password_require_symbols', 'password_require_special',
            'two_factor_required_for_admins', 'require_2fa_admin',
            'content_moderation', 'auto_publish', 'spam_protection', 'profanity_filter',
            'email_notifications', 'email_notifications_enabled', 'sms_notifications', 
            'sms_notifications_enabled', 'push_notifications', 'push_notifications_enabled',
            'notification_new_user', 'notification_new_content', 'notification_new_report',
            'notification_new_application', 'xp_enabled', 'xp_system_enabled', 'badges_enabled', 
            'leaderboard_enabled', 'maintenance_mode', 'api_enabled', 'api_require_authentication',
            'webhook_enabled', 'webhooks_enabled', 'cors_enabled', 'api_cors_enabled', 'api_docs_enabled'
        ];
        
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field) ? true : false;
        }

        // Ayarları kaydet
        foreach ($validated as $key => $value) {
            $type = $this->determineType($value);
            $group = $this->determineGroup($key);
            
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => $type,
                    'group' => $group,
                ]
            );
        }

        // Cache'i temizle
        $this->clearSettingsCache();

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_settings',
            'target_type' => 'Setting',
            'target_id' => null,
            'details' => 'Site ayarları güncellendi',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Ayarlar başarıyla kaydedildi! 🎉');
    }

    /**
     * Belirli bir kategori için ayarları getir
     */
    public function getByCategory(string $category)
    {
        $cacheKey = "settings.category.{$category}";
        
        $settings = Cache::remember($cacheKey, 3600, function () use ($category) {
            return Setting::where('group', $category)->get()->keyBy('key');
        });
        
        return response()->json($settings);
    }

    /**
     * Cache'i temizle
     */
    public function clearCache()
    {
        $this->clearSettingsCache();
        
        // Diğer cache'leri de temizle
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'clear_cache',
            'target_type' => 'System',
            'target_id' => null,
            'details' => 'Tüm cache temizlendi',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        return back()->with('success', 'Cache başarıyla temizlendi! 🧹');
    }

    /**
     * Ayarları test et
     */
    public function testSettings(Request $request)
    {
        $type = $request->input('type');
        
        switch ($type) {
            case 'email':
                return $this->testEmailSettings($request);
            case 'smtp':
                return $this->testSmtpSettings($request);
            case 'api':
                return $this->testApiSettings($request);
            default:
                return response()->json(['success' => false, 'message' => 'Geçersiz test tipi']);
        }
    }

    /**
     * Email ayarlarını test et
     */
    private function testEmailSettings(Request $request)
    {
        try {
            $email = $request->input('email', auth()->user()->email);
            
            \Mail::raw('Bu bir test emailidir.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email - ' . config('app.name'));
            });
            
            return response()->json([
                'success' => true,
                'message' => "Test emaili {$email} adresine gönderildi! 📧"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email gönderilemedi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * SMTP ayarlarını test et
     */
    private function testSmtpSettings(Request $request)
    {
        try {
            $config = [
                'host' => $request->input('smtp_host'),
                'port' => $request->input('smtp_port'),
                'username' => $request->input('smtp_username'),
                'password' => $request->input('smtp_password'),
                'encryption' => $request->input('smtp_encryption'),
            ];
            
            // SMTP bağlantısını test et
            $transport = new \Swift_SmtpTransport($config['host'], $config['port'], $config['encryption']);
            $transport->setUsername($config['username']);
            $transport->setPassword($config['password']);
            
            $mailer = new \Swift_Mailer($transport);
            $mailer->getTransport()->start();
            
            return response()->json([
                'success' => true,
                'message' => 'SMTP bağlantısı başarılı! ✅'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SMTP bağlantısı başarısız: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API ayarlarını test et
     */
    private function testApiSettings(Request $request)
    {
        try {
            // API endpoint'ini test et
            $response = \Http::get(url('/api/health'));
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'API çalışıyor! ✅'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'API yanıt vermiyor'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API testi başarısız: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Değerin tipini belirle
     */
    private function determineType($value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }
        
        if (is_int($value) || is_numeric($value)) {
            return 'integer';
        }
        
        if (is_array($value)) {
            return 'json';
        }
        
        return 'string';
    }

    /**
     * Ayarın grubunu belirle
     */
    private function determineGroup(string $key): string
    {
        // Genel ayarlar
        if (in_array($key, ['site_name', 'site_tagline', 'site_description', 'homepage_announcement', 
            'site_logo', 'site_favicon', 'contact_email', 'contact_phone', 'social_facebook', 
            'social_twitter', 'social_instagram', 'social_discord', 'social_youtube', 
            'google_analytics_id', 'facebook_pixel_id', 'timezone', 'language'])) {
            return 'general';
        }
        
        // Kayıt ve güvenlik
        if (in_array($key, ['registration_open', 'require_email_verification', 'require_phone_verification',
            'recaptcha_enabled', 'recaptcha_site_key', 'recaptcha_secret_key', 'minimum_age',
            'password_min_length', 'password_require_uppercase', 'password_require_lowercase',
            'password_require_numbers', 'password_require_symbols', 'two_factor_required_for_admins',
            'session_lifetime', 'max_login_attempts', 'lockout_duration'])) {
            return 'security';
        }
        
        // İçerik
        if (in_array($key, ['content_moderation', 'auto_publish', 'max_upload_size', 'allowed_file_types',
            'daily_post_limit', 'daily_comment_limit', 'spam_protection', 'profanity_filter',
            'profanity_words', 'auto_moderate_reports'])) {
            return 'content';
        }
        
        // Bildirim
        if (in_array($key, ['email_notifications', 'sms_notifications', 'push_notifications',
            'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption',
            'smtp_from_address', 'smtp_from_name', 'notification_new_user', 'notification_new_content',
            'notification_new_report', 'notification_new_application'])) {
            return 'notification';
        }
        
        // XP ve gamification
        if (in_array($key, ['xp_enabled', 'xp_register', 'xp_login', 'xp_post_create',
            'xp_comment_create', 'xp_like_receive', 'xp_guide_create', 'xp_clan_create',
            'xp_lfg_create', 'xp_per_level', 'xp_level_multiplier', 'badges_enabled',
            'leaderboard_enabled', 'leaderboard_update_frequency'])) {
            return 'xp';
        }
        
        // Bakım
        if (in_array($key, ['maintenance_mode', 'maintenance_message', 'maintenance_eta', 'maintenance_allowed_ips'])) {
            return 'maintenance';
        }
        
        // API
        if (in_array($key, ['api_enabled', 'api_rate_limit', 'api_rate_limit_period',
            'api_require_authentication', 'webhook_enabled', 'webhook_secret',
            'cors_enabled', 'cors_allowed_origins'])) {
            return 'api';
        }
        
        return 'general';
    }

    /**
     * Ayarlar cache'ini temizle
     */
    private function clearSettingsCache(): void
    {
        Cache::forget('settings.all');
        
        // Kategori cache'lerini de temizle
        $categories = ['general', 'security', 'content', 'notification', 'xp', 'maintenance', 'api'];
        foreach ($categories as $category) {
            Cache::forget("settings.category.{$category}");
        }
    }
}
