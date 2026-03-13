<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'onesignal_player_id',
        'is_active',
        'user_type',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Roles assigned to user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
                    ->withTimestamps();
    }

    /**
     * Social accounts linked to user
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * User notification subscription preferences
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string|array $roles): bool
    {
        // Quick check via user_type
        $typeMap = ['admin' => self::TYPE_ADMIN, 'editor' => self::TYPE_EDITOR];
        $checkRoles = is_string($roles) ? [$roles] : $roles;
        foreach ($checkRoles as $r) {
            if (isset($typeMap[$r]) && $this->user_type === $typeMap[$r]) {
                return true;
            }
        }
        // Also check admin user_type for any role (admin has all roles)
        if ($this->user_type === self::TYPE_ADMIN) {
            return true;
        }

        if (is_string($roles)) {
            return $this->roles()->where('name', $roles)->exists();
        }

        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles()
                    ->whereHas('permissions', function ($query) use ($permission) {
                        $query->where('name', $permission);
                    })
                    ->exists();
    }

    // user_type: 0 = user, 1 = admin, 2 = editor
    const TYPE_USER = 0;
    const TYPE_ADMIN = 1;
    const TYPE_EDITOR = 2;

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::TYPE_ADMIN || $this->hasRole('admin');
    }

    /**
     * Check if user is editor
     */
    public function isEditor(): bool
    {
        return $this->user_type === self::TYPE_EDITOR || $this->isAdmin() || $this->hasRole(['admin', 'editor']);
    }

    /**
     * Assign role to user
     */
    public function assignRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Remove role from user
     */
    public function removeRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role->id);
    }

    /**
     * Get all permissions through roles
     */
    public function getAllPermissions(): \Illuminate\Support\Collection
    {
        return $this->roles()
                    ->with('permissions')
                    ->get()
                    ->pluck('permissions')
                    ->flatten()
                    ->unique('id');
    }
}
