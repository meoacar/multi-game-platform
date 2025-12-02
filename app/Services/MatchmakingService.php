<?php

namespace App\Services;

use App\Models\MatchmakingQueue;
use App\Models\MatchmakingMatch;
use App\Models\MatchmakingHistory;
use App\Models\User;
use App\Notifications\MatchFoundNotification;
use App\Notifications\MatchAcceptedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

/**
 * MatchmakingService
 * 
 * Matchmaking sisteminin ana iş mantığını yönetir
 * 
 * Sorumluluklar:
 * - Kuyruğa ekleme/çıkarma
 * - Eşleşme arama ve oluşturma
 * - Eşleşme kabul/red işlemleri
 * - Süresi dolmuş kayıtları temizleme
 */
class MatchmakingService
{
    protected MatchmakingAlgorithm $algorithm;
    protected XpService $xpService;

    public function __construct(MatchmakingAlgorithm $algorithm, XpService $xpService)
    {
        $this->algorithm = $algorithm;
        $this->xpService = $xpService;
    }

    /**
     * Kullanıcıyı kuyruğa ekle
     * 
     * @param User $user
     * @param array $preferences [game_id, mode, min_rank, max_rank, city, microphone_required, play_style]
     * @return MatchmakingQueue
     * @throws \Exception
     */
    public function joinQueue(User $user, array $preferences): MatchmakingQueue
    {
        // Kullanıcı zaten kuyrukta mı kontrol et
        $existingQueue = MatchmakingQueue::where('user_id', $user->id)
            ->active()
            ->first();

        if ($existingQueue) {
            throw new \Exception('Zaten kuyrukta bekliyorsunuz');
        }

        // Profil kontrolü
        if (!$user->profile) {
            throw new \Exception('Lütfen profilinizi tamamlayın');
        }

        // Kuyruğa ekle
        $queue = MatchmakingQueue::create([
            'user_id' => $user->id,
            'game_id' => $preferences['game_id'],
            'mode' => $preferences['mode'],
            'min_rank' => $preferences['min_rank'] ?? null,
            'max_rank' => $preferences['max_rank'] ?? null,
            'city' => $preferences['city'] ?? null,
            'microphone_required' => $preferences['microphone_required'] ?? false,
            'play_style' => $preferences['play_style'] ?? null,
            'status' => 'searching',
            'expires_at' => now()->addMinutes(5), // 5 dakika timeout
            'search_attempts' => 0,
        ]);

        Log::info('User joined matchmaking queue', [
            'user_id' => $user->id,
            'queue_id' => $queue->id,
            'mode' => $queue->mode,
        ]);

        return $queue;
    }

    /**
     * Kullanıcıyı kuyruktan çıkar
     * 
     * @param User $user
     * @return bool
     */
    public function leaveQueue(User $user): bool
    {
        $queue = MatchmakingQueue::where('user_id', $user->id)
            ->active()
            ->first();

        if (!$queue) {
            return false;
        }

        $queue->updateStatus('cancelled');

        Log::info('User left matchmaking queue', [
            'user_id' => $user->id,
            'queue_id' => $queue->id,
        ]);

        return true;
    }


    /**
     * Aktif kuyruklar arasında eşleşme ara
     * Scheduled job tarafından çağrılır
     * 
     * @return int Oluşturulan eşleşme sayısı
     */
    public function findMatches(): int
    {
        $matchesCreated = 0;

        // Her oyun ve mod kombinasyonu için ayrı ayrı eşleşme ara
        $activeQueues = MatchmakingQueue::active()->get();

        if ($activeQueues->isEmpty()) {
            return 0;
        }

        // Oyun ve mod'a göre grupla
        $groupedQueues = $activeQueues->groupBy(function ($queue) {
            return $queue->game_id . '_' . $queue->mode;
        });

        foreach ($groupedQueues as $key => $queues) {
            if ($queues->isEmpty()) {
                continue;
            }

            $mode = $queues->first()->mode;
            
            // En iyi grubu bul
            $bestGroup = $this->algorithm->findBestGroup($queues, $mode);

            if ($bestGroup) {
                // Eşleşme oluştur
                $match = $this->createMatchFromGroup($bestGroup, $queues->first()->game_id, $mode);
                
                if ($match) {
                    $matchesCreated++;
                    
                    // Kuyrukları güncelle
                    MatchmakingQueue::whereIn('id', $bestGroup['queue_ids'])
                        ->update(['status' => 'matched']);
                }
            }

            // Deneme sayılarını artır
            $queues->each(function ($queue) {
                $queue->incrementSearchAttempts();
            });
        }

        Log::info('Matchmaking search completed', [
            'matches_created' => $matchesCreated,
            'active_queues' => $activeQueues->count(),
        ]);

        return $matchesCreated;
    }

