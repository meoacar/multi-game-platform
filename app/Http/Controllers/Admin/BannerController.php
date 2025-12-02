<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * BannerController
 * 
 * Banner/slider yönetimi için controller
 * 
 * Özellikler:
 * - Banner CRUD işlemleri
 * - Zamanlama (başlangıç-bitiş tarihi)
 * - Hedef kitle segmentasyonu
 * - A/B testing yönetimi
 * - İstatistik takibi
 * - Toplu işlemler
 * - AJAX API endpoint'leri
 */
class BannerController extends Controller
{
    /**
     * Banner listesi
     */
    public function index(Request $request)
    {
        $query = Banner::with(['abTestParent', 'abTestVariants']);

        // Konum filtresi
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        // Durum filtresi
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'scheduled':
                    $query->where('is_active', true)
                          ->where('start_date', '>', now());
                    break;
                case 'expired':
                    $query->where('end_date', '<', now());
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        // Hedef kitle filtresi
        if ($request->filled('target_audience')) {
            $query->where('target_audience', $request->target_audience);
        }

        // A/B test filtresi
        if ($request->filled('ab_test')) {
            if ($request->ab_test === 'yes') {
                $query->where('is_ab_test', true);
            } else {
                $query->where('is_ab_test', false);
            }
        }

        // Arama
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $banners = $query->paginate(20)->withQueryString();

        $locations = Banner::getAvailableLocations();
        $targetAudiences = Banner::getTargetAudiences();

        return view('admin.banners.index', compact('banners', 'locations', 'targetAudiences'));
    }

    /**
     * Yeni banner oluşturma formu
     */
    public function create()
    {
        $locations = Banner::getAvailableLocations();
        $targetAudiences = Banner::getTargetAudiences();
        $parentBanners = Banner::abTestParents()->get();

        return view('admin.banners.create', compact('locations', 'targetAudiences', 'parentBanners'));
    }

    /**
     * Banner kaydetme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:banners,slug',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_url' => 'nullable|url|max:500',
            'link_target' => 'required|in:_self,_blank',
            'location' => 'required|string|in:' . implode(',', array_keys(Banner::getAvailableLocations())),
            'order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'target_audience' => 'required|string|in:' . implode(',', array_keys(Banner::getTargetAudiences())),
            'target_criteria' => 'nullable|array',
            'is_ab_test' => 'boolean',
            'ab_test_group' => 'nullable|string|max:50',
            'ab_test_parent_id' => 'nullable|exists:banners,id',
            'ab_test_weight' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
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
        while (Banner::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Görsel yükleme
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('banners', 'public');
        }

        // Mobil görsel yükleme
        if ($request->hasFile('mobile_image')) {
            $validated['mobile_image_path'] = $request->file('mobile_image')->store('banners/mobile', 'public');
        }

        // Eğer order belirtilmemişse, en sona ekle
        if (!isset($validated['order'])) {
            $validated['order'] = Banner::where('location', $validated['location'])->max('order') + 1;
        }

        // Image ve mobile_image alanlarını kaldır (image_path ve mobile_image_path kullanıyoruz)
        unset($validated['image'], $validated['mobile_image']);

        $banner = Banner::create($validated);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner başarıyla oluşturuldu.');
    }

    /**
     * Banner detay sayfası
     */
    public function show(Banner $banner)
    {
        $banner->load(['abTestParent', 'abTestVariants']);

        // İstatistikler
        $stats = [
            'ctr' => $banner->getClickThroughRate(),
            'is_active' => $banner->isActive(),
            'is_scheduled' => $banner->isScheduled(),
            'is_expired' => $banner->isExpired(),
        ];

        return view('admin.banners.show', compact('banner', 'stats'));
    }

    /**
     * Banner düzenleme formu
     */
    public function edit(Banner $banner)
    {
        $locations = Banner::getAvailableLocations();
        $targetAudiences = Banner::getTargetAudiences();
        $parentBanners = Banner::abTestParents()
            ->where('id', '!=', $banner->id)
            ->get();

        return view('admin.banners.edit', compact('banner', 'locations', 'targetAudiences', 'parentBanners'));
    }

