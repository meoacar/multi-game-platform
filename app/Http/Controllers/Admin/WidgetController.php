<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * WidgetController
 * 
 * Widget yönetimi için controller
 * 
 * Özellikler:
 * - Widget CRUD işlemleri
 * - Widget türleri yönetimi
 * - Drag & drop yerleştirme
 * - Konum bazlı widget yönetimi
 * - AJAX API endpoint'leri
 */
class WidgetController extends Controller
{
    /**
     * Widget listesi
     */
    public function index(Request $request)
    {
        $query = Widget::query();

        // Konum filtresi
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        // Tür filtresi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Durum filtresi
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'active');
        }

        // Arama
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $widgets = $query->paginate(20)->withQueryString();

        $types = Widget::getAvailableTypes();
        $locations = Widget::getAvailableLocations();

        return view('admin.widgets.index', compact('widgets', 'types', 'locations'));
    }

    /**
     * Yeni widget oluşturma formu
     */
    public function create()
    {
        $types = Widget::getAvailableTypes();
        $locations = Widget::getAvailableLocations();

        return view('admin.widgets.create', compact('types', 'locations'));
    }

    /**
     * Widget kaydetme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:widgets,slug',
            'type' => 'required|string|in:' . implode(',', array_keys(Widget::getAvailableTypes())),
            'location' => 'required|string|in:' . implode(',', array_keys(Widget::getAvailableLocations())),
            'content' => 'nullable|string',
            'settings' => 'nullable|array',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Slug otomatik oluştur
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        } else {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        // Slug benzersizliğini kontrol et
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Widget::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Eğer order belirtilmemişse, en sona ekle
        if (!isset($validated['order'])) {
            $validated['order'] = Widget::where('location', $validated['location'])->max('order') + 1;
        }

        $widget = Widget::create($validated);

        return redirect()
            ->route('admin.widgets.index')
            ->with('success', 'Widget başarıyla oluşturuldu.');
    }

    /**
     * Widget düzenleme formu
     */
    public function edit(Widget $widget)
    {
        $types = Widget::getAvailableTypes();
        $locations = Widget::getAvailableLocations();

        return view('admin.widgets.edit', compact('widget', 'types', 'locations'));
    }

    /**
     * Widget güncelleme
     */
    public function update(Request $request, Widget $widget)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:widgets,slug,' . $widget->id,
            'type' => 'required|string|in:' . implode(',', array_keys(Widget::getAvailableTypes())),
            'location' => 'required|string|in:' . implode(',', array_keys(Widget::getAvailableLocations())),
            'content' => 'nullable|string',
            'settings' => 'nullable|array',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Slug güncelle
        if (!empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $widget->update($validated);

        return redirect()
            ->route('admin.widgets.index')
            ->with('success', 'Widget başarıyla güncellendi.');
    }

    /**
     * Widget silme
     */
    public function destroy(Widget $widget)
    {
        $widget->delete();

        return redirect()
            ->route('admin.widgets.index')
            ->with('success', 'Widget başarıyla silindi.');
    }

    /**
     * Widget durumu değiştirme
     */
    public function toggleStatus(Widget $widget)
    {
        $widget->update([
            'is_active' => !$widget->is_active
        ]);

        $status = $widget->is_active ? 'aktif edildi' : 'pasif edildi';

        return redirect()->back()
            ->with('success', "Widget başarıyla {$status}.");
    }

    /**
     * Widget'ları yeniden sıralama (AJAX - Drag & Drop)
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'widgets' => 'required|array',
            'widgets.*.id' => 'required|exists:widgets,id',
            'widgets.*.location' => 'required|string',
            'widgets.*.order' => 'required|integer',
        ]);

        foreach ($validated['widgets'] as $widgetData) {
            Widget::where('id', $widgetData['id'])->update([
                'location' => $widgetData['location'],
                'order' => $widgetData['order'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Widget sıralaması başarıyla güncellendi.',
        ]);
    }

    /**
     * Toplu silme
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'widget_ids' => 'required|array',
            'widget_ids.*' => 'exists:widgets,id',
        ]);

        Widget::whereIn('id', $request->widget_ids)->delete();

        return redirect()->back()
            ->with('success', count($request->widget_ids) . ' widget başarıyla silindi.');
    }

    /**
     * Toplu aktif etme
     */
    public function bulkActivate(Request $request)
    {
        $request->validate([
            'widget_ids' => 'required|array',
            'widget_ids.*' => 'exists:widgets,id',
        ]);

        Widget::whereIn('id', $request->widget_ids)->update(['is_active' => true]);

        return redirect()->back()
            ->with('success', count($request->widget_ids) . ' widget başarıyla aktif edildi.');
    }

    /**
     * Toplu pasif etme
     */
    public function bulkDeactivate(Request $request)
    {
        $request->validate([
            'widget_ids' => 'required|array',
            'widget_ids.*' => 'exists:widgets,id',
        ]);

        Widget::whereIn('id', $request->widget_ids)->update(['is_active' => false]);

        return redirect()->back()
            ->with('success', count($request->widget_ids) . ' widget başarıyla pasif edildi.');
    }

    /**
     * Widget önizleme
     */
    public function preview(Widget $widget)
    {
        $renderedContent = $widget->render();

        return view('admin.widgets.preview', compact('widget', 'renderedContent'));
    }

    /**
     * Konum bazlı widget yönetimi sayfası
     */
    public function manage(Request $request)
    {
        $location = $request->get('location', 'sidebar_right');
        $widgets = Widget::where('location', $location)
            ->orderBy('order')
            ->get();

        $locations = Widget::getAvailableLocations();
        $types = Widget::getAvailableTypes();

        return view('admin.widgets.manage', compact('widgets', 'location', 'locations', 'types'));
    }

    /**
     * Widget istatistikleri (AJAX)
     */
    public function statistics()
    {
        $stats = [
            'total' => Widget::count(),
            'active' => Widget::where('is_active', true)->count(),
            'inactive' => Widget::where('is_active', false)->count(),
            'by_type' => Widget::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_location' => Widget::selectRaw('location, COUNT(*) as count')
                ->groupBy('location')
                ->pluck('count', 'location')
                ->toArray(),
        ];

        return response()->json($stats);
    }
}
