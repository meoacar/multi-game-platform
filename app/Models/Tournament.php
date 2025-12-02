<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