    /**
     * Banner güncelleme
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:banners,slug,' . $banner->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_url' => 'nullable|url|max:500',
            'link_target' => 'required|in:_self,_blank',
            'location' => 'required|string|in:' . implode(',', array_keys(Banner::getAvailableLocations())),
            'order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'target_audience' => 'required|string|in:' . implode(',', array_keys(Banner::getTargetAudiences())),
            'target_criteria' => 'nullable|array',
            'is_ab_test' => 'boolean',
            'ab_test_group' => 'nullable|string|max:50',
            'ab_test_parent_id' => 'nullable|exists:banners,id',
            'ab_test_weight' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
        ]);

        // Slug güncelle
        if (!empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        // Yeni görsel yükleme
        if ($request->hasFile('image')) {
            // Eski görseli sil
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('banners', 'public');
        }

        // Yeni mobil görsel yükleme
        if ($request->hasFile('mobile_image')) {
            // Eski görseli sil
            if ($banner->mobile_image_path) {
                Storage::disk('public')->delete($banner->mobile_image_path);
            }
            $validated['mobile_image_path'] = $request->file('mobile_image')->store('banners/mobile', 'public');
        }

        // Image ve mobile_image alanlarını kaldır
        unset($validated['image'], $validated['mobile_image']);

        $banner->update($validated);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner başarıyla güncellendi.');
    }

    /**
     * Banner silme
     */
    public function destroy(Banner $banner)
    {
        // Görselleri sil
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        if ($banner->mobile_image_path) {
            Storage::disk('public')->delete($banner->mobile_image_path);
        }

        $banner->delete();

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner başarıyla silindi.');
    }

    /**
     * Banner durumu değiştirme
     */
    public function toggleStatus(Banner $banner)
    {
        $banner->update([
            'is_active' => !$banner->is_active
        ]);

        $status = $banner->is_active ? 'aktif edildi' : 'pasif edildi';

        return redirect()->back()
            ->with('success', "Banner başarıyla {$status}.");
    }

    /**
     * Banner'ları yeniden sıralama (AJAX - Drag & Drop)
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:banners,id',
            'banners.*.location' => 'required|string',
            'banners.*.order' => 'required|integer',
        ]);

        foreach ($validated['banners'] as $bannerData) {
            Banner::where('id', $bannerData['id'])->update([
                'location' => $bannerData['location'],
                'order' => $bannerData['order'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Banner sıralaması başarıyla güncellendi.',
        ]);
    }

    /**
     * Toplu silme
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'banner_ids' => 'required|array',
            'banner_ids.*' => 'exists:banners,id',
        ]);

        $banners = Banner::whereIn('id', $request->banner_ids)->get();

        foreach ($banners as $banner) {
            // Görselleri sil
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            if ($banner->mobile_image_path) {
                Storage::disk('public')->delete($banner->mobile_image_path);
            }
            $banner->delete();
        }

        return redirect()->back()
            ->with('success', count($request->banner_ids) . ' banner başarıyla silindi.');
    }

    /**
     * Toplu aktif etme
     */
    public function bulkActivate(Request $request)
    {
        $request->validate([
            'banner_ids' => 'required|array',
            'banner_ids.*' => 'exists:banners,id',
        ]);

        Banner::whereIn('id', $request->banner_ids)->update(['is_active' => true]);

        return redirect()->back()
            ->with('success', count($request->banner_ids) . ' banner başarıyla aktif edildi.');
    }

    /**
     * Toplu pasif etme
     */
    public function bulkDeactivate(Request $request)
    {
        $request->validate([
            'banner_ids' => 'required|array',
            'banner_ids.*' => 'exists:banners,id',
        ]);

        Banner::whereIn('id', $request->banner_ids)->update(['is_active' => false]);

        return redirect()->back()
            ->with('success', count($request->banner_ids) . ' banner başarıyla pasif edildi.');
    }

    /**
     * Banner önizleme
     */
    public function preview(Banner $banner)
    {
        return view('admin.banners.preview', compact('banner'));
    }

