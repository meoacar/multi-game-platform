<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;

/**
 * Admin SEO Controller
 * SEO ayarlarını yönetir
 */
class SeoController extends Controller
{
    /**
     * SEO ayarları listesi
     */
    public function index()
    {
        $seoSettings = SeoSetting::orderBy('page_type')->get();
        
        return view('admin.seo.index', compact('seoSettings'));
    }

    /**
     * Yeni SEO ayarı oluşturma formu
     */
    public function create()
    {
        $pageTypes = $this->getAvailablePageTypes();
        
        return view('admin.seo.create', compact('pageTypes'));
    }

    /**
     * Yeni SEO ayarı kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_type' => 'required|string|unique:seo_settings,page_type',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|url',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|url',
        ]);

        // is_active checkbox'ı ayrı kontrol et
        $validated['is_active'] = $request->has('is_active') ? true : false;

        SeoSetting::create($validated);

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO ayarları oluşturuldu!');
    }

    /**
     * SEO ayarı düzenleme formu
     */
    public function edit(SeoSetting $seoSetting)
    {
        $pageTypes = $this->getAvailablePageTypes();
        
        return view('admin.seo.edit', compact('seoSetting', 'pageTypes'));
    }

    /**
     * SEO ayarı güncelle
     */
    public function update(Request $request, SeoSetting $seoSetting)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|url',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|url',
        ]);

        // is_active checkbox'ı ayrı kontrol et
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $seoSetting->update($validated);

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO ayarları güncellendi!');
    }

    /**
     * SEO ayarı sil
     */
    public function destroy(SeoSetting $seoSetting)
    {
        $seoSetting->delete();

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO ayarları silindi!');
    }

    /**
     * Aktif/Pasif toggle
     */
    public function toggleActive(SeoSetting $seoSetting)
    {
        $seoSetting->update(['is_active' => !$seoSetting->is_active]);

        return back()->with('success', 'Durum güncellendi!');
    }

    /**
     * Tüm cache'i temizle
     */
    public function clearCache()
    {
        $seoSettings = SeoSetting::all();
        
        foreach ($seoSettings as $setting) {
            SeoSetting::clearCache($setting->page_type);
        }

        return back()->with('success', 'SEO cache temizlendi!');
    }

    /**
     * Sitemap.xml oluştur (Public)
     */
    public function sitemap()
    {
        $urls = [];
        
        // Ana sayfa
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];
        
        // Statik sayfalar
        $staticPages = [
            '/ilanlar' => ['changefreq' => 'hourly', 'priority' => '0.9'],
            '/klanlar' => ['changefreq' => 'daily', 'priority' => '0.9'],
            '/rehber' => ['changefreq' => 'weekly', 'priority' => '0.8'],
            '/topluluk' => ['changefreq' => 'daily', 'priority' => '0.8'],
            '/turnuvalar' => ['changefreq' => 'daily', 'priority' => '0.8'],
            '/cihazlar' => ['changefreq' => 'weekly', 'priority' => '0.7'],
            '/liderlik-tablosu' => ['changefreq' => 'daily', 'priority' => '0.7'],
        ];
        
        foreach ($staticPages as $url => $meta) {
            $urls[] = array_merge([
                'loc' => url($url),
                'lastmod' => now()->toAtomString(),
            ], $meta);
        }
        
        // Dinamik içerikler (son 1000 kayıt)
        // Klanlar
        $clans = \App\Models\Clan::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->limit(1000)
            ->get(['slug', 'updated_at']);
        
        foreach ($clans as $clan) {
            $urls[] = [
                'loc' => route('clans.show', $clan->slug),
                'lastmod' => $clan->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ];
        }
        
        // LFG İlanları
        $lfgPosts = \App\Models\LfgPost::where('status', 'open')
            ->orderBy('updated_at', 'desc')
            ->limit(1000)
            ->get(['id', 'updated_at']);
        
        foreach ($lfgPosts as $post) {
            $urls[] = [
                'loc' => route('lfg.show', $post->id),
                'lastmod' => $post->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.6'
            ];
        }
        
        // Rehberler
        $guides = \App\Models\Guide::where('is_published', true)
            ->orderBy('updated_at', 'desc')
            ->limit(1000)
            ->get(['id', 'updated_at']);
        
        foreach ($guides as $guide) {
            $urls[] = [
                'loc' => route('guide.show', $guide->id),
                'lastmod' => $guide->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ];
        }
        
        // Topluluk Gönderileri
        $posts = \App\Models\CommunityPost::orderBy('updated_at', 'desc')
            ->limit(1000)
            ->get(['id', 'updated_at']);
        
        foreach ($posts as $post) {
            $urls[] = [
                'loc' => route('community.show', $post->id),
                'lastmod' => $post->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.5'
            ];
        }
        
        return response()->view('seo.sitemap', compact('urls'))
            ->header('Content-Type', 'text/xml');
    }
    
    /**
     * Robots.txt oluştur (Public)
     */
    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Disallow: /ayarlar/\n";
        $content .= "Disallow: /profilim/\n";
        $content .= "\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
        
        return response($content)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Mevcut sayfa tiplerini getir
     */
    private function getAvailablePageTypes(): array
    {
        return [
            'home' => 'Ana Sayfa',
            'clans' => 'Klanlar',
            'clans_show' => 'Klan Detay',
            'lfg' => 'İlanlar',
            'lfg_show' => 'İlan Detay',
            'guides' => 'Rehberler',
            'guides_show' => 'Rehber Detay',
            'community' => 'Topluluk',
            'tournaments' => 'Turnuvalar',
            'devices' => 'Cihaz Ayarları',
            'profile' => 'Profil',
            'leaderboard' => 'Liderlik Tablosu',
        ];
    }
}
