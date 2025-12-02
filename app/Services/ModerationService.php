<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ClanApplication;
use App\Models\LfgApplication;
use App\Models\User;
use App\Models\LfgPost;
use App\Models\Clan;
use App\Models\GuidePost;
use App\Models\CommunityPost;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

/**
 * Moderation Service
 * 
 * İçerik moderasyonu, rapor işleme ve otomatik moderasyon için servis.
 */
class ModerationService
{
    /**
     * Küfür/hakaret kelime listesi
     */
    private array $badWords = [
        'küfür1', 'küfür2', 'hakaret1', 'hakaret2',
        // Gerçek implementasyonda daha kapsamlı liste olmalı
    ];

    /**
     * Spam tespiti için minimum süre (saniye)
     */
    private int $spamThreshold = 60;

    /**
     * Moderasyon kuyruğunu getir
     * 
     * @return array
     */
    public function getQueue(): array
    {
        return [
            'pending_reports' => $this->getPendingReports(),
            'pending_content' => $this->getPendingContent(),
            'pending_applications' => $this->getPendingApplications(),
            'suspicious_activities' => $this->getSuspiciousActivities(),
            'summary' => $this->getQueueSummary(),
        ];
    }

    /**
     * Bekleyen raporları getir
     */
    private function getPendingReports(): Collection
    {
        return Report::with(['reporter:id,name,email', 'reportable'])
            ->where('status', 'pending')
            ->orderByDesc('priority')
            ->orderBy('created_at')
            ->limit(50)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'type' => $report->reportable_type,
                    'reason' => $report->reason,
                    'description' => $report->description,
                    'reporter' => $report->reporter ? $report->reporter->name : 'Anonim',
                    'priority' => $report->priority,
                    'created_at' => $report->created_at->diffForHumans(),
                    'url' => $this->getReportUrl($report),
                ];
            });
    }

    /**
     * Bekleyen içerikleri getir (moderasyon açıksa)
     */
    private function getPendingContent(): array
    {
        // Şu an için moderasyon sistemi yok, gelecekte eklenebilir
        return [
            'guides' => GuidePost::where('is_published', false)->count(),
            'community_posts' => 0, // Moderasyon sistemi eklendiğinde
        ];
    }

    /**
     * Bekleyen başvuruları getir
     */
    private function getPendingApplications(): array
    {
        return [
            'clan_applications' => ClanApplication::with(['user:id,name', 'clan:id,name'])
                ->where('status', 'pending')
                ->orderBy('created_at')
                ->limit(20)
                ->get()
                ->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'type' => 'clan',
                        'user' => $app->user->name,
                        'clan' => $app->clan->name,
                        'message' => $app->message,
                        'created_at' => $app->created_at->diffForHumans(),
                    ];
                }),
            'lfg_applications' => LfgApplication::with(['user:id,name', 'lfgPost:id,title'])
                ->where('status', 'pending')
                ->orderBy('created_at')
                ->limit(20)
                ->get()
                ->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'type' => 'lfg',
                        'user' => $app->user->name,
                        'lfg_post' => $app->lfgPost->title,
                        'message' => $app->message,
                        'created_at' => $app->created_at->diffForHumans(),
                    ];
                }),
        ];
    }

    /**
     * Şüpheli aktiviteleri getir
     */
    private function getSuspiciousActivities(): Collection
    {
        $suspicious = collect();
        
        // Spam kullanıcılar (son 1 saatte 10+ içerik)
        $spammers = $this->detectSpammers();
        $suspicious = $suspicious->merge($spammers);
        
        // Çok raporlanan kullanıcılar
        $reportedUsers = $this->getHighlyReportedUsers();
        $suspicious = $suspicious->merge($reportedUsers);
        
        // Şüpheli içerikler (küfür içeren)
        $badContent = $this->detectBadContent();
        $suspicious = $suspicious->merge($badContent);
        
        return $suspicious->take(20);
    }

    /**
     * Kuyruk özetini getir
     */
    private function getQueueSummary(): array
    {
        return [
            'total_pending_reports' => Report::where('status', 'pending')->count(),
            'high_priority_reports' => Report::where('status', 'pending')
                ->where('priority', 'high')
                ->count(),
            'total_pending_applications' => ClanApplication::where('status', 'pending')->count() +
                                           LfgApplication::where('status', 'pending')->count(),
            'suspicious_users' => $this->detectSpammers()->count() + 
                                 $this->getHighlyReportedUsers()->count(),
        ];
    }

    /**
     * Raporu işle
     * 
     * @param int $reportId Rapor ID
     * @param string $action İşlem (approve, reject)
     * @param string|null $reason Sebep
     * @return array
     */
    public function processReport(int $reportId, string $action, ?string $reason = null): array
    {
        $report = Report::with('reportable')->findOrFail($reportId);
        
        DB::beginTransaction();
        
        try {
            if ($action === 'approve') {
                // Raporu onayla ve içeriği işle
                $this->approveReport($report, $reason);
                $message = 'Rapor onaylandı ve gerekli işlemler yapıldı.';
            } else {
                // Raporu reddet
                $this->rejectReport($report, $reason);
                $message = 'Rapor reddedildi.';
            }
            
            DB::commit();
            
            // Cache'i temizle
            Cache::forget('moderation.queue');
            
            return [
                'success' => true,
                'message' => $message,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Rapor işlenirken hata oluştu: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Raporu onayla
     */
    private function approveReport(Report $report, ?string $reason): void
    {
        $report->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
            'resolution_note' => $reason,
        ]);
        
        // Raporlanan içeriğe göre işlem yap
        $reportable = $report->reportable;
        
        if (!$reportable) {
            return;
        }
        
        switch ($report->reportable_type) {
            case 'App\Models\User':
                $this->handleUserReport($reportable, $report);
                break;
            case 'App\Models\LfgPost':
            case 'App\Models\Clan':
            case 'App\Models\GuidePost':
            case 'App\Models\CommunityPost':
                $this->handleContentReport($reportable, $report);
                break;
            case 'App\Models\Comment':
                $this->handleCommentReport($reportable, $report);
                break;
        }
    }

    /**
     * Kullanıcı raporunu işle
     */
    private function handleUserReport(User $user, Report $report): void
    {
        // Kullanıcının aldığı rapor sayısını kontrol et
        $reportCount = Report::where('reportable_type', 'App\Models\User')
            ->where('reportable_id', $user->id)
            ->where('status', 'resolved')
            ->count();
        
        // 3 onaylı rapor sonrası otomatik ban
        if ($reportCount >= 3) {
            $user->update([
                'status' => 'banned',
                'ban_reason' => 'Çoklu rapor nedeniyle otomatik ban',
                'banned_at' => now(),
                'banned_by' => auth()->id(),
            ]);
        }
    }

    /**
     * İçerik raporunu işle
     */
    private function handleContentReport($content, Report $report): void
    {
        // İçeriği gizle veya sil
        if (method_exists($content, 'delete')) {
            $content->delete();
        }
    }

    /**
     * Yorum raporunu işle
     */
    private function handleCommentReport(Comment $comment, Report $report): void
    {
        $comment->delete();
    }

    /**
     * Raporu reddet
     */
    private function rejectReport(Report $report, ?string $reason): void
    {
        $report->update([
            'status' => 'rejected',
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
            'resolution_note' => $reason,
        ]);
    }

    /**
     * Otomatik moderasyon
     * 
     * @param mixed $content İçerik (string veya model)
     * @return array
     */
    public function autoModerate($content): array
    {
        $issues = [];
        
        // String içerik kontrolü
        if (is_string($content)) {
            $text = $content;
        } else {
            // Model ise içeriği çıkar
            $text = $this->extractTextFromModel($content);
        }
        
        // Küfür kontrolü
        if ($this->containsBadWords($text)) {
            $issues[] = [
                'type' => 'profanity',
                'severity' => 'high',
                'message' => 'İçerik uygunsuz kelimeler içeriyor',
            ];
        }
        
        // Link spam kontrolü
        if ($this->isLinkSpam($text)) {
            $issues[] = [
                'type' => 'link_spam',
                'severity' => 'medium',
                'message' => 'İçerik çok fazla link içeriyor',
            ];
        }
        
        // Tekrarlayan içerik kontrolü
        if (!is_string($content) && $this->isDuplicateContent($content)) {
            $issues[] = [
                'type' => 'duplicate',
                'severity' => 'low',
                'message' => 'Benzer içerik daha önce paylaşılmış',
            ];
        }
        
        return [
            'passed' => empty($issues),
            'issues' => $issues,
            'action' => $this->determineAction($issues),
        ];
    }

    /**
     * Model'den metin çıkar
     */
    private function extractTextFromModel($model): string
    {
        $text = '';
        
        if (isset($model->title)) $text .= $model->title . ' ';
        if (isset($model->description)) $text .= $model->description . ' ';
        if (isset($model->content)) $text .= $model->content . ' ';
        if (isset($model->message)) $text .= $model->message . ' ';
        
        return $text;
    }

    /**
     * Küfür içeriyor mu kontrol et
     */
    private function containsBadWords(string $text): bool
    {
        $text = mb_strtolower($text);
        
        foreach ($this->badWords as $word) {
            if (str_contains($text, $word)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Link spam kontrolü
     */
    private function isLinkSpam(string $text): bool
    {
        // 3'ten fazla link varsa spam olarak işaretle
        $linkCount = preg_match_all('/https?:\/\//', $text);
        return $linkCount > 3;
    }

    /**
     * Tekrarlayan içerik kontrolü
     */
    private function isDuplicateContent($model): bool
    {
        // Basit kontrol - aynı kullanıcının son 1 saatte aynı başlıkla içerik paylaşması
        if (!isset($model->user_id) || !isset($model->title)) {
            return false;
        }
        
        $modelClass = get_class($model);
        
        $duplicate = $modelClass::where('user_id', $model->user_id)
            ->where('title', $model->title)
            ->where('created_at', '>=', now()->subHour())
            ->where('id', '!=', $model->id ?? 0)
            ->exists();
        
        return $duplicate;
    }

    /**
     * İşlem belirle
     */
    private function determineAction(array $issues): string
    {
        if (empty($issues)) {
            return 'approve';
        }
        
        $highSeverity = collect($issues)->where('severity', 'high')->count();
        
        if ($highSeverity > 0) {
            return 'reject';
        }
        
        return 'review';
    }

    /**
     * Spam kullanıcıları tespit et
     */
    private function detectSpammers(): Collection
    {
        // Son 1 saatte 10+ içerik paylaşan kullanıcılar
        // Önce spam yapan user ID'lerini bul
        $spammerIds = DB::table('users')
            ->leftJoin('lfg_posts', function($join) {
                $join->on('users.id', '=', 'lfg_posts.user_id')
                     ->where('lfg_posts.created_at', '>=', now()->subHour());
            })
            ->leftJoin('community_posts', function($join) {
                $join->on('users.id', '=', 'community_posts.user_id')
                     ->where('community_posts.created_at', '>=', now()->subHour());
            })
            ->whereNull('users.deleted_at')
            ->groupBy('users.id')
            ->havingRaw('COUNT(CASE WHEN lfg_posts.id IS NOT NULL OR community_posts.id IS NOT NULL THEN 1 END) >= 10')
            ->pluck('users.id');
        
        // Bulunan ID'lere göre kullanıcıları getir
        if ($spammerIds->isEmpty()) {
            return collect();
        }
        
        $spammers = User::whereIn('id', $spammerIds)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'spammer',
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'description' => 'Son 1 saatte çok fazla içerik paylaştı',
                    'severity' => 'high',
                ];
            });
        
        return $spammers;
    }

    /**
     * Çok raporlanan kullanıcıları getir
     */
    private function getHighlyReportedUsers(): Collection
    {
        $reportedUsers = Report::select('reportable_id', DB::raw('COUNT(*) as report_count'))
            ->where('reportable_type', 'App\Models\User')
            ->where('status', 'pending')
            ->groupBy('reportable_id')
            ->having('report_count', '>=', 3)
            ->with('reportable:id,name')
            ->get()
            ->map(function ($report) {
                return [
                    'type' => 'highly_reported',
                    'user_id' => $report->reportable_id,
                    'user_name' => $report->reportable ? $report->reportable->name : 'Bilinmeyen',
                    'description' => $report->report_count . ' bekleyen rapor var',
                    'severity' => 'high',
                ];
            });
        
        return $reportedUsers;
    }

    /**
     * Kötü içerik tespit et
     */
    private function detectBadContent(): Collection
    {
        $badContent = collect();
        
        // Son yorumları kontrol et
        $comments = Comment::where('created_at', '>=', now()->subDay())
            ->limit(100)
            ->get();
        
        foreach ($comments as $comment) {
            if ($this->containsBadWords($comment->content)) {
                $badContent->push([
                    'type' => 'bad_content',
                    'content_type' => 'comment',
                    'content_id' => $comment->id,
                    'description' => 'Yorum uygunsuz kelimeler içeriyor',
                    'severity' => 'medium',
                ]);
            }
        }
        
        return $badContent;
    }

    /**
     * Rapor URL'ini oluştur
     */
    private function getReportUrl(Report $report): string
    {
        return route('admin.reports.show', $report->id);
    }
}
