<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Performance Indexes Migration
 * 
 * Sık kullanılan sorgular için index'ler ekler
 * Query performansını artırır ve N+1 problemlerini önler
 * 
 * Not: Bazı index'ler zaten mevcut olabilir, bu durumda hata vermeden devam eder
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Her index'i ayrı ayrı ekle, hata olursa devam et
        $this->addIndex('users', 'last_login_at');
        $this->addIndex('users', 'xp_total');
        $this->addIndex('users', ['status', 'created_at'], 'users_status_created_at_idx');
        $this->addIndex('users', ['is_admin', 'status'], 'users_is_admin_status_idx');
        
        // Profiles tablosu
        $this->addIndex('profiles', 'city');
        $this->addIndex('profiles', 'game_id');
        $this->addIndex('profiles', 'pubg_id');
        $this->addIndex('profiles', ['user_id', 'game_id'], 'profiles_user_game_idx');
        
        // LFG Posts tablosu
        $this->addIndex('lfg_posts', 'user_id');
        $this->addIndex('lfg_posts', 'game_id');
        $this->addIndex('lfg_posts', 'status');
        $this->addIndex('lfg_posts', 'is_featured');
        $this->addIndex('lfg_posts', 'city');
        $this->addIndex('lfg_posts', 'views_count');
        $this->addIndex('lfg_posts', ['status', 'created_at'], 'lfg_posts_status_created_idx');
        $this->addIndex('lfg_posts', ['game_id', 'status'], 'lfg_posts_game_status_idx');
        
        // Clans tablosu
        $this->addIndex('clans', 'leader_id');
        $this->addIndex('clans', 'game_id');
        $this->addIndex('clans', 'is_verified');
        $this->addIndex('clans', 'city');
        $this->addIndex('clans', ['game_id', 'is_verified'], 'clans_game_verified_idx');
        
        // Guide Posts tablosu
        $this->addIndex('guide_posts', 'user_id');
        $this->addIndex('guide_posts', 'game_id');
        $this->addIndex('guide_posts', 'is_published');
        $this->addIndex('guide_posts', 'is_featured');
        $this->addIndex('guide_posts', 'views_count');
        $this->addIndex('guide_posts', ['is_published', 'created_at'], 'guide_posts_published_created_idx');
        $this->addIndex('guide_posts', ['game_id', 'is_published'], 'guide_posts_game_published_idx');
        
        // Community Posts tablosu
        $this->addIndex('community_posts', 'user_id');
        $this->addIndex('community_posts', 'is_featured');
        
        // Comments tablosu
        $this->addIndex('comments', 'user_id');
        $this->addIndex('comments', ['commentable_type', 'commentable_id'], 'comments_commentable_idx');
        
        // Reports tablosu
        $this->addIndex('reports', 'reporter_id');
        $this->addIndex('reports', 'status');
        $this->addIndex('reports', 'priority');
        $this->addIndex('reports', ['reportable_type', 'reportable_id'], 'reports_reportable_idx');
        $this->addIndex('reports', ['status', 'priority'], 'reports_status_priority_idx');
        
        // Clan Applications tablosu
        $this->addIndex('clan_applications', 'clan_id');
        $this->addIndex('clan_applications', 'user_id');
        $this->addIndex('clan_applications', 'status');
        $this->addIndex('clan_applications', ['clan_id', 'status'], 'clan_apps_clan_status_idx');
        
        // LFG Applications tablosu
        $this->addIndex('lfg_applications', 'lfg_post_id');
        $this->addIndex('lfg_applications', 'user_id');
        $this->addIndex('lfg_applications', 'status');
        $this->addIndex('lfg_applications', ['lfg_post_id', 'status'], 'lfg_apps_post_status_idx');
        
        // Admin Activity Logs tablosu
        $this->addIndex('admin_activity_logs', 'admin_id');
        $this->addIndex('admin_activity_logs', 'action');
        $this->addIndex('admin_activity_logs', ['target_type', 'target_id'], 'admin_logs_target_idx');
        $this->addIndex('admin_activity_logs', ['admin_id', 'created_at'], 'admin_logs_admin_created_idx');
        
        // XP Events tablosu
        $this->addIndex('xp_events', 'user_id');
        $this->addIndex('xp_events', 'type');
        $this->addIndex('xp_events', ['user_id', 'created_at'], 'xp_events_user_created_idx');
        
        // Admin Roles tablosu
        $this->addIndex('admin_roles', 'slug');
        $this->addIndex('admin_roles', 'is_system');
        
        // Admin Permissions tablosu
        $this->addIndex('admin_permissions', 'slug');
        $this->addIndex('admin_permissions', 'group');
        
        // System Logs tablosu
        $this->addIndex('system_logs', 'type');
        $this->addIndex('system_logs', 'level');
        $this->addIndex('system_logs', ['type', 'created_at'], 'system_logs_type_created_idx');
        
        // Scheduled Reports tablosu
        $this->addIndex('scheduled_reports', 'is_active');
        $this->addIndex('scheduled_reports', 'frequency');
        $this->addIndex('scheduled_reports', 'next_send_at');
        $this->addIndex('scheduled_reports', ['is_active', 'next_send_at'], 'scheduled_reports_active_next_idx');
    }

    /**
     * Index ekle (hata varsa sessizce geç)
     */
    private function addIndex(string $table, $columns, ?string $indexName = null): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                if ($indexName) {
                    $table->index($columns, $indexName);
                } else {
                    $table->index($columns);
                }
            });
        } catch (\Exception $e) {
            // Index zaten varsa veya tablo yoksa sessizce geç
            // Migration'ı durdurmaz
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Index'leri kaldır (hata varsa sessizce geç)
        $this->dropIndex('users', 'users_last_login_at_index');
        $this->dropIndex('users', 'users_xp_total_index');
        $this->dropIndex('users', 'users_status_created_at_idx');
        $this->dropIndex('users', 'users_is_admin_status_idx');
        
        $this->dropIndex('profiles', 'profiles_city_index');
        $this->dropIndex('profiles', 'profiles_game_id_index');
        $this->dropIndex('profiles', 'profiles_pubg_id_index');
        $this->dropIndex('profiles', 'profiles_user_game_idx');
        
        $this->dropIndex('lfg_posts', 'lfg_posts_user_id_index');
        $this->dropIndex('lfg_posts', 'lfg_posts_game_id_index');
        $this->dropIndex('lfg_posts', 'lfg_posts_status_index');
        $this->dropIndex('lfg_posts', 'lfg_posts_is_featured_index');
        $this->dropIndex('lfg_posts', 'lfg_posts_city_index');
        $this->dropIndex('lfg_posts', 'lfg_posts_views_count_index');
        $this->dropIndex('lfg_posts', 'lfg_posts_status_created_idx');
        $this->dropIndex('lfg_posts', 'lfg_posts_game_status_idx');
        
        $this->dropIndex('clans', 'clans_leader_id_index');
        $this->dropIndex('clans', 'clans_game_id_index');
        $this->dropIndex('clans', 'clans_is_verified_index');
        $this->dropIndex('clans', 'clans_city_index');
        $this->dropIndex('clans', 'clans_game_verified_idx');
        
        $this->dropIndex('guide_posts', 'guide_posts_user_id_index');
        $this->dropIndex('guide_posts', 'guide_posts_game_id_index');
        $this->dropIndex('guide_posts', 'guide_posts_is_published_index');
        $this->dropIndex('guide_posts', 'guide_posts_is_featured_index');
        $this->dropIndex('guide_posts', 'guide_posts_views_count_index');
        $this->dropIndex('guide_posts', 'guide_posts_published_created_idx');
        $this->dropIndex('guide_posts', 'guide_posts_game_published_idx');
        
        $this->dropIndex('community_posts', 'community_posts_user_id_index');
        $this->dropIndex('community_posts', 'community_posts_is_featured_index');
        
        $this->dropIndex('comments', 'comments_user_id_index');
        $this->dropIndex('comments', 'comments_commentable_idx');
        
        $this->dropIndex('reports', 'reports_reporter_id_index');
        $this->dropIndex('reports', 'reports_status_index');
        $this->dropIndex('reports', 'reports_priority_index');
        $this->dropIndex('reports', 'reports_reportable_idx');
        $this->dropIndex('reports', 'reports_status_priority_idx');
        
        $this->dropIndex('clan_applications', 'clan_applications_clan_id_index');
        $this->dropIndex('clan_applications', 'clan_applications_user_id_index');
        $this->dropIndex('clan_applications', 'clan_applications_status_index');
        $this->dropIndex('clan_applications', 'clan_apps_clan_status_idx');
        
        $this->dropIndex('lfg_applications', 'lfg_applications_lfg_post_id_index');
        $this->dropIndex('lfg_applications', 'lfg_applications_user_id_index');
        $this->dropIndex('lfg_applications', 'lfg_applications_status_index');
        $this->dropIndex('lfg_applications', 'lfg_apps_post_status_idx');
        
        $this->dropIndex('admin_activity_logs', 'admin_activity_logs_admin_id_index');
        $this->dropIndex('admin_activity_logs', 'admin_activity_logs_action_index');
        $this->dropIndex('admin_activity_logs', 'admin_logs_target_idx');
        $this->dropIndex('admin_activity_logs', 'admin_logs_admin_created_idx');
        
        $this->dropIndex('xp_events', 'xp_events_user_id_index');
        $this->dropIndex('xp_events', 'xp_events_type_index');
        $this->dropIndex('xp_events', 'xp_events_user_created_idx');
        
        $this->dropIndex('admin_roles', 'admin_roles_slug_index');
        $this->dropIndex('admin_roles', 'admin_roles_is_system_index');
        
        $this->dropIndex('admin_permissions', 'admin_permissions_slug_index');
        $this->dropIndex('admin_permissions', 'admin_permissions_group_index');
        
        $this->dropIndex('system_logs', 'system_logs_type_index');
        $this->dropIndex('system_logs', 'system_logs_level_index');
        $this->dropIndex('system_logs', 'system_logs_type_created_idx');
        
        $this->dropIndex('scheduled_reports', 'scheduled_reports_is_active_index');
        $this->dropIndex('scheduled_reports', 'scheduled_reports_frequency_index');
        $this->dropIndex('scheduled_reports', 'scheduled_reports_next_send_at_index');
        $this->dropIndex('scheduled_reports', 'scheduled_reports_active_next_idx');
    }

    /**
     * Index kaldır (hata varsa sessizce geç)
     */
    private function dropIndex(string $table, string $indexName): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        } catch (\Exception $e) {
            // Index yoksa sessizce geç
        }
    }
};
