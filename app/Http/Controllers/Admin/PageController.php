<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();

        // Arama
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // Yayın durumu filtresi
        if ($request->filled('is_published')) {
            $query->where('is_published', $request->is_published === 'published');
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $pages = $query->paginate(20)->withQueryString();

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $templates = Page::getAvailableTemplates();
        return view('admin.pages.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'template' => 'required|string|in:' . implode(',', array_keys(Page::getAvailableTemplates())),
            'is_published' => 'boolean',
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
        while (Page::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        Page::create($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Sayfa başarıyla oluşturuldu.');
    }

    public function edit(Page $page)
    {
        $templates = Page::getAvailableTemplates();
        return view('admin.pages.edit', compact('page', 'templates'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'template' => 'required|string|in:' . implode(',', array_keys(Page::getAvailableTemplates())),
            'is_published' => 'boolean',
        ]);

        // Slug güncelle
        if (!empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Sayfa başarıyla güncellendi.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Sayfa başarıyla silindi.');
    }

    public function togglePublish(Page $page)
    {
        $page->update([
            'is_published' => !$page->is_published
        ]);

        $status = $page->is_published ? 'yayınlandı' : 'yayından kaldırıldı';

        return redirect()->back()
            ->with('success', "Sayfa başarıyla {$status}.");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'page_ids' => 'required|array',
            'page_ids.*' => 'exists:pages,id',
        ]);

        Page::whereIn('id', $request->page_ids)->delete();

        return redirect()->back()
            ->with('success', count($request->page_ids) . ' sayfa başarıyla silindi.');
    }

    public function bulkPublish(Request $request)
    {
        $request->validate([
            'page_ids' => 'required|array',
            'page_ids.*' => 'exists:pages,id',
        ]);

        Page::whereIn('id', $request->page_ids)->update(['is_published' => true]);

        return redirect()->back()
            ->with('success', count($request->page_ids) . ' sayfa başarıyla yayınlandı.');
    }

    public function bulkUnpublish(Request $request)
    {
        $request->validate([
            'page_ids' => 'required|array',
            'page_ids.*' => 'exists:pages,id',
        ]);

        Page::whereIn('id', $request->page_ids)->update(['is_published' => false]);

        return redirect()->back()
            ->with('success', count($request->page_ids) . ' sayfa başarıyla yayından kaldırıldı.');
    }

    /**
     * Sayfa önizlemesi
     */
    public function preview(Page $page)
    {
        // Önizleme için özel layout kullan
        return view('admin.pages.preview', compact('page'));
    }

    /**
     * Sayfa istatistikleri
     */
    public function statistics()
    {
        $stats = [
            'total' => Page::count(),
            'published' => Page::where('is_published', true)->count(),
            'draft' => Page::where('is_published', false)->count(),
            'deleted' => Page::onlyTrashed()->count(),
            'by_template' => Page::selectRaw('template, COUNT(*) as count')
                ->groupBy('template')
                ->pluck('count', 'template')
                ->toArray(),
        ];

        return response()->json($stats);
    }
}
