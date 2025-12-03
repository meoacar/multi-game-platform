<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * GameScope - Global Scope for Multi-Game Platform
 * 
 * Bu scope, oyuna özel modellere otomatik olarak game_id filtrelemesi uygular.
 * Session'daki game_id değerine göre sorguları filtreler.
 * 
 * Kullanım:
 * - Model'e otomatik olarak eklenir (booted metodunda)
 * - Session'da game_id varsa, tüm sorgular otomatik filtrelenir
 * - withoutGameScope() ile devre dışı bırakılabilir
 * 
 * Requirements: 4.1, 4.2
 * - 4.1: Oyuna özel entity'ler sorgulanırken otomatik game_id filtreleme
 * - 4.2: Model seviyesinde global scope ile filtreleme
 */
class GameScope implements Scope
{
    /**
     * Scope'u query'ye uygula
     * 
     * Session'daki game_id değerine göre otomatik filtreleme yapar.
     * Eğer session'da game_id yoksa, filtreleme yapılmaz.
     * 
     * @param Builder $builder Query builder instance
     * @param Model $model Model instance
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Session'dan game_id'yi al
        $gameId = session('game_id');
        
        // Eğer game_id varsa, filtreleme uygula
        if ($gameId) {
            $builder->where($model->getTable() . '.game_id', $gameId);
        }
    }
}
