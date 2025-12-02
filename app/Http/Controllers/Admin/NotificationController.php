<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkNotification;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * Admin Bildirim Controller
 * 
 * Toplu bildirim gönderimi, email şablonları ve bülten yönetimi
 * 
 * Özellikler:
 * - Toplu bildirim oluşturma ve gönderme
 * - Email şablonu yönetimi
 * - Hedef kitle segmentasyonu
 * - Zamanlanmış bildirimler
 * - Bildirim istatistikleri
 */
class NotificationController extends Controller
{
    /**
     * Toplu bildirim listesi
     */
    public function index()
    {
        $notifications = BulkNotification::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $statistics = [
            'total' => BulkNotification::count(),
            'draft' => BulkNotification::draft()->count(),
            'scheduled' => BulkNotification::scheduled()->count(),
            'sent' => BulkNotification::sent()->count(),
            'total_recipients' => BulkNotification::sum('total_recipients'),
            'total_sent' => BulkNotification::sum('sent_count'),
        ];

        return view('admin.notifications.index', compact('notifications', 'statistics'));
    }

    /**
     * Yeni toplu bildirim oluşturma formu
     */
    public function create()
    {
        // Email şablonlarını getir
        $emailTemplates = EmailTemplate::active()->get();

        // Segment seçenekleri
        $segments = [
            'all' => 'Tüm Kullanıcılar',
            'new' => 'Yeni Kullanıcılar (Son 7 gün)',
            'active' => 'Aktif Kullanıcılar (Son 30 gün)',
            'inactive' => 'Pasif Kullanıcılar (30+ gün)',
            'verified' => 'Email Doğrulanmış',
            'unverified' => 'Email Doğrulanmamış',
            'custom' => 'Özel Seçim',
        ];

        return view('admin.notifications.create', compact('emailTemplates', 'segments'));
    }

