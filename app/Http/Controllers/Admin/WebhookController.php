<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * Webhook listesi
     */
    public function index()
    {
        $webhooks = Webhook::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.api.webhooks.index', compact('webhooks'));
    }

    /**
     * Yeni webhook oluşturma formu
     */
    public function create()
    {
        $availableEvents = $this->getAvailableEvents();
        return view('admin.api.webhooks.create', compact('availableEvents'));
    }

    /**
     * Webhook oluştur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'user_id' => 'nullable|exists:users,id',
            'events' => 'required|array|min:1',
            'events.*' => 'string',
            'secret' => 'nullable|string|max:64',
            'timeout' => 'required|integer|min:5|max:120',
            'retry_count' => 'required|integer|min:0|max:5',
            'headers' => 'nullable|array',
        ]);

        $webhook = Webhook::create($validated);

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'create_webhook',
            'target_type' => 'Webhook',
            'target_id' => $webhook->id,
            'details' => "Webhook oluşturuldu: {$webhook->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.api.webhooks.show', $webhook)
            ->with('success', 'Webhook başarıyla oluşturuldu! 🎉');
    }

    /**
     * Webhook detayları
     */
    public function show(Webhook $webhook)
    {
        $webhook->load('user');
        
        // Son 100 log kaydı
        $logs = $webhook->logs()
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        // İstatistikler
        $stats = [
            'total_triggers' => $webhook->logs()->count(),
            'success_rate' => $webhook->success_count + $webhook->failure_count > 0 
                ? round(($webhook->success_count / ($webhook->success_count + $webhook->failure_count)) * 100, 2)
                : 0,
            'avg_response_time' => $webhook->logs()->avg('response_time'),
        ];

        return view('admin.api.webhooks.show', compact('webhook', 'logs', 'stats'));
    }

    /**
     * Webhook düzenleme formu
     */
    public function edit(Webhook $webhook)
    {
        $availableEvents = $this->getAvailableEvents();
        return view('admin.api.webhooks.edit', compact('webhook', 'availableEvents'));
    }

    /**
     * Webhook güncelle
     */
    public function update(Request $request, Webhook $webhook)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'events.*' => 'string',
            'secret' => 'nullable|string|max:64',
            'timeout' => 'required|integer|min:5|max:120',
            'retry_count' => 'required|integer|min:0|max:5',
            'headers' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $webhook->update($validated);

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_webhook',
            'target_type' => 'Webhook',
            'target_id' => $webhook->id,
            'details' => "Webhook güncellendi: {$webhook->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.api.webhooks.show', $webhook)
            ->with('success', 'Webhook başarıyla güncellendi! ✅');
    }

    /**
     * Webhook sil
     */
    public function destroy(Webhook $webhook)
    {
        $name = $webhook->name;
        $webhook->delete();

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_webhook',
            'target_type' => 'Webhook',
            'target_id' => $webhook->id,
            'details' => "Webhook silindi: {$name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.api.webhooks.index')
            ->with('success', 'Webhook başarıyla silindi! 🗑️');
    }

    /**
     * Webhook'u test et
     */
    public function test(Webhook $webhook)
    {
        $testPayload = [
            'test' => true,
            'message' => 'Bu bir test webhook çağrısıdır',
            'webhook_id' => $webhook->id,
            'webhook_name' => $webhook->name,
        ];

        $success = $webhook->trigger('webhook.test', $testPayload);

        if ($success) {
            return back()->with('success', 'Test webhook başarıyla gönderildi! ✅');
        }

        return back()->with('error', 'Test webhook gönderilemedi! ❌');
    }

    /**
     * Webhook'u aktif/pasif yap
     */
    public function toggle(Webhook $webhook)
    {
        $webhook->update(['is_active' => !$webhook->is_active]);

        $status = $webhook->is_active ? 'aktif' : 'pasif';

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'toggle_webhook',
            'target_type' => 'Webhook',
            'target_id' => $webhook->id,
            'details' => "Webhook {$status} yapıldı: {$webhook->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Webhook {$status} yapıldı! ✅");
    }

    /**
     * Mevcut olaylar listesi
     */
    private function getAvailableEvents(): array
    {
        return [
            'user.created' => 'Yeni kullanıcı kaydı',
            'user.updated' => 'Kullanıcı güncellendi',
            'user.deleted' => 'Kullanıcı silindi',
            'user.banned' => 'Kullanıcı banlandı',
            'post.created' => 'Yeni gönderi oluşturuldu',
            'post.updated' => 'Gönderi güncellendi',
            'post.deleted' => 'Gönderi silindi',
            'comment.created' => 'Yeni yorum yapıldı',
            'comment.deleted' => 'Yorum silindi',
            'lfg.created' => 'Yeni LFG ilanı',
            'lfg.application' => 'LFG başvurusu',
            'clan.created' => 'Yeni klan oluşturuldu',
            'clan.application' => 'Klan başvurusu',
            'guide.created' => 'Yeni rehber oluşturuldu',
            'guide.published' => 'Rehber yayınlandı',
            'report.created' => 'Yeni rapor',
            'report.resolved' => 'Rapor çözüldü',
        ];
    }
}
