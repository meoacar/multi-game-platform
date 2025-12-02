<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminRole;
use App\Models\AdminPermission;
use App\Models\User;

class AdminRoleSeeder extends Seeder
{
    /**
     * Admin rol ve yetki sistemini başlat
     */
    public function run(): void
    {
        // Rolleri oluştur
        $superAdmin = AdminRole::create([
            'name' => 'Süper Admin',
            'slug' => 'super_admin',
            'description' => 'Tüm yetkilere sahip',
            'is_system' => true,
            'color' => '#DC2626',
            'icon' => 'shield-check',
        ]);

        $moderator = AdminRole::create([
            'name' => 'Moderatör',
            'slug' => 'moderator',
            'description' => 'İçerik moderasyonu yapabilir',
            'is_system' => true,
            'color' => '#F59E0B',
            'icon' => 'shield',
        ]);

        $contentManager = AdminRole::create([
            'name' => 'İçerik Yöneticisi',
            'slug' => 'content_manager',
            'description' => 'İçerik yönetimi yapabilir',
            'is_system' => true,
            'color' => '#10B981',
            'icon' => 'pencil',
        ]);

        // Tüm yetkileri al
        $allPermissions = AdminPermission::all();

        // Süper admin'e tüm yetkileri ver
        $superAdmin->permissions()->sync($allPermissions->pluck('id'));

        // Moderatöre sadece moderasyon yetkilerini ver
        $moderatorPermissions = AdminPermission::whereIn('slug', [
            'reports.view',
            'reports.manage',
            'users.view',
            'users.ban',
        ])->pluck('id');
        $moderator->permissions()->sync($moderatorPermissions);

        // İçerik yöneticisine içerik yetkilerini ver
        $contentPermissions = AdminPermission::whereIn('slug', [
            'lfg-posts.view',
            'lfg-posts.manage',
            'clans.view',
            'clans.manage',
            'guides.view',
            'guides.manage',
        ])->pluck('id');
        $contentManager->permissions()->sync($contentPermissions);

        // Admin kullanıcıya süper admin rolü ver
        $admin = User::where('email', 'admin@pubgcommunity.com')->first();
        if ($admin) {
            $admin->adminRoles()->attach($superAdmin->id);
            echo "✅ Admin kullanıcıya 'Süper Admin' rolü verildi!\n";
        }

        echo "✅ 3 rol oluşturuldu: Süper Admin, Moderatör, İçerik Yöneticisi\n";
    }
}
