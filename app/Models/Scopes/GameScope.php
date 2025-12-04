<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * GameScope - Otomatik oyun filtresi
 * 
 * Bu scope, tüm query'lere otomatik olarak mevcut oyunun game_id'sini ekler.
 * Subdomain'e göre otomatik filtreleme yapar.
 * 
 * Kullanım:
 * - Model'e trait ekle: use HasGameScope;
 * - Otomatik olarak mevcut oyuna göre filtrelenir
 * - Tüm oyunları görmek için: Model::withoutGlobalScope('game')->get()
 */
class GameScope implements Scope
{
    /**
     * Scope'u query'ye uygula
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Eğer game_id session'da veya config'de varsa uygula
        $gameId = $this->getCurrentGameId();
        
        if ($gameId) {
            $builder->where($model->getTable() . '.game_id', $gameId);
        }
    }

    /**
     * Mevcut oyun ID'sini al
     */
    protected function getCurrentGameId(): ?int
    {
        // 1. Session'dan kontrol et
        if (session()->has('current_game_id')) {
            return session('current_game_id');
        }

        // 2. Config'den kontrol et (middleware tarafından set edilir)
        if (config('app.current_game_id')) {
            return config('app.current_game_id');
        }

        // 3. Request'ten kontrol et
        if (request()->has('game_id')) {
            return request()->get('game_id');
        }

        return null;
    }
}
