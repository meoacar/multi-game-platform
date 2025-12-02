<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use App\Models\AdminRole;
use App\Models\AdminPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function banli_kullanici_api_erisimi_engellenebilir()
    {
        $user = User::factory()->create([
            'status' => 'banned',
        ]);
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/me');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Your account has been banned.'
            ]);
    }

    /** @test */
    public function aktif_kullanici_api_erisimi_yapabilir()
    {
        $user = User::factory()->create([
            'status' => 'active',
        ]);
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/me');

        $response->assertStatus(200);
    }

    /** @test */
    public function auth_middleware_misafir_kullaniciyi_engeller()
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(401);
    }

    /** @test */
    public function auth_middleware_giris_yapmis_kullaniciyi_gecirer()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/me');

        $response->assertStatus(200);
    }

    /** @test */
    public function check_admin_permission_middleware_yetkisi_olmayan_kullaniciyi_engeller()
    {
        // Yetki oluştur
        $permission = AdminPermission::create([
            'name' => 'Kullanıcıları Düzenle',
            'slug' => 'users.edit',
            'group' => 'users',
            'description' => 'Kullanıcıları düzenleme yetkisi',
        ]);

        // Yetkisi olmayan kullanıcı
        $user = User::factory()->create(['is_admin' => true]);

        // Test route'u oluştur
        \Route::get('/test-permission', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'permission:users.edit']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/test-permission');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Bu işlem için yetkiniz yok'
            ]);
    }

    /** @test */
    public function check_admin_permission_middleware_yetkisi_olan_kullaniciyi_gecirer()
    {
        // Yetki oluştur
        $permission = AdminPermission::create([
            'name' => 'Kullanıcıları Düzenle',
            'slug' => 'users.edit',
            'group' => 'users',
            'description' => 'Kullanıcıları düzenleme yetkisi',
        ]);

        // Rol oluştur ve yetkiyi ata
        $role = AdminRole::create([
            'name' => 'Moderatör',
            'slug' => 'moderator',
            'description' => 'Moderatör rolü',
        ]);
        $role->permissions()->attach($permission->id);

        // Kullanıcı oluştur ve rolü ata
        $user = User::factory()->create(['is_admin' => true]);
        $user->adminRoles()->attach($role->id);

        // Test route'u oluştur
        \Route::get('/test-permission-allowed', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'permission:users.edit']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/test-permission-allowed');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function check_admin_permission_middleware_super_admin_tum_yetkilere_sahip()
    {
        // Yetki oluştur
        $permission = AdminPermission::create([
            'name' => 'Kullanıcıları Sil',
            'slug' => 'users.delete',
            'group' => 'users',
            'description' => 'Kullanıcıları silme yetkisi',
        ]);

        // Süper admin rolü oluştur
        $superAdminRole = AdminRole::create([
            'name' => 'Süper Admin',
            'slug' => 'super_admin',
            'description' => 'Tüm yetkilere sahip',
        ]);

        // Kullanıcı oluştur ve süper admin rolü ata
        $user = User::factory()->create(['is_admin' => true]);
        $user->adminRoles()->attach($superAdminRole->id);

        // Test route'u oluştur
        \Route::get('/test-super-admin', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'permission:users.delete']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/test-super-admin');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function check_admin_permission_middleware_giris_yapmamis_kullaniciyi_engeller()
    {
        // Test route'u oluştur
        \Route::get('/test-guest', function () {
            return response()->json(['success' => true]);
        })->middleware(['permission:users.view']);

        $response = $this->getJson('/test-guest');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Bu işlem için giriş yapmalısınız'
            ]);
    }

    /** @test */
    public function check_admin_permission_middleware_ozel_yetki_ile_calisir()
    {
        // Yetki oluştur
        $permission = AdminPermission::create([
            'name' => 'İçerik Yönetimi',
            'slug' => 'content.manage',
            'group' => 'content',
            'description' => 'İçerik yönetimi yetkisi',
        ]);

        // Kullanıcı oluştur ve özel yetki ver
        $user = User::factory()->create(['is_admin' => true]);
        $user->customPermissions()->attach($permission->id, ['granted' => true]);

        // Test route'u oluştur
        \Route::get('/test-custom-permission', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'permission:content.manage']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/test-custom-permission');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function check_admin_role_middleware_rolü_olmayan_kullaniciyi_engeller()
    {
        // Rol oluştur
        $role = AdminRole::create([
            'name' => 'Moderatör',
            'slug' => 'moderator',
            'description' => 'Moderatör rolü',
        ]);

        // Rolü olmayan kullanıcı
        $user = User::factory()->create(['is_admin' => true]);

        // Test route'u oluştur
        \Route::get('/test-role-denied', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'role:moderator']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/test-role-denied');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Bu sayfaya erişim yetkiniz yok'
            ]);
    }

    /** @test */
    public function check_admin_role_middleware_rolü_olan_kullaniciyi_gecirer()
    {
        // Rol oluştur
        $role = AdminRole::create([
            'name' => 'Moderatör',
            'slug' => 'moderator',
            'description' => 'Moderatör rolü',
        ]);

        // Kullanıcı oluştur ve rolü ata
        $user = User::factory()->create(['is_admin' => true]);
        $user->adminRoles()->attach($role->id);

        // Test route'u oluştur
        \Route::get('/test-role-allowed', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'role:moderator']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/test-role-allowed');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function check_admin_role_middleware_birden_fazla_rol_ile_calisir()
    {
        // Roller oluştur
        $moderatorRole = AdminRole::create([
            'name' => 'Moderatör',
            'slug' => 'moderator',
            'description' => 'Moderatör rolü',
        ]);

        $contentManagerRole = AdminRole::create([
            'name' => 'İçerik Yöneticisi',
            'slug' => 'content_manager',
            'description' => 'İçerik yöneticisi rolü',
        ]);

        // Kullanıcı oluştur ve sadece content_manager rolü ata
        $user = User::factory()->create(['is_admin' => true]);
        $user->adminRoles()->attach($contentManagerRole->id);

        // Test route'u oluştur (moderator VEYA content_manager)
        \Route::get('/test-multiple-roles', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'role:moderator,content_manager']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/test-multiple-roles');

        // content_manager rolü olduğu için geçmeli
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function check_admin_role_middleware_giris_yapmamis_kullaniciyi_engeller()
    {
        // Test route'u oluştur
        \Route::get('/test-role-guest', function () {
            return response()->json(['success' => true]);
        })->middleware(['role:moderator']);

        $response = $this->getJson('/test-role-guest');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Bu sayfaya erişim için giriş yapmalısınız'
            ]);
    }

    /** @test */
    public function check_admin_role_middleware_hicbir_rolü_olmayan_kullaniciyi_engeller()
    {
        // Roller oluştur
        $moderatorRole = AdminRole::create([
            'name' => 'Moderatör',
            'slug' => 'moderator',
            'description' => 'Moderatör rolü',
        ]);

        $adminRole = AdminRole::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Admin rolü',
        ]);

        // Kullanıcı oluştur ama rol atama
        $user = User::factory()->create(['is_admin' => true]);

        // Test route'u oluştur (moderator VEYA admin gerekli)
        \Route::get('/test-no-roles', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'role:moderator,admin']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/test-no-roles');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Bu sayfaya erişim yetkiniz yok'
            ]);
    }

    /** @test */
    public function log_admin_activity_middleware_admin_islemlerini_loglar()
    {
        // Admin kullanıcı oluştur
        $admin = User::factory()->create(['is_admin' => true]);

        // Test route'u oluştur
        \Route::post('/test-log-activity', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'log.admin']);

        // İstek yap
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/test-log-activity', [
                'test_field' => 'test_value'
            ]);

        $response->assertStatus(200);

        // Log kaydının oluşturulduğunu kontrol et
        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $admin->id,
            'ip_address' => '127.0.0.1',
        ]);

        // Log kaydını al ve meta bilgilerini kontrol et
        $log = AdminActivityLog::where('admin_id', $admin->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals('POST', $log->meta['method']);
        $this->assertArrayHasKey('request_data', $log->meta);
        $this->assertEquals('test_value', $log->meta['request_data']['test_field']);
    }

    /** @test */
    public function log_admin_activity_middleware_get_isteklerini_loglamaz()
    {
        // Admin kullanıcı oluştur
        $admin = User::factory()->create(['is_admin' => true]);

        // Test route'u oluştur
        \Route::get('/test-no-log-get', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'log.admin']);

        // Mevcut log sayısını al
        $logCountBefore = AdminActivityLog::count();

        // GET isteği yap
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/test-no-log-get');

        $response->assertStatus(200);

        // Log sayısının değişmediğini kontrol et (GET loglanmaz)
        $logCountAfter = AdminActivityLog::count();
        $this->assertEquals($logCountBefore, $logCountAfter);
    }

    /** @test */
    public function log_admin_activity_middleware_basarisiz_istekleri_loglamaz()
    {
        // Admin kullanıcı oluştur
        $admin = User::factory()->create(['is_admin' => true]);

        // Test route'u oluştur (404 döndürecek)
        \Route::post('/test-no-log-fail', function () {
            abort(404);
        })->middleware(['auth:sanctum', 'log.admin']);

        // Mevcut log sayısını al
        $logCountBefore = AdminActivityLog::count();

        // İstek yap
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/test-no-log-fail');

        $response->assertStatus(404);

        // Log sayısının değişmediğini kontrol et (başarısız istekler loglanmaz)
        $logCountAfter = AdminActivityLog::count();
        $this->assertEquals($logCountBefore, $logCountAfter);
    }

    /** @test */
    public function log_admin_activity_middleware_hassas_bilgileri_loglamaz()
    {
        // Admin kullanıcı oluştur
        $admin = User::factory()->create(['is_admin' => true]);

        // Test route'u oluştur
        \Route::post('/test-log-sensitive', function () {
            return response()->json(['success' => true]);
        })->middleware(['auth:sanctum', 'log.admin']);

        // Hassas bilgilerle istek yap
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/test-log-sensitive', [
                'username' => 'testuser',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                '_token' => 'csrf_token',
            ]);

        $response->assertStatus(200);

        // Log kaydını al
        $log = AdminActivityLog::where('admin_id', $admin->id)->first();
        $this->assertNotNull($log);

        // Hassas bilgilerin loglanmadığını kontrol et
        $this->assertArrayHasKey('request_data', $log->meta);
        $this->assertArrayHasKey('username', $log->meta['request_data']);
        $this->assertArrayNotHasKey('password', $log->meta['request_data']);
        $this->assertArrayNotHasKey('password_confirmation', $log->meta['request_data']);
        $this->assertArrayNotHasKey('_token', $log->meta['request_data']);
    }

    /** @test */
    public function log_admin_activity_middleware_giris_yapmamis_kullaniciyi_loglamaz()
    {
        // Test route'u oluştur
        \Route::post('/test-no-log-guest', function () {
            return response()->json(['success' => true]);
        })->middleware(['log.admin']);

        // Mevcut log sayısını al
        $logCountBefore = AdminActivityLog::count();

        // Giriş yapmadan istek yap
        $response = $this->postJson('/test-no-log-guest', [
            'test_field' => 'test_value'
        ]);

        $response->assertStatus(200);

        // Log sayısının değişmediğini kontrol et
        $logCountAfter = AdminActivityLog::count();
        $this->assertEquals($logCountBefore, $logCountAfter);
    }
}
