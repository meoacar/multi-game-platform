<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * MenuController
 * 
 * Menü yönetimi için controller
 * 
 * Özellikler:
 * - Menü CRUD işlemleri
 * - Menü öğesi CRUD işlemleri
 * - Drag & drop sıralama
 * - Hiyerarşik yapı yönetimi
 * - AJAX API endpoint'leri
 */
class MenuController extends Controller
{
    /**
     * Menü listesi
     */
    public function index()
    {
        $menus = Menu::withCount('allItems')->latest()->paginate(15);
        
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Yeni menü oluşturma formu
     */
    public function create()
    {
        $locations = Menu::getAvailableLocations();
        
        return view('admin.menus.create', compact('locations'));
    }

    /**
     * Menü kaydetme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:menus,slug',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Slug otomatik oluştur
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $menu = Menu::create($validated);

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('success', 'Menü başarıyla oluşturuldu.');
    }

    /**
     * Menü düzenleme sayfası
     */
    public function edit(Menu $menu)
    {
        $menu->load(['items.children']);
        $locations = Menu::getAvailableLocations();
        $pages = Page::where('is_published', true)->get();
        $itemTypes = MenuItem::getAvailableTypes();
        
        return view('admin.menus.edit', compact('menu', 'locations', 'pages', 'itemTypes'));
    }

    /**
     * Menü güncelleme
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:menus,slug,' . $menu->id,
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $menu->update($validated);

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('success', 'Menü başarıyla güncellendi.');
    }

    /**
     * Menü silme
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menü başarıyla silindi.');
    }

    /**
     * Menü öğesi ekleme (AJAX)
     */
    public function storeItem(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:menu_items,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:custom,page,category,url,route',
            'url' => 'nullable|string|max:500',
            'route' => 'nullable|string|max:255',
            'target_id' => 'nullable|integer',
            'target_type' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'css_class' => 'nullable|string|max:255',
            'target' => 'nullable|string|in:_self,_blank',
            'is_active' => 'boolean',
        ]);

        $validated['menu_id'] = $menu->id;
        $validated['order'] = MenuItem::where('menu_id', $menu->id)
            ->where('parent_id', $validated['parent_id'] ?? null)
            ->max('order') + 1;

        $item = MenuItem::create($validated);
        $item->load('children');

        return response()->json([
            'success' => true,
            'message' => 'Menü öğesi başarıyla eklendi.',
            'item' => $item,
        ]);
    }

    /**
     * Menü öğesi güncelleme (AJAX)
     */
    public function updateItem(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:custom,page,category,url,route',
            'url' => 'nullable|string|max:500',
            'route' => 'nullable|string|max:255',
            'target_id' => 'nullable|integer',
            'target_type' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'css_class' => 'nullable|string|max:255',
            'target' => 'nullable|string|in:_self,_blank',
            'is_active' => 'boolean',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menü öğesi başarıyla güncellendi.',
            'item' => $item,
        ]);
    }

    /**
     * Menü öğesi silme (AJAX)
     */
    public function destroyItem(MenuItem $item)
    {
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menü öğesi başarıyla silindi.',
        ]);
    }

    /**
     * Menü öğelerini yeniden sıralama (AJAX - Drag & Drop)
     */
    public function reorderItems(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.parent_id' => 'nullable|exists:menu_items,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $itemData) {
            MenuItem::where('id', $itemData['id'])->update([
                'parent_id' => $itemData['parent_id'],
                'order' => $itemData['order'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menü sıralaması başarıyla güncellendi.',
        ]);
    }

    /**
     * Menü öğesi durumu değiştirme (AJAX)
     */
    public function toggleItemStatus(MenuItem $item)
    {
        $item->update(['is_active' => !$item->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Menü öğesi durumu değiştirildi.',
            'is_active' => $item->is_active,
        ]);
    }
}
