<?php

namespace App\Services;

use App\Models\MatchmakingQueue;
use Illuminate\Support\Collection;

/**
 * MatchmakingAlgorithm
 * 
 * Oyuncular arasındaki uyumluluk skorunu hesaplar ve en iyi grupları bulur
 * 
 * Skor Dağılımı:
 * - Rank Uyumluluğu: 0-30 puan
 * - Oyun Stili Uyumluluğu: 0-25 puan
 * - Lokasyon Uyumluluğu: 0-20 puan
 * - Mikrofon Uyumluluğu: 0-15 puan
 * - Oyun Deneyimi: 0-10 puan
 * Toplam: 0-100 puan
 */
class MatchmakingAlgorithm
{
    /**
     * Minimum uyumluluk skoru eşiği
     */
    const MIN_COMPATIBILITY_SCORE = 60;

    /**
     * Rank uyumluluğunu hesapla (0-30 puan)
     * 
     * Rank farkı ne kadar az ise skor o kadar yüksek
     * - Aynı rank: 30 puan
     * - 1 fark: 25 puan
     * - 2 fark: 20 puan
     * - 3+ fark: 0 puan
     */
    private function calculateRankCompatibility(?string $rank1, ?string $rank2): int
    {
        if (!$rank1 || !$rank2) {
            return 15; // Rank belirtilmemişse orta skor
        }

        $rankOrder = [
            'Bronze' => 1,
            'Silver' => 2,
            'Gold' => 3,
            'Platinum' => 4,
            'Diamond' => 5,
            'Crown' => 6,
            'Ace' => 7,
            'Conqueror' => 8,
        ];

        $rank1Value = $rankOrder[$rank1] ?? 0;
        $rank2Value = $rankOrder[$rank2] ?? 0;

        $difference = abs($rank1Value - $rank2Value);

        return match (true) {
            $difference === 0 => 30,
            $difference === 1 => 25,
            $difference === 2 => 20,
            default => 0,
        };
    }

    /**
     * Oyun stili uyumluluğunu hesapla (0-25 puan)
     * 
     * - Aynı stil: 25 puan
     * - Farklı stil: 10 puan
     * - Stil belirtilmemiş: 15 puan
     */
    private function calculatePlayStyleCompatibility(?string $style1, ?string $style2): int
    {
        if (!$style1 || !$style2) {
            return 15; // Stil belirtilmemişse orta skor
        }

        return $style1 === $style2 ? 25 : 10;
    }

    /**
     * Lokasyon uyumluluğunu hesapla (0-20 puan)
     * 
     * - Aynı şehir: 20 puan
     * - Farklı şehir: 5 puan
     * - Şehir belirtilmemiş: 10 puan
     */
    private function calculateLocationCompatibility(?string $city1, ?string $city2): int
    {
        if (!$city1 || !$city2) {
            return 10; // Şehir belirtilmemişse orta skor
        }

        return $city1 === $city2 ? 20 : 5;
    }

    /**
     * Mikrofon uyumluluğunu hesapla (0-15 puan)
     * 
     * - Her ikisi de mikrofon istiyor: 15 puan
     * - Her ikisi de mikrofon istemiyor: 15 puan
     * - Biri istiyor biri istemiyor: 0 puan
     */
    private function calculateMicrophoneCompatibility(bool $mic1, bool $mic2): int
    {
        return $mic1 === $mic2 ? 15 : 0;
    }

    /**
     * Oyun deneyimi uyumluluğunu hesapla (0-10 puan)
     * 
     * Arama deneme sayısına göre bonus puan
     * Daha çok bekleyen oyunculara öncelik ver
     */
    private function calculateExperienceCompatibility(int $attempts1, int $attempts2): int
    {
        $avgAttempts = ($attempts1 + $attempts2) / 2;
        
        return match (true) {
            $avgAttempts >= 5 => 10, // Çok beklemişler, yüksek öncelik
            $avgAttempts >= 3 => 7,
            $avgAttempts >= 1 => 5,
            default => 3,
        };
    }

    /**
     * İki oyuncu arasındaki toplam uyumluluk skorunu hesapla (0-100)
     */
    public function calculateTotalScore(MatchmakingQueue $q1, MatchmakingQueue $q2): int
    {
        // Farklı oyun veya mod ise uyumsuz
        if ($q1->game_id !== $q2->game_id || $q1->mode !== $q2->mode) {
            return 0;
        }

        // Rank aralığı kontrolü
        if (!$this->isRankInRange($q1, $q2)) {
            return 0;
        }

        $rankScore = $this->calculateRankCompatibility($q1->min_rank, $q2->min_rank);
        $styleScore = $this->calculatePlayStyleCompatibility($q1->play_style, $q2->play_style);
        $locationScore = $this->calculateLocationCompatibility($q1->city, $q2->city);
        $micScore = $this->calculateMicrophoneCompatibility(
            $q1->microphone_required,
            $q2->microphone_required
        );
        $experienceScore = $this->calculateExperienceCompatibility(
            $q1->search_attempts,
            $q2->search_attempts
        );

        return $rankScore + $styleScore + $locationScore + $micScore + $experienceScore;
    }

