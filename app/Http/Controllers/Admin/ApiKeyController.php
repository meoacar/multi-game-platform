<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\ApiLog;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    /**
     * API key listesi
     */
    public function index()
    {
        $apiKeys = ApiKey::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.api.keys.index', compact('apiKeys'));
    }

    /**
     * Yeni API key oluşturma formu
     */
    public function create()
    {
        return view('admin.api.keys.create');
    }

    /**
     * API key oluştur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'rate_limit' => 'required|integer|min:10|max:10000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'ip_whitelist' => 'nullable|string',
            'expires_at' => 'nullable|date|after:today',
        ]);

        // IP whitelist'i temizle
        if ($validated['ip_whitelist']) {
            $ips = explode(',', $validated['ip_whitelist']);
            $ips = array_map('trim', $ips);
            $validated['ip_whitelist'] = implode(',', $ips);
        }

        $apiKey = ApiKey::generate($validated);

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'create_api_key',
            'target_type' => 'ApiKey',
            'target_id' => $apiKey->id,
            'details' => "API key oluşturuldu: {$apiKey->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Ham key'i sadece bir kez göster
        return redirect()->route('admin.api.keys.show', $apiKey)
            ->with('success', 'API key başarıyla oluşturuldu! 🎉')
            ->with('raw_key', $apiKey->raw_key);
    }

    /**
     * API key detayları
     */
    public function show(ApiKey $apiKey)
    {
        $apiKey->load('user');
        
        // Son 100 log kaydı
        $logs = $apiKey->logs()
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        // İstatistikler
        $stats = [
            'total_requests' => $apiKey->logs()->count(),
            'success_requests' => $apiKey->logs()->where('status_code', '<', 400)->count(),
            'failed_requests' => $apiKey->logs()->where('status_code', '>=', 400)->count(),
            'avg_response_time' => $apiKey->logs()->avg('response_time'),
        ];

        return view('admin.api.keys.show', compact('apiKey', 'logs', 'stats'));
    }

    /**
     * API key düzenleme formu
     */
    public function edit(ApiKey $apiKey)
    {
        return view('admin.api.keys.edit', compact('apiKey'));
    }

    /**
     * API key güncelle
     */
    public function update(Request $request, ApiKey $apiKey)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate_limit' => 'required|integer|min:10|max:10000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'ip_whitelist' => 'nullable|string',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        // IP whitelist'i temizle
        if (isset($validated['ip_whitelist'])) {
            $ips = explode(',', $validated['ip_whitelist']);
            $ips = array_map('trim', $ips);
            $validated['ip_whitelist'] = implode(',', $ips);
        }

        $validated['is_active'] = $request->has('is_active');

        $apiKey->update($validated);

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_api_key',
            'target_type' => 'ApiKey',
            'target_id' => $apiKey->id,
            'details' => "API key güncellendi: {$apiKey->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.api.keys.show', $apiKey)
            ->with('success', 'API key başarıyla güncellendi! ✅');
    }

    /**
     * API key sil
     */
    public function destroy(ApiKey $apiKey)
    {
        $name = $apiKey->name;
        $apiKey->delete();

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_api_key',
            'target_type' => 'ApiKey',
            'target_id' => $apiKey->id,
            'details' => "API key silindi: {$name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.api.keys.index')
            ->with('success', 'API key başarıyla silindi! 🗑️');
    }

    /**
     * API key'i aktif/pasif yap
     */
    public function toggle(ApiKey $apiKey)
    {
        $apiKey->update(['is_active' => !$apiKey->is_active]);

        $status = $apiKey->is_active ? 'aktif' : 'pasif';

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'toggle_api_key',
            'target_type' => 'ApiKey',
            'target_id' => $apiKey->id,
            'details' => "API key {$status} yapıldı: {$apiKey->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "API key {$status} yapıldı! ✅");
    }

    /**
     * API logları
     */
    public function logs(Request $request)
    {
        $query = ApiLog::with('apiKey');

        // Filtreleme
        if ($request->filled('api_key_id')) {
            $query->where('api_key_id', $request->api_key_id);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('status_code')) {
            $query->where('status_code', $request->status_code);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(50);

        $apiKeys = ApiKey::where('is_active', true)->get();

        return view('admin.api.logs', compact('logs', 'apiKeys'));
    }
}