    /**
     * Toplu bildirim kaydetme
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:email,sms,push,site',
            'target_type' => 'required|in:all,segment,custom',
            'target_criteria' => 'nullable|array',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'scheduled_at' => 'nullable|date|after:now',
            'email_template_id' => 'nullable|exists:email_templates,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Bildirimi oluştur
            $notification = BulkNotification::create([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'target_type' => $request->target_type,
                'target_criteria' => $request->target_criteria,
                'user_ids' => $request->user_ids,
                'status' => $request->scheduled_at ? 'scheduled' : 'draft',
                'scheduled_at' => $request->scheduled_at,
                'created_by' => auth()->id(),
            ]);

            // Toplam alıcı sayısını hesapla
            $notification->calculateTotalRecipients();

            // Admin aktivitesini logla
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'create_bulk_notification',
                'target_type' => 'BulkNotification',
                'target_id' => $notification->id,
                'details' => "Toplu bildirim oluşturuldu: {$notification->title}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.notifications.show', $notification->id)
                ->with('success', 'Toplu bildirim başarıyla oluşturuldu! 📢');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bildirim oluşturulurken hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Toplu bildirim detayı
     */
    public function show($id)
    {
        $notification = BulkNotification::with('creator')->findOrFail($id);

        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Toplu bildirim düzenleme formu
     */
    public function edit($id)
    {
        $notification = BulkNotification::findOrFail($id);

        // Sadece taslak ve zamanlanmış bildirimleri düzenleyebiliriz
        if (!in_array($notification->status, ['draft', 'scheduled'])) {
            return back()->with('error', 'Bu bildirim düzenlenemez!');
        }

        $emailTemplates = EmailTemplate::active()->get();

        $segments = [
            'all' => 'Tüm Kullanıcılar',
            'new' => 'Yeni Kullanıcılar (Son 7 gün)',
            'active' => 'Aktif Kullanıcılar (Son 30 gün)',
            'inactive' => 'Pasif Kullanıcılar (30+ gün)',
            'verified' => 'Email Doğrulanmış',
            'unverified' => 'Email Doğrulanmamış',
            'custom' => 'Özel Seçim',
        ];

        return view('admin.notifications.edit', compact('notification', 'emailTemplates', 'segments'));
    }

    /**
     * Toplu bildirim güncelleme
     */
    public function update(Request $request, $id)
    {
        $notification = BulkNotification::findOrFail($id);

        // Sadece taslak ve zamanlanmış bildirimleri güncelleyebiliriz
        if (!in_array($notification->status, ['draft', 'scheduled'])) {
            return back()->with('error', 'Bu bildirim güncellenemez!');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:email,sms,push,site',
            'target_type' => 'required|in:all,segment,custom',
            'target_criteria' => 'nullable|array',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $notification->update([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'target_type' => $request->target_type,
                'target_criteria' => $request->target_criteria,
                'user_ids' => $request->user_ids,
                'status' => $request->scheduled_at ? 'scheduled' : 'draft',
                'scheduled_at' => $request->scheduled_at,
            ]);

            // Toplam alıcı sayısını yeniden hesapla
            $notification->calculateTotalRecipients();

            // Admin aktivitesini logla
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'update_bulk_notification',
                'target_type' => 'BulkNotification',
                'target_id' => $notification->id,
                'details' => "Toplu bildirim güncellendi: {$notification->title}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.notifications.show', $notification->id)
                ->with('success', 'Toplu bildirim başarıyla güncellendi! ✅');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bildirim güncellenirken hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Toplu bildirim silme
     */
    public function destroy($id)
    {
        $notification = BulkNotification::findOrFail($id);

        // Gönderilme aşamasındaki bildirimleri silemeyiz
        if ($notification->status === 'sending') {
            return back()->with('error', 'Gönderilme aşamasındaki bildirim silinemez!');
        }

        $title = $notification->title;
        $notification->delete();

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_bulk_notification',
            'target_type' => 'BulkNotification',
            'target_id' => $id,
            'details' => "Toplu bildirim silindi: {$title}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Toplu bildirim başarıyla silindi! 🗑️');
    }

    /**
     * Toplu bildirim gönderme
     */
    public function send($id)
    {
        $notification = BulkNotification::findOrFail($id);

        // Gönderilmeye hazır mı kontrol et
        if (!$notification->isReadyToSend()) {
            return back()->with('error', 'Bu bildirim gönderilmeye hazır değil!');
        }

        DB::beginTransaction();
        try {
            // Durumu "gönderiliyor" olarak işaretle
            $notification->markAsSending();

            // Hedef kullanıcıları al
            $userIds = $notification->getTargetUserIds();

            // Bildirim tipine göre gönder
            switch ($notification->type) {
                case 'email':
                    $this->sendEmailNotification($notification, $userIds);
                    break;
                case 'sms':
                    $this->sendSmsNotification($notification, $userIds);
                    break;
                case 'push':
                    $this->sendPushNotification($notification, $userIds);
                    break;
                case 'site':
                    $this->sendSiteNotification($notification, $userIds);
                    break;
            }

            // Durumu "gönderildi" olarak işaretle
            $notification->markAsSent();

            // Admin aktivitesini logla
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'send_bulk_notification',
                'target_type' => 'BulkNotification',
                'target_id' => $notification->id,
                'details' => "Toplu bildirim gönderildi: {$notification->title} ({$notification->total_recipients} alıcı)",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.notifications.show', $notification->id)
                ->with('success', 'Toplu bildirim başarıyla gönderildi! 🚀');
        } catch (\Exception $e) {
            DB::rollBack();
            $notification->markAsFailed();
            
            return back()->with('error', 'Bildirim gönderilirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Email bildirimi gönder
     */
    private function sendEmailNotification($notification, $userIds)
    {
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            try {
                Mail::raw($notification->message, function ($message) use ($user, $notification) {
                    $message->to($user->email)
                        ->subject($notification->title);
                });

                $notification->incrementSentCount();
            } catch (\Exception $e) {
                $notification->incrementFailedCount();
                \Log::error("Email gönderimi başarısız: {$user->email} - " . $e->getMessage());
            }
        }
    }

    /**
     * SMS bildirimi gönder
     */
    private function sendSmsNotification($notification, $userIds)
    {
        // SMS gönderimi için üçüncü parti servis entegrasyonu gerekli
        // Şimdilik sadece sayaçları güncelle
        $users = User::whereIn('id', $userIds)->whereNotNull('phone')->get();

        foreach ($users as $user) {
            // TODO: SMS servisi entegrasyonu
            $notification->incrementSentCount();
        }
    }

    /**
     * Push bildirimi gönder
     */
    private function sendPushNotification($notification, $userIds)
    {
        $users = User::whereIn('id', $userIds)
            ->whereNotNull('fcm_token')
            ->get();

        $fcmService = app(\App\Services\FcmService::class);

        foreach ($users as $user) {
            try {
                $fcmService->sendToUser(
                    $user,
                    $notification->title,
                    $notification->message,
                    [
                        'notification_id' => $notification->id,
                        'type' => 'bulk_notification',
                    ]
                );

                $notification->incrementSentCount();
            } catch (\Exception $e) {
                $notification->incrementFailedCount();
                \Log::error("Push notification başarısız: {$user->id} - " . $e->getMessage());
            }
        }
    }

    /**
     * Site içi bildirim gönder
     */
    private function sendSiteNotification($notification, $userIds)
    {
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            try {
                $user->notify(new \App\Notifications\BulkNotificationReceived($notification));
                $notification->incrementSentCount();
            } catch (\Exception $e) {
                $notification->incrementFailedCount();
                \Log::error("Site bildirimi başarısız: {$user->id} - " . $e->getMessage());
            }
        }
    }

    /**
     * Email şablonları listesi
     */
    public function templates()
    {
        $templates = EmailTemplate::orderBy('name')->paginate(20);

        return view('admin.notifications.templates.index', compact('templates'));
    }

    /**
     * Yeni email şablonu oluşturma formu
     */
    public function createTemplate()
    {
        return view('admin.notifications.templates.create');
    }

    /**
     * Email şablonu kaydetme
     */
    public function storeTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:email_templates,slug',
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $template = EmailTemplate::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'subject' => $request->subject,
                'body' => $request->body,
                'variables' => $request->variables,
                'is_active' => $request->has('is_active'),
            ]);

            // Admin aktivitesini logla
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'create_email_template',
                'target_type' => 'EmailTemplate',
                'target_id' => $template->id,
                'details' => "Email şablonu oluşturuldu: {$template->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.notifications.templates')
                ->with('success', 'Email şablonu başarıyla oluşturuldu! 📧');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Şablon oluşturulurken hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Email şablonu düzenleme formu
     */
    public function editTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);

        return view('admin.notifications.templates.edit', compact('template'));
    }