    /**
     * İki oyuncu arasındaki uyumluluğu hesapla
     * 
     * @param MatchmakingQueue $queue1
     * @param MatchmakingQueue $queue2
     * @return int Uyumluluk skoru (0-100)
     */
    public function calculateCompatibility(MatchmakingQueue $queue1, MatchmakingQueue $queue2): int
    {
        return $this->algorithm->calculateTotalScore($queue1, $queue2);
    }

    /**
     * Eşleşme oluştur
     * 
     * @param array $groupData [queue_ids, avg_score]
     * @param int $gameId
     * @param string $mode
     * @return MatchmakingMatch|null
     */
    protected function createMatchFromGroup(array $groupData, int $gameId, string $mode): ?MatchmakingMatch
    {
        try {
            DB::beginTransaction();

            // Kuyrukları getir
            $queues = MatchmakingQueue::whereIn('id', $groupData['queue_ids'])->get();
            
            if ($queues->isEmpty()) {
                DB::rollBack();
                return null;
            }

            $userIds = $queues->pluck('user_id')->toArray();

            // Eşleşme oluştur
            $match = MatchmakingMatch::create([
                'game_id' => $gameId,
                'mode' => $mode,
                'user_ids' => $userIds,
                'compatibility_score' => (int) $groupData['avg_score'],
                'match_criteria' => [
                    'queue_ids' => $groupData['queue_ids'],
                    'created_at' => now()->toDateTimeString(),
                ],
                'status' => 'pending',
                'acceptance_status' => [],
                'expires_at' => now()->addSeconds(30), // 30 saniye kabul süresi
            ]);

            DB::commit();

            Log::info('Match created', [
                'match_id' => $match->id,
                'user_ids' => $userIds,
                'compatibility_score' => $match->compatibility_score,
            ]);

            // Tüm kullanıcılara bildirim gönder
            $this->notifyMatchFound($match);

            return $match;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create match', [
                'error' => $e->getMessage(),
                'group_data' => $groupData,
            ]);
            return null;
        }
    }

    /**
     * Eşleşme oluştur (public method)
     * 
     * @param array $userIds
     * @param int $gameId
     * @param string $mode
     * @param int $compatibilityScore
     * @return MatchmakingMatch
     */
    public function createMatch(array $userIds, int $gameId, string $mode, int $compatibilityScore): MatchmakingMatch
    {
        return MatchmakingMatch::create([
            'game_id' => $gameId,
            'mode' => $mode,
            'user_ids' => $userIds,
            'compatibility_score' => $compatibilityScore,
            'match_criteria' => [
                'created_at' => now()->toDateTimeString(),
            ],
            'status' => 'pending',
            'acceptance_status' => [],
            'expires_at' => now()->addSeconds(30),
        ]);
    }


    /**
     * Eşleşmeyi kabul et
     * 
     * @param MatchmakingMatch $match
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function acceptMatch(MatchmakingMatch $match, User $user): bool
    {
        // Kullanıcı bu eşleşmede mi kontrol et
        if (!in_array($user->id, $match->user_ids ?? [])) {
            throw new \Exception('Bu eşleşmede yer almıyorsunuz');
        }

        // Eşleşme aktif mi kontrol et
        if (!$match->isActive()) {
            throw new \Exception('Bu eşleşme artık aktif değil');
        }

        // Kullanıcı zaten kabul etmiş mi kontrol et
        if ($match->getUserAcceptanceStatus($user->id) === 'accepted') {
            return true;
        }

        // Kabul et
        $match->acceptByUser($user->id);

        Log::info('User accepted match', [
            'user_id' => $user->id,
            'match_id' => $match->id,
        ]);

        // Tüm kullanıcılar kabul etti mi kontrol et
        $match->refresh();
        if ($match->allUsersAccepted()) {
            $match->update(['status' => 'accepted']);
            
            // Tüm kullanıcılara kabul bildirimi gönder
            $this->notifyMatchAccepted($match);
            
            $this->completeMatch($match);
        }

        return true;
    }

    /**
     * Eşleşmeyi reddet
     * 
     * @param MatchmakingMatch $match
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function rejectMatch(MatchmakingMatch $match, User $user): bool
    {
        // Kullanıcı bu eşleşmede mi kontrol et
        if (!in_array($user->id, $match->user_ids ?? [])) {
            throw new \Exception('Bu eşleşmede yer almıyorsunuz');
        }

        // Eşleşme aktif mi kontrol et
        if (!$match->isActive()) {
            throw new \Exception('Bu eşleşme artık aktif değil');
        }

        // Reddet
        $match->rejectByUser($user->id);

        Log::info('User rejected match', [
            'user_id' => $user->id,
            'match_id' => $match->id,
        ]);

        // Eşleşmeyi iptal et ve geçmişe kaydet
        $this->cancelMatch($match, 'rejected');

        return true;
    }

    /**
     * Eşleşmeyi tamamla
     * 
     * @param MatchmakingMatch $match
     * @return void
     */
    protected function completeMatch(MatchmakingMatch $match): void
    {
        try {
            DB::beginTransaction();

            // Her kullanıcı için geçmiş kaydı oluştur ve XP ver
            foreach ($match->user_ids as $userId) {
                $user = User::find($userId);
                
                if (!$user) {
                    continue;
                }

                $queue = MatchmakingQueue::where('user_id', $userId)
                    ->where('status', 'matched')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $waitTime = $queue ? now()->diffInSeconds($queue->created_at) : 0;

                MatchmakingHistory::create([
                    'user_id' => $userId,
                    'match_id' => $match->id,
                    'result' => 'completed',
                    'wait_time_seconds' => $waitTime,
                    'compatibility_score' => $match->compatibility_score,
                    'matched_users' => $match->user_ids,
                ]);

                // XP ver
                $this->awardMatchmakingXp($user, $match);
            }

            // Kuyrukları temizle
            MatchmakingQueue::whereIn('user_id', $match->user_ids)
                ->where('status', 'matched')
                ->update(['status' => 'completed']);

            DB::commit();

            Log::info('Match completed', [
                'match_id' => $match->id,
                'user_ids' => $match->user_ids,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete match', [
                'match_id' => $match->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Eşleşmeyi iptal et
     * 
     * @param MatchmakingMatch $match
     * @param string $reason
     * @return void
     */
    protected function cancelMatch(MatchmakingMatch $match, string $reason): void
    {
        try {
            DB::beginTransaction();

            // Eşleşme durumunu güncelle
            $match->update(['status' => $reason]);

            // Her kullanıcı için geçmiş kaydı oluştur
            foreach ($match->user_ids as $userId) {
                $queue = MatchmakingQueue::where('user_id', $userId)
                    ->where('status', 'matched')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $waitTime = $queue ? now()->diffInSeconds($queue->created_at) : 0;

                MatchmakingHistory::create([
                    'user_id' => $userId,
                    'match_id' => $match->id,
                    'result' => $reason,
                    'wait_time_seconds' => $waitTime,
                    'compatibility_score' => $match->compatibility_score,
                    'matched_users' => $match->user_ids,
                ]);
            }

            // Kuyrukları geri searching durumuna al
            MatchmakingQueue::whereIn('user_id', $match->user_ids)
                ->where('status', 'matched')
                ->update([
                    'status' => 'searching',
                    'expires_at' => now()->addMinutes(5),
                ]);

            DB::commit();

            Log::info('Match cancelled', [
                'match_id' => $match->id,
                'reason' => $reason,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel match', [
                'match_id' => $match->id,
                'error' => $e->getMessage(),
            ]);
            throw $e; // Exception'ı yeniden fırlat
        }
    }


    /**
     * Süresi dolmuş kuyrukları temizle
     * 
     * @return int Temizlenen kuyruk sayısı
     */
    public function cleanupExpiredQueues(): int
    {
        $expiredQueues = MatchmakingQueue::where('status', 'searching')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredQueues->isEmpty()) {
            return 0;
        }

        $count = 0;

        foreach ($expiredQueues as $queue) {
            try {
                DB::beginTransaction();

                // Kuyruk durumunu güncelle
                $queue->updateStatus('expired');

                // Geçmiş kaydı oluştur
                MatchmakingHistory::create([
                    'user_id' => $queue->user_id,
                    'match_id' => null,
                    'result' => 'timeout',
                    'wait_time_seconds' => now()->diffInSeconds($queue->created_at),
                    'compatibility_score' => 0,
                    'matched_users' => [],
                ]);

                DB::commit();
                $count++;

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to cleanup expired queue', [
                    'queue_id' => $queue->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Expired queues cleaned up', [
            'count' => $count,
        ]);

        return $count;
    }

    /**
     * Süresi dolmuş eşleşmeleri temizle
     * 
     * @return int Temizlenen eşleşme sayısı
     */
    public function cleanupExpiredMatches(): int
    {
        $expiredMatches = MatchmakingMatch::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredMatches->isEmpty()) {
            return 0;
        }

        $count = 0;

        foreach ($expiredMatches as $match) {
            try {
                $this->cancelMatch($match, 'timeout');
                $count++;

            } catch (\Exception $e) {
                Log::error('Failed to cleanup expired match', [
                    'match_id' => $match->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Expired matches cleaned up', [
            'count' => $count,
        ]);

        return $count;
    }

    /**
     * Kullanıcının aktif kuyruğunu getir
     * 
     * @param User $user
     * @return MatchmakingQueue|null
     */
    public function getUserActiveQueue(User $user): ?MatchmakingQueue
    {
        return MatchmakingQueue::where('user_id', $user->id)
            ->active()
            ->first();
    }

    /**
     * Kullanıcının bekleyen eşleşmelerini getir
     * 
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserPendingMatches(User $user)
    {
        return MatchmakingMatch::forUser($user->id)
            ->pending()
            ->get();
    }

    /**
     * Kullanıcının eşleşme geçmişini getir
     * 
     * @param User $user
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserHistory(User $user, int $limit = 20)
    {
        return MatchmakingHistory::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Kullanıcının başarı oranını hesapla
     * 
     * @param User $user
     * @return float
     */
    public function getUserSuccessRate(User $user): float
    {
        $total = MatchmakingHistory::forUser($user->id)->count();
        
        if ($total === 0) {
            return 0.0;
        }

        $successful = MatchmakingHistory::forUser($user->id)
            ->successful()
            ->count();

        return round(($successful / $total) * 100, 2);
    }

    /**
     * Kullanıcının ortalama bekleme süresini hesapla (saniye)
     * 
     * @param User $user
     * @return float
     */
    public function getUserAverageWaitTime(User $user): float
    {
        $avgWaitTime = MatchmakingHistory::forUser($user->id)
            ->successful()
            ->avg('wait_time_seconds');

        return round($avgWaitTime ?? 0, 2);
    }

    /**
     * Matchmaking başarısı için XP ver
     * 
     * @param User $user
     * @param MatchmakingMatch $match
     * @return void
     */
    protected function awardMatchmakingXp(User $user, MatchmakingMatch $match): void
    {
        try {
            // İlk eşleşme mi kontrol et
            $previousMatches = MatchmakingHistory::forUser($user->id)
                ->successful()
                ->count();

            if ($previousMatches === 0) {
                // İlk eşleşme - 50 XP
                $this->xpService->addXp($user, 'matchmaking_first_match', [
                    'match_id' => $match->id,
                    'mode' => $match->mode,
                    'compatibility_score' => $match->compatibility_score,
                ]);

                Log::info('First matchmaking XP awarded', [
                    'user_id' => $user->id,
                    'match_id' => $match->id,
                    'xp' => 50,
                ]);
            } else {
                // Normal eşleşme - 20 XP
                $this->xpService->addXp($user, 'matchmaking_success', [
                    'match_id' => $match->id,
                    'mode' => $match->mode,
                    'compatibility_score' => $match->compatibility_score,
                ]);

                Log::info('Matchmaking success XP awarded', [
                    'user_id' => $user->id,
                    'match_id' => $match->id,
                    'xp' => 20,
                ]);
            }

        } catch (\Exception $e) {
            // XP verme hatası eşleşmeyi etkilememeli
            Log::error('Failed to award matchmaking XP', [
                'user_id' => $user->id,
                'match_id' => $match->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Eşleşme bulundu bildirimi gönder
     * 
     * @param MatchmakingMatch $match
     * @return void
     */
    protected function notifyMatchFound(MatchmakingMatch $match): void
    {
        try {
            $users = User::whereIn('id', $match->user_ids)->get();
            
            Notification::send($users, new MatchFoundNotification($match));

            Log::info('Match found notifications sent', [
                'match_id' => $match->id,
                'user_count' => $users->count(),
            ]);

        } catch (\Exception $e) {
            // Bildirim hatası eşleşmeyi etkilememeli
            Log::error('Failed to send match found notifications', [
                'match_id' => $match->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Eşleşme kabul edildi bildirimi gönder
     * 
     * @param MatchmakingMatch $match
     * @return void
     */
    protected function notifyMatchAccepted(MatchmakingMatch $match): void
    {
        try {
            $users = User::whereIn('id', $match->user_ids)->get();
            
            Notification::send($users, new MatchAcceptedNotification($match));

            Log::info('Match accepted notifications sent', [
                'match_id' => $match->id,
                'user_count' => $users->count(),
            ]);

        } catch (\Exception $e) {
            // Bildirim hatası eşleşmeyi etkilememeli
            Log::error('Failed to send match accepted notifications', [
                'match_id' => $match->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
