<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin Permission Controller
 * Admin yetki yönetimi - yetki listesi ve grupları
 */
class PermissionController extends Controller
{
    /**
     * Yetki listesi
     * GET /admin/permissions
     */
    public function index(Request $request)
    {
        $query = AdminPermission::query();

        // Grup filtresi
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'group');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if ($sortBy === 'group') {
            $query->orderBy('group', $sortOrder)->orderBy('name', 'asc');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Sayfalama veya gruplama
        if ($request->get('view') === 'grouped') {
            // Gruplar halinde göster
            $permissions = $query->with('roles')->get()->groupBy('group');
            $paginated = null;
        } else {
            // Sayfalı liste
            $permissions = null;
            $paginated = $query->with('roles')->paginate(50)->withQueryString();
        }

        // Tüm grupları getir (filtre için)
        $groups = AdminPermission::getAllGroups();

        // İstatistikler
        $stats = [
            'total' => AdminPermission::count(),
            'groups' => AdminPermission::distinct('group')->count('group'),
            'most_used' => $this->getMostUsedPermissions(5),
        ];

        return view('admin.permissions.index', compact(
            'permissions',
            'paginated',
            'groups',
            'stats'
        ));
    }

    /**
     * Yetki detayı
     * GET /admin/permissions/{id}
     */
    public function show($id)
    {
        $permission = AdminPermission::with(['roles.users'])
            ->withCount('roles')
            ->findOrFail($id);

        // Bu yetkiye sahip kullanıcı sayısı
        $userCount = DB::table('user_role')
            ->whereIn('role_id', $permission->roles->pluck('id'))
            ->distinct('user_id')
            ->count();

        return view('admin.permissions.show', compact('permission', 'userCount'));
    }

    /**
     * Yetki grupları görünümü
     * GET /admin/permissions/groups
     */
    public function groups(Request $request)
    {
        // Tüm yetkileri gruplara göre organize et
        $permissionsGrouped = AdminPermission::with(['roles'])
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        // Her grup için istatistikler
        $groupStats = [];
        foreach ($permissionsGrouped as $group => $permissions) {
            $groupStats[$group] = [
                'count' => $permissions->count(),
                'roles_count' => $permissions->flatMap->roles->unique('id')->count(),
            ];
        }

        return view('admin.permissions.groups', compact('permissionsGrouped', 'groupStats'));
    }

    /**
     * Belirli bir grubun detayı
     * GET /admin/permissions/groups/{group}
     */
    public function groupDetail($group)
    {
        // Grup adını decode et (URL'den geldiği için)
        $group = urldecode($group);

        // Gruptaki tüm yetkileri getir
        $permissions = AdminPermission::with(['roles'])
            ->where('group', $group)
            ->orderBy('name')
            ->get();

        if ($permissions->isEmpty()) {
            abort(404, 'Grup bulunamadı');
        }

        // Grup istatistikleri
        $stats = [
            'total_permissions' => $permissions->count(),
            'total_roles' => $permissions->flatMap->roles->unique('id')->count(),
            'most_common_role' => $this->getMostCommonRoleInGroup($permissions),
        ];

        return view('admin.permissions.group-detail', compact('group', 'permissions', 'stats'));
    }

    /**
     * En çok kullanılan yetkileri getir
     * 
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    private function getMostUsedPermissions($limit = 5)
    {
        return AdminPermission::withCount('roles')
            ->orderBy('roles_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Gruptaki en yaygın rolü bul
     * 
     * @param \Illuminate\Support\Collection $permissions
     * @return AdminRole|null
     */
    private function getMostCommonRoleInGroup($permissions)
    {
        $roleCounts = [];
        
        foreach ($permissions as $permission) {
            foreach ($permission->roles as $role) {
                if (!isset($roleCounts[$role->id])) {
                    $roleCounts[$role->id] = [
                        'role' => $role,
                        'count' => 0,
                    ];
                }
                $roleCounts[$role->id]['count']++;
            }
        }

        if (empty($roleCounts)) {
            return null;
        }

        // En yüksek sayıya sahip rolü bul
        $mostCommon = collect($roleCounts)->sortByDesc('count')->first();
        
        return $mostCommon ? $mostCommon['role'] : null;
    }

    /**
     * Yetki kullanım raporu (AJAX)
     * GET /admin/api/permissions/usage
     */
    public function usage(Request $request)
    {
        $permissions = AdminPermission::withCount('roles')->get();

        $data = $permissions->map(function($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
                'group' => $permission->group,
                'roles_count' => $permission->roles_count,
                'roles' => $permission->roles->pluck('name'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Grup bazlı istatistikler (AJAX)
     * GET /admin/api/permissions/group-stats
     */
    public function groupStats()
    {
        $groups = AdminPermission::select('group')
            ->selectRaw('COUNT(*) as permission_count')
            ->groupBy('group')
            ->orderBy('group')
            ->get();

        $stats = $groups->map(function($group) {
            $permissions = AdminPermission::where('group', $group->group)->get();
            $rolesCount = $permissions->flatMap->roles->unique('id')->count();

            return [
                'group' => $group->group,
                'permission_count' => $group->permission_count,
                'roles_count' => $rolesCount,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