    /**
     * Rank'in belirlenen aralıkta olup olmadığını kontrol et
     */
    private function isRankInRange(MatchmakingQueue $q1, MatchmakingQueue $q2): bool
    {
        $rankOrder = [
            'Bronze' => 1,
            'Silver' => 2,
            'Gold' => 3,
            'Platinum' => 4,
            'Diamond' => 5,
            'Crown' => 6,
            'Ace' => 7,
            'Conqueror' => 8,
        ];

        $q1MinRank = $rankOrder[$q1->min_rank] ?? 1;
        $q1MaxRank = $rankOrder[$q1->max_rank] ?? 8;
        $q2MinRank = $rankOrder[$q2->min_rank] ?? 1;
        $q2MaxRank = $rankOrder[$q2->max_rank] ?? 8;

        // q1'in rank aralığı q2'nin rank'ını kapsıyor mu?
        $q1CoversQ2 = $q2MinRank >= $q1MinRank && $q2MinRank <= $q1MaxRank;
        
        // q2'nin rank aralığı q1'in rank'ını kapsıyor mu?
        $q2CoversQ1 = $q1MinRank >= $q2MinRank && $q1MinRank <= $q2MaxRank;

        return $q1CoversQ2 || $q2CoversQ1;
    }

    /**
     * Verilen kuyruklar arasından en iyi grubu bul
     * 
     * @param Collection $queues Aktif kuyruklar
     * @param string $mode Oyun modu (Squad, Duo, Solo)
     * @return array|null [queue_ids, avg_score] veya null
     */
    public function findBestGroup(Collection $queues, string $mode): ?array
    {
        $groupSize = $this->getGroupSize($mode);
        
        if ($queues->count() < $groupSize) {
            return null;
        }

        $bestGroup = null;
        $bestScore = self::MIN_COMPATIBILITY_SCORE;

        // Tüm olası kombinasyonları dene
        $combinations = $this->getCombinations($queues->all(), $groupSize);

        foreach ($combinations as $combination) {
            $avgScore = $this->calculateGroupCompatibility($combination);
            
            if ($avgScore >= self::MIN_COMPATIBILITY_SCORE && $avgScore > $bestScore) {
                $bestScore = $avgScore;
                $bestGroup = [
                    'queue_ids' => array_map(fn($q) => $q->id, $combination),
                    'avg_score' => $avgScore,
                ];
            }
        }

        return $bestGroup;
    }

    /**
     * Mod'a göre grup büyüklüğünü belirle
     */
    private function getGroupSize(string $mode): int
    {
        return match ($mode) {
            'Squad' => 4,
            'Duo' => 2,
            'Solo' => 1,
            default => 4,
        };
    }

    /**
     * Grup içindeki tüm oyuncular arasındaki ortalama uyumluluğu hesapla
     */
    private function calculateGroupCompatibility(array $group): float
    {
        if (count($group) === 1) {
            return 100; // Solo için her zaman maksimum skor
        }

        $scores = [];
        $count = count($group);

        // Her oyuncu çifti arasındaki skoru hesapla
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $scores[] = $this->calculateTotalScore($group[$i], $group[$j]);
            }
        }

        return count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
    }

    /**
     * Verilen diziden belirli boyutta kombinasyonlar oluştur
     */
    private function getCombinations(array $items, int $size): array
    {
        if ($size === 0) {
            return [[]];
        }

        if (count($items) === 0) {
            return [];
        }

        $head = array_shift($items);
        $combinations = [];

        // Head'i içeren kombinasyonlar
        foreach ($this->getCombinations($items, $size - 1) as $combination) {
            $combinations[] = array_merge([$head], $combination);
        }

        // Head'i içermeyen kombinasyonlar
        foreach ($this->getCombinations($items, $size) as $combination) {
            $combinations[] = $combination;
        }

        return $combinations;
    }

    /**
     * Minimum uyumluluk skorunu al
     */
    public function getMinCompatibilityScore(): int
    {
        return self::MIN_COMPATIBILITY_SCORE;
    }
}
