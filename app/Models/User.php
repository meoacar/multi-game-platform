<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

/**
 * User Model
 * 
 * İlişkiler:
 * - hasOne: Profile, Device
 * - hasMany: LfgPost, LfgApplication, Clan (lider), ClanApplication, GuidePost, Comment, XpEvent, CommunityPost, Message, Notification, Report
 * - belongsToMany: Clan (üye), Badge, User (friendships)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'status',
        'xp_total',
        'last_login_at',
        'fcm_token',
        'device_type',
        'fcm_token_updated_at',
        // Multi-game
        'game_id',
        // Onboarding kolonları
        'onboarding_completed',
        'onboarding_step',
        'profile_completion',
        'pubg_id',
        'player_level',
        'player_tier',
        'main_server',
        'favorite_mode',
        'favorite_type',
        'active_hours',
        'interests',
        'push_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'xp_total' => 'integer',
            'last_login_at' => 'datetime',
            'fcm_token_updated_at' => 'datetime',
            'settings' => 'array',
            // Onboarding casts
            'onboarding_completed' => 'boolean',
            'onboarding_step' => 'integer',
            'profile_completion' => 'integer',
            'player_level' => 'integer',
            'active_hours' => 'array',
            'interests' => 'array',
            'push_enabled' => 'boolean',
        ];
    }

    /**
     * Kullanıcının ana oyunu
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Kullanıcının profili
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Kullanıcının cihaz bilgisi
     */
    public function device()
    {
        return $this->hasOne(Device::class);
    }

    /**
     * Kullanıcının admin olup olmadığını kontrol et
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Kullanıcının aktif olup olmadığını kontrol et
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Kullanıcının banlanmış olup olmadığını kontrol et
     */
    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    /**
     * Son giriş zamanını güncelle
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    // ==========================================
    // Onboarding Metodları
    // ==========================================

    /**
     * Kullanıcının onboarding'i tamamlayıp tamamlamadığını kontrol et
     * 
     * @return bool
     */
    public function hasCompletedOnboarding(): bool
    {
        return (bool) $this->onboarding_completed;
    }

    /**
     * Profil tamamlanma yüzdesini hesapla
     * 
     * Doldurulmuş alan sayısına göre yüzde hesaplar.
     * Toplam 12 onboarding alanı var.
     * 
     * @return int
     */
    public function getProfileCompletionAttribute(): int
    {
        // Eğer manuel olarak ayarlanmışsa onu kullan
        if (isset($this->attributes['profile_completion'])) {
            return (int) $this->attributes['profile_completion'];
        }

        // Kontrol edilecek alanlar
        $fields = [
            'pubg_id',
            'player_level',
            'player_tier',
            'main_server',
            'favorite_mode',
            'favorite_type',
            'active_hours',
            'interests',
            'push_enabled',
        ];

        $filledCount = 0;
        $totalFields = count($fields);

        foreach ($fields as $field) {
            $value = $this->attributes[$field] ?? null;
            
            // Array alanlar için özel kontrol
            if (in_array($field, ['active_hours', 'interests'])) {
                $decoded = is_string($value) ? json_decode($value, true) : $value;
                if (!empty($decoded)) {
                    $filledCount++;
                }
            } 
            // Boolean alanlar için özel kontrol (push_enabled)
            elseif ($field === 'push_enabled') {
                // push_enabled her zaman sayılır (true veya false)
                $filledCount++;
            }
            // Diğer alanlar
            elseif (!empty($value)) {
                $filledCount++;
            }
        }

        return (int) round(($filledCount / $totalFields) * 100);
    }

    /**
     * Onboarding adımını güncelle
     * 
     * @param int $step Yeni adım numarası (1-4 arası)
     * @return void
     */
    public function updateOnboardingStep(int $step): void
    {
        $this->update(['onboarding_step' => $step]);
    }

    /**
     * Kullanıcının LFG ilanları
     */
    public function lfgPosts()
    {
        return $this->hasMany(LfgPost::class);
    }

    /**
     * Kullanıcının LFG başvuruları
     */
    public function lfgApplications()
    {
        return $this->hasMany(LfgApplication::class);
    }

    /**
     * Kullanıcının lider olduğu klanlar
     */
    public function ownedClans()
    {
        return $this->hasMany(Clan::class);
    }

    /**
     * Kullanıcının üye olduğu klanlar
     */
    public function clans()
    {
        return $this->belongsToMany(Clan::class, 'clan_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Kullanıcının klan başvuruları
     */
    public function clanApplications()
    {
        return $this->hasMany(ClanApplication::class);
    }

    /**
     * Kullanıcının rehber yazıları
     */
    public function guidePosts()
    {
        return $this->hasMany(GuidePost::class);
    }

    /**
     * Kullanıcının topluluk postları
     */
    public function communityPosts()
    {
        return $this->hasMany(CommunityPost::class);
    }

    /**
     * Kullanıcının yorumları
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Kullanıcının gönderdiği mesajlar
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Kullanıcının aldığı mesajlar
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Kullanıcının arkadaşlıkları
     */
    public function friendships()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    /**
     * Kullanıcının rozetleri
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    /**
     * Kullanıcının XP eventleri
     */
    public function xpEvents()
    {
        return $this->hasMany(XpEvent::class);
    }

    /**
     * Kullanıcının lider olduğu takımlar
     */
    public function ownedSquads()
    {
        return $this->hasMany(Squad::class, 'leader_id');
    }

    /**
     * Kullanıcının üye olduğu takımlar
     */
    public function squads()
    {
        return $this->belongsToMany(Squad::class, 'squad_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Kullanıcının düzenlediği turnuvalar
     */
    public function organizedTournaments()
    {
        return $this->hasMany(Tournament::class, 'organizer_id');
    }

    /**
     * Kullanıcının yaptığı raporlar
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Kullanıcı hakkındaki admin notları
     */
    public function adminNotes()
    {
        return $this->hasMany(AdminNote::class)->orderBy('created_at', 'desc');
    }

    /**
     * Kullanıcının matchmaking kuyruğu
     */
    public function matchmakingQueue()
    {
        return $this->hasOne(MatchmakingQueue::class);
    }

    /**
     * Kullanıcının matchmaking geçmişi
     */
    public function matchmakingHistory()
    {
        return $this->hasMany(MatchmakingHistory::class);
    }

    /**
     * Kullanıcının matchmaking tercihleri
     */
    public function matchmakingPreference()
    {
        return $this->hasOne(MatchmakingPreference::class);
    }

    /**
     * Kullanıcının level'ını hesapla
     */
    public function getLevel(): int
    {
        $xpService = app(\App\Services\XpService::class);
        return $xpService->calculateLevel($this->xp_total ?? 0);
    }

    /**
     * Kullanıcının level ilerlemesini al
     */
    public function getLevelProgress(): array
    {
        $xpService = app(\App\Services\XpService::class);
        $level = $this->getLevel();
        return $xpService->getLevelProgress($this->xp_total ?? 0, $level);
    }

    /**
     * Kullanıcıya XP ekle
     */
    public function addXp(string $type, ?array $meta = null)
    {
        $xpService = app(\App\Services\XpService::class);
        return $xpService->addXp($this, $type, $meta);
    }

    /**
     * Şifre sıfırlama notification'ını gönder
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // ==========================================
    // Admin Rol ve Yetki Metodları
    // ==========================================

    /**
     * Kullanıcının admin rolleri
     */
    public function adminRoles()
    {
        return $this->belongsToMany(
            AdminRole::class,
            'user_role',
            'user_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * Kullanıcının özel yetkileri (granted/revoked)
     */
    public function customPermissions()
    {
        return $this->belongsToMany(
            AdminPermission::class,
            'user_permission',
            'user_id',
            'permission_id'
        )->withPivot('granted');
    }

    /**
     * Kullanıcının belirli bir yetkiye sahip olup olmadığını kontrol et
     * 
     * @param string $permission Yetki slug'ı
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        // Admin değilse direkt false
        if (!$this->is_admin) {
            return false;
        }

        // Önce özel yetki kontrolü yap
        $customPermission = $this->customPermissions()
            ->where('slug', $permission)
            ->first();

        if ($customPermission) {
            return (bool) $customPermission->pivot->granted;
        }

        // Rol yetkisi kontrolü
        return $this->adminRoles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('slug', $permission);
            })
            ->exists();
    }

    /**
     * Kullanıcının belirli bir role sahip olup olmadığını kontrol et
     * 
     * @param string $role Rol slug'ı
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        // Admin değilse direkt false
        if (!$this->is_admin) {
            return false;
        }
        
        return $this->adminRoles()->where('slug', $role)->exists();
    }

    /**
     * Kullanıcının birden fazla rolden birine sahip olup olmadığını kontrol et
     * 
     * @param array $roles Rol slug'ları
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->adminRoles()->whereIn('slug', $roles)->exists();
    }

    /**
     * Kullanıcının tüm rollere sahip olup olmadığını kontrol et
     * 
     * @param array $roles Rol slug'ları
     * @return bool
     */
    public function hasAllRoles(array $roles): bool
    {
        $userRoles = $this->adminRoles()->pluck('slug')->toArray();
        return count(array_intersect($roles, $userRoles)) === count($roles);
    }

    /**
     * Kullanıcıya rol ata
     * 
     * @param int|AdminRole|string $role Rol ID, model veya slug
     * @return void
     */
    public function assignRole($role): void
    {
        if (is_string($role)) {
            $role = AdminRole::where('slug', $role)->firstOrFail();
        }

        $roleId = $role instanceof AdminRole ? $role->id : $role;

        if (!$this->adminRoles()->where('role_id', $roleId)->exists()) {
            $this->adminRoles()->attach($roleId);
        }
    }

    /**
     * Kullanıcıdan rol kaldır
     * 
     * @param int|AdminRole|string $role Rol ID, model veya slug
     * @return void
     */
    public function removeRole($role): void
    {
        if (is_string($role)) {
            $role = AdminRole::where('slug', $role)->firstOrFail();
        }

        $roleId = $role instanceof AdminRole ? $role->id : $role;
        $this->adminRoles()->detach($roleId);
    }

    /**
     * Kullanıcının tüm rollerini senkronize et
     * 
     * @param array $roles Rol ID'leri veya slug'ları
     * @return void
     */
    public function syncRoles(array $roles): void
    {
        $roleIds = collect($roles)->map(function ($role) {
            if (is_string($role)) {
                return AdminRole::where('slug', $role)->firstOrFail()->id;
            }
            return $role instanceof AdminRole ? $role->id : $role;
        })->toArray();

        $this->adminRoles()->sync($roleIds);
    }

    /**
     * Kullanıcıya özel yetki ver
     * 
     * @param int|AdminPermission|string $permission Yetki ID, model veya slug
     * @return void
     */
    public function givePermission($permission): void
    {
        if (is_string($permission)) {
            $permission = AdminPermission::where('slug', $permission)->firstOrFail();
        }

        $permissionId = $permission instanceof AdminPermission ? $permission->id : $permission;

        $this->customPermissions()->syncWithoutDetaching([
            $permissionId => ['granted' => true]
        ]);
    }

    /**
     * Kullanıcıdan özel yetki kaldır
     * 
     * @param int|AdminPermission|string $permission Yetki ID, model veya slug
     * @return void
     */
    public function revokePermission($permission): void
    {
        if (is_string($permission)) {
            $permission = AdminPermission::where('slug', $permission)->firstOrFail();
        }

        $permissionId = $permission instanceof AdminPermission ? $permission->id : $permission;

        $this->customPermissions()->syncWithoutDetaching([
            $permissionId => ['granted' => false]
        ]);
    }

    /**
     * Kullanıcının tüm yetkilerini al (rol + özel yetkiler)
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissions()
    {
        // Rollerden gelen yetkiler
        $rolePermissions = $this->adminRoles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');

        // Özel yetkiler (granted = true olanlar)
        $customPermissions = $this->customPermissions()
            ->wherePivot('granted', true)
            ->get();

        // Kaldırılan yetkiler (granted = false olanlar)
        $revokedPermissions = $this->customPermissions()
            ->wherePivot('granted', false)
            ->pluck('id')
            ->toArray();

        // Birleştir ve kaldırılanları çıkar
        return $rolePermissions
            ->merge($customPermissions)
            ->unique('id')
            ->reject(function ($permission) use ($revokedPermissions) {
                return in_array($permission->id, $revokedPermissions);
            });
    }

    /**
     * Süper admin mi kontrol et
     * 
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_admin && $this->hasRole('super_admin');
    }

    /**
     * Moderatör mü kontrol et
     * 
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->is_admin && $this->hasRole('moderator');
    }

    /**
     * İçerik yöneticisi mi kontrol et
     * 
     * @return bool
     */
    public function isContentManager(): bool
    {
        return $this->is_admin && $this->hasRole('content_manager');
    }
}
