<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\AdminPermission;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Admin Role Controller
 * Admin rol yönetimi - CRUD işlemleri ve yetki atama
 */
class RoleController extends Controller
{
    /**
     * Rol listesi
     * GET /admin/roles
     */
    public function index(Request $request)
    {
        $query = AdminRole::withCount(['users', 'permissions']);

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sistem rolleri filtresi
        if ($request->filled('is_system')) {
            $query->where('is_system', $request->is_system === 'true');
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $roles = $query->paginate(20)->withQueryString();

        // İstatistikler
        $stats = [
            'total' => AdminRole::count(),
            'system' => AdminRole::where('is_system', true)->count(),
            'custom' => AdminRole::where('is_system', false)->count(),
            'total_users' => DB::table('user_role')->distinct('user_id')->count(),
        ];

        return view('admin.roles.index', compact('roles', 'stats'));
    }

    /**
     * Rol oluşturma formu
     * GET /admin/roles/create
     */
    public function create()
    {
        // Tüm yetkileri gruplar halinde getir
        $permissions = AdminPermission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Rol kaydetme
     * POST /admin/roles
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:admin_roles,name',
            'slug' => 'nullable|string|max:255|unique:admin_roles,slug',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:admin_permissions,id',
        ]);

        DB::beginTransaction();
        try {
            // Slug otomatik oluştur
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Rol oluştur
            $role = AdminRole::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'color' => $validated['color'] ?? '#3B82F6',
                'icon' => $validated['icon'] ?? 'shield',
                'is_system' => false, // Kullanıcı tarafından oluşturulan roller sistem rolü değildir
            ]);

            // Yetkileri ata
            if (!empty($validated['permissions'])) {
                $role->permissions()->attach($validated['permissions']);
            }

            // Admin activity log
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'create_role',
                'target_type' => 'AdminRole',
                'target_id' => $role->id,
                'details' => "Yeni rol oluşturuldu: {$role->name}",
            ]);

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol başarıyla oluşturuldu');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Rol oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Rol detayı
     * GET /admin/roles/{id}
     */
    public function show($id)
    {
        $role = AdminRole::with(['permissions', 'users.profile'])
            ->withCount(['users', 'permissions'])
            ->findOrFail($id);

        return view('admin.roles.show', compact('role'));
    }

    /**
     * Rol düzenleme formu
     * GET /admin/roles/{id}/edit
     */
    public function edit($id)
    {
        $role = AdminRole::with('permissions')->findOrFail($id);

        // Sistem rollerini düzenleyemez
        if ($role->is_system) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Sistem rolleri düzenlenemez');
        }

        // Tüm yetkileri gruplar halinde getir
        $permissions = AdminPermission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        
        // Rolün sahip olduğu yetki ID'lerini al
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    /**
     * Rol güncelleme
     * PUT /admin/roles/{id}
     */
    public function update(Request $request, $id)
    {
        $role = AdminRole::findOrFail($id);

        // Sistem rollerini güncelleyemez
        if ($role->is_system) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Sistem rolleri güncellenemez');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:admin_roles,name,' . $role->id,
            'slug' => 'nullable|string|max:255|unique:admin_roles,slug,' . $role->id,
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:admin_permissions,id',
        ]);

        DB::beginTransaction();
        try {
            // Slug otomatik oluştur
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Rolü güncelle
            $role->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? $role->description,
                'color' => $validated['color'] ?? $role->color,
                'icon' => $validated['icon'] ?? $role->icon,
            ]);

            // Yetkileri senkronize et
            $oldPermissions = $role->permissions->pluck('id')->toArray();
            $newPermissions = $validated['permissions'] ?? [];
            
            $role->permissions()->sync($newPermissions);

            // Değişiklikleri logla
            $addedPermissions = array_diff($newPermissions, $oldPermissions);
            $removedPermissions = array_diff($oldPermissions, $newPermissions);

            $details = "Rol güncellendi: {$role->name}";
            if (!empty($addedPermissions)) {
                $details .= " | Eklenen yetkiler: " . count($addedPermissions);
            }
            if (!empty($removedPermissions)) {
                $details .= " | Kaldırılan yetkiler: " . count($removedPermissions);
            }

            // Admin activity log
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'update_role',
                'target_type' => 'AdminRole',
                'target_id' => $role->id,
                'details' => $details,
            ]);

            DB::commit();

            return redirect()->route('admin.roles.show', $role->id)
                ->with('success', 'Rol başarıyla güncellendi');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Rol güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Rol silme
     * DELETE /admin/roles/{id}
     */
    public function destroy($id)
    {
        $role = AdminRole::withCount('users')->findOrFail($id);

        // Sistem rollerini silemez
        if ($role->is_system) {
            return back()->with('error', 'Sistem rolleri silinemez');
        }

        // Kullanıcısı olan rolleri silemez
        if ($role->users_count > 0) {
            return back()->with('error', "Bu rol {$role->users_count} kullanıcı tarafından kullanılıyor. Önce kullanıcıları başka bir role atayın.");
        }

        $roleName = $role->name;

        DB::beginTransaction();
        try {
            // Yetkileri ayır
            $role->permissions()->detach();

            // Rolü sil
            $role->delete();

            // Admin activity log
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'delete_role',
                'target_type' => 'AdminRole',
                'target_id' => $id,
                'details' => "Rol silindi: {$roleName}",
            ]);

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol başarıyla silindi');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Rol silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Role yetki atama (AJAX)
     * POST /admin/roles/{id}/permissions/attach
     */
    public function attachPermission(Request $request, $id)
    {
        $role = AdminRole::findOrFail($id);

        // Sistem rollerini düzenleyemez
        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem rolleri düzenlenemez'
            ], 403);
        }

        $validated = $request->validate([
            'permission_id' => 'required|exists:admin_permissions,id',
        ]);

        try {
            $role->givePermission($validated['permission_id']);

            // Admin activity log
            $permission = AdminPermission::find($validated['permission_id']);
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'attach_permission',
                'target_type' => 'AdminRole',
                'target_id' => $role->id,
                'details' => "Role yetki eklendi: {$role->name} -> {$permission->name}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yetki başarıyla eklendi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Yetki eklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rolden yetki kaldırma (AJAX)
     * POST /admin/roles/{id}/permissions/detach
     */
    public function detachPermission(Request $request, $id)
    {
        $role = AdminRole::findOrFail($id);

        // Sistem rollerini düzenleyemez
        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem rolleri düzenlenemez'
            ], 403);
        }

        $validated = $request->validate([
            'permission_id' => 'required|exists:admin_permissions,id',
        ]);

        try {
            $role->revokePermission($validated['permission_id']);

            // Admin activity log
            $permission = AdminPermission::find($validated['permission_id']);
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'detach_permission',
                'target_type' => 'AdminRole',
                'target_id' => $role->id,
                'details' => "Rolden yetki kaldırıldı: {$role->name} -> {$permission->name}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yetki başarıyla kaldırıldı'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Yetki kaldırılırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rol yetkilerini senkronize et (AJAX)
     * POST /admin/roles/{id}/permissions/sync
     */
    public function syncPermissions(Request $request, $id)
    {
        $role = AdminRole::findOrFail($id);

        // Sistem rollerini düzenleyemez
        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem rolleri düzenlenemez'
            ], 403);
        }

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:admin_permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $oldPermissions = $role->permissions->pluck('id')->toArray();
            $newPermissions = $validated['permissions'];

            $role->syncPermissions($newPermissions);

            // Değişiklikleri hesapla
            $addedCount = count(array_diff($newPermissions, $oldPermissions));
            $removedCount = count(array_diff($oldPermissions, $newPermissions));

            // Admin activity log
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'sync_permissions',
                'target_type' => 'AdminRole',
                'target_id' => $role->id,
                'details' => "Rol yetkileri senkronize edildi: {$role->name} | Eklenen: {$addedCount}, Kaldırılan: {$removedCount}",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Yetkiler başarıyla senkronize edildi',
                'added' => $addedCount,
                'removed' => $removedCount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Yetkiler senkronize edilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rol kopyalama
     * POST /admin/roles/{id}/duplicate
     */
    public function duplicate($id)
    {
        $originalRole = AdminRole::with('permissions')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Yeni rol oluştur
            $newRole = AdminRole::create([
                'name' => $originalRole->name . ' (Kopya)',
                'slug' => $originalRole->slug . '-kopya-' . time(),
                'description' => $originalRole->description,
                'color' => $originalRole->color,
                'icon' => $originalRole->icon,
                'is_system' => false,
            ]);

            // Yetkileri kopyala
            $permissionIds = $originalRole->permissions->pluck('id')->toArray();
            $newRole->permissions()->attach($permissionIds);

            // Admin activity log
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'duplicate_role',
                'target_type' => 'AdminRole',
                'target_id' => $newRole->id,
                'details' => "Rol kopyalandı: {$originalRole->name} -> {$newRole->name}",
            ]);

            DB::commit();

            return redirect()->route('admin.roles.edit', $newRole->id)
                ->with('success', 'Rol başarıyla kopyalandı');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Rol kopyalanırken hata oluştu: ' . $e->getMessage());
        }
    }
}