    /**
     * Email şablonu güncelleme
     */
    public function updateTemplate(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:email_templates,slug,' . $id,
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $template->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'subject' => $request->subject,
                'body' => $request->body,
                'variables' => $request->variables,
                'is_active' => $request->has('is_active'),
            ]);

            // Admin aktivitesini logla
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'update_email_template',
                'target_type' => 'EmailTemplate',
                'target_id' => $template->id,
                'details' => "Email şablonu güncellendi: {$template->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.notifications.templates')
                ->with('success', 'Email şablonu başarıyla güncellendi! ✅');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Şablon güncellenirken hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Email şablonu silme
     */
    public function destroyTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $name = $template->name;
        
        $template->delete();

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_email_template',
            'target_type' => 'EmailTemplate',
            'target_id' => $id,
            'details' => "Email şablonu silindi: {$name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.notifications.templates')
            ->with('success', 'Email şablonu başarıyla silindi! 🗑️');
    }

    /**
     * Email şablonu önizleme
     */
    public function previewTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);

        // Örnek veri ile önizleme
        $sampleData = [
            'name' => 'Ahmet Yılmaz',
            'email' => 'ahmet@example.com',
            'site_name' => config('app.name'),
            'site_url' => url('/'),
        ];

        $preview = $template->preview($sampleData);
        $preview['sample_data'] = $sampleData; // Örnek veriyi de gönder

        return view('admin.notifications.templates.preview', compact('template', 'preview'));
    }

    /**
     * Email şablonu kopyalama
     */
    public function duplicateTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);
        
        $newTemplate = $template->duplicate();

        // Admin aktivitesini logla
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'duplicate_email_template',
            'target_type' => 'EmailTemplate',
            'target_id' => $newTemplate->id,
            'details' => "Email şablonu kopyalandı: {$template->name} -> {$newTemplate->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.notifications.templates.edit', $newTemplate->id)
            ->with('success', 'Email şablonu başarıyla kopyalandı! 📋');
    }

    /**
     * Hedef kitle önizleme (AJAX)
     */
    public function previewAudience(Request $request)
    {
        $targetType = $request->input('target_type');
        $targetCriteria = $request->input('target_criteria', []);
        $userIds = $request->input('user_ids', []);

        $query = User::query()->where('status', 'active');

        if ($targetType === 'all') {
            // Tüm kullanıcılar
            $count = $query->count();
        } elseif ($targetType === 'segment') {
            // Segment kriterleri uygula
            foreach ($targetCriteria as $key => $value) {
                switch ($key) {
                    case 'user_type':
                        if ($value === 'new') {
                            $query->where('created_at', '>=', now()->subDays(7));
                        } elseif ($value === 'active') {
                            $query->where('last_login_at', '>=', now()->subDays(30));
                        } elseif ($value === 'inactive') {
                            $query->where('last_login_at', '<', now()->subDays(30));
                        }
                        break;
                    case 'email_verified':
                        if ($value) {
                            $query->whereNotNull('email_verified_at');
                        } else {
                            $query->whereNull('email_verified_at');
                        }
                        break;
                }
            }
            $count = $query->count();
        } elseif ($targetType === 'custom') {
            // Özel kullanıcı listesi
            $count = count($userIds);
        } else {
            $count = 0;
        }

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "{$count} kullanıcıya gönderilecek",
        ]);
    }

    /**
     * Test email gönderme
     */
    public function sendTestEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            Mail::raw($request->body, function ($message) use ($request) {
                $message->to($request->email)
                    ->subject($request->subject);
            });

            return response()->json([
                'success' => true,
                'message' => "Test emaili {$request->email} adresine gönderildi! 📧",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email gönderilemedi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