    /**
     * Banner istatistikleri (AJAX)
     */
    public function statistics(Banner $banner)
    {
        $stats = [
            'view_count' => $banner->view_count,
            'click_count' => $banner->click_count,
            'ctr' => $banner->getClickThroughRate(),
            'is_active' => $banner->isActive(),
            'is_scheduled' => $banner->isScheduled(),
            'is_expired' => $banner->isExpired(),
        ];

        // A/B test istatistikleri
        if ($banner->is_ab_test && $banner->abTestVariants->count() > 0) {
            $stats['ab_test'] = [
                'variants' => $banner->abTestVariants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'group' => $variant->ab_test_group,
                        'view_count' => $variant->view_count,
                        'click_count' => $variant->click_count,
                        'ctr' => $variant->getClickThroughRate(),
                        'weight' => $variant->ab_test_weight,
                    ];
                }),
            ];
        }

        return response()->json($stats);
    }

    /**
     * Genel banner istatistikleri (AJAX)
     */
    public function overallStatistics()
    {
        $stats = [
            'total' => Banner::count(),
            'active' => Banner::active()->count(),
            'scheduled' => Banner::where('is_active', true)
                ->where('start_date', '>', now())
                ->count(),
            'expired' => Banner::where('end_date', '<', now())->count(),
            'inactive' => Banner::where('is_active', false)->count(),
            'ab_tests' => Banner::where('is_ab_test', true)->count(),
            'by_location' => Banner::selectRaw('location, COUNT(*) as count')
                ->groupBy('location')
                ->pluck('count', 'location')
                ->toArray(),
            'by_target_audience' => Banner::selectRaw('target_audience, COUNT(*) as count')
                ->groupBy('target_audience')
                ->pluck('count', 'target_audience')
                ->toArray(),
            'total_views' => Banner::sum('view_count'),
            'total_clicks' => Banner::sum('click_count'),
        ];

        return response()->json($stats);
    }

    /**
     * Banner görüntülenme kaydı (AJAX - Frontend'den çağrılır)
     */
    public function trackView(Banner $banner)
    {
        $banner->incrementViews();

        return response()->json([
            'success' => true,
            'view_count' => $banner->view_count,
        ]);
    }

    /**
     * Banner tıklama kaydı (AJAX - Frontend'den çağrılır)
     */
    public function trackClick(Banner $banner)
    {
        $banner->incrementClicks();

        return response()->json([
            'success' => true,
            'click_count' => $banner->click_count,
        ]);
    }

    /**
     * A/B test varyantı oluşturma
     */
    public function createAbTestVariant(Banner $banner)
    {
        // Parent banner A/B test olarak işaretle
        if (!$banner->is_ab_test) {
            $banner->update([
                'is_ab_test' => true,
                'ab_test_group' => 'A',
            ]);
        }

        $locations = Banner::getAvailableLocations();
        $targetAudiences = Banner::getTargetAudiences();

        return view('admin.banners.create-variant', compact('banner', 'locations', 'targetAudiences'));
    }

    /**
     * A/B test raporu
     */
    public function abTestReport(Banner $banner)
    {
        if (!$banner->is_ab_test || $banner->ab_test_parent_id) {
            return redirect()
                ->route('admin.banners.index')
                ->with('error', 'Bu banner bir A/B test parent değil.');
        }

        $banner->load('abTestVariants');

        // Her varyant için istatistikler
        $variants = collect([$banner])->merge($banner->abTestVariants)->map(function ($variant) {
            return [
                'id' => $variant->id,
                'title' => $variant->title,
                'group' => $variant->ab_test_group,
                'view_count' => $variant->view_count,
                'click_count' => $variant->click_count,
                'ctr' => $variant->getClickThroughRate(),
                'weight' => $variant->ab_test_weight,
            ];
        });

        // Kazanan varyantı belirle (en yüksek CTR)
        $winner = $variants->sortByDesc('ctr')->first();

        return view('admin.banners.ab-test-report', compact('banner', 'variants', 'winner'));
    }

    /**
     * Konum bazlı banner yönetimi sayfası
     */
    public function manage(Request $request)
    {
        $location = $request->get('location', 'home_hero');
        $banners = Banner::where('location', $location)
            ->orderBy('order')
            ->get();

        $locations = Banner::getAvailableLocations();

        return view('admin.banners.manage', compact('banners', 'location', 'locations'));
    }
}
