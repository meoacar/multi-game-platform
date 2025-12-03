<?php

namespace App\Models;

use App\Models\Scopes\GameScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tournament Model
 * Turnuva yönetimi - Oyuna özel
 * 
 * İlişkiler:
 * - belongsTo: User (organizer)
 * - belongsTo: Game
 * - hasMany: TournamentTeam
 * 
 * Global Scope: GameScope (otomatik game_id filtreleme)
 */
class Tournament extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organizer_id',
        'game_id',
        'name',
        'slug',
        'description',
        'rules',
        'prize_pool',
        'max_teams',
        'team_size',
        'registration_starts_at',
        'registration_ends_at',
        'tournament_starts_at',
        'tournament_ends_at',
        'status',
        'bracket_data',
    ];

    protected $casts = [
        'max_teams' => 'integer',
        'team_size' => 'integer',
        'registration_starts_at' => 'datetime',
        'registration_ends_at' => 'datetime',
        'tournament_starts_at' => 'datetime',
        'tournament_ends_at' => 'datetime',
        'bracket_data' => 'array',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(TournamentTeam::class);
    }

    /**
     * Model boot
     * GameScope ekle ve otomatik game_id atama
     */
    protected static function booted(): void
    {
        // Global scope ekle
        static::addGlobalScope(new GameScope());

        // Yeni kayıt oluşturulurken otomatik game_id ata
        static::creating(function ($tournament) {
            if (!$tournament->game_id && session('game_id')) {
                $tournament->game_id = session('game_id');
            }
        });
    }

    /**
     * Scope: Belirli bir oyuna göre filtrele
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $gameId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForGame($query, int $gameId)
    {
        return $query->where('game_id', $gameId);
    }

    /**
     * Scope: Eager load ile ilişkileri yükle (N+1 prevention - Requirements 15.3)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRelations($query)
    {
        return $query->with([
            'organizer.profile',
            'game',
            'teams.captain.profile'
        ]);
    }

    /**
     * Scope: Sadece temel ilişkileri yükle (hafif versiyon)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithBasicRelations($query)
    {
        return $query->with([
            'organizer.profile',
            'game'
        ]);
    }
}
