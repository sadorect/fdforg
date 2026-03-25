<?php

namespace App\Models;

use App\Support\AdminPermissions;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'bio',
        'learner_type',
        'location',
        'country',
        'phone_number',
        'organization_name',
        'learner_profile_completed_at',
        'learner_profile_deferred_at',
        'email_verified_at',
        'is_admin',
        'password',
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
            'is_admin' => 'boolean',
            'learner_profile_completed_at' => 'datetime',
            'learner_profile_deferred_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public const LEARNER_TYPES = [
        'educator',
        'parent',
        'guardian',
        'student',
        'explorer',
    ];

    public static function learnerTypeOptions(): array
    {
        return array_combine(self::LEARNER_TYPES, array_map(
            static fn (string $type): string => ucfirst($type),
            self::LEARNER_TYPES
        ));
    }

    public function hasCompleteLearnerProfile(): bool
    {
        return filled($this->name)
            && filled($this->learner_type)
            && filled($this->location)
            && filled($this->country);
    }

    public function shouldPromptForLearnerProfile(): bool
    {
        return ! $this->hasCompleteLearnerProfile();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->is_admin;
    }

    public function hasRole(string $roleSlug): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('slug', $roleSlug);
        }

        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    public function hasAnyPermission(array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $permissionSlug) {
            if ($this->hasPermission($permissionSlug)) {
                return true;
            }
        }

        return false;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if (! $this->canAccessAdminPanel()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        $acceptableSlugs = AdminPermissions::acceptableSlugs($permissionSlug);

        if ($this->relationLoaded('roles')) {
            return $this->roles->contains(function (Role $role) use ($acceptableSlugs) {
                if ($role->relationLoaded('permissions')) {
                    return $role->permissions->contains(function (Permission $permission) use ($acceptableSlugs) {
                        return in_array($permission->slug, $acceptableSlugs, true);
                    });
                }

                return $role->permissions()->whereIn('slug', $acceptableSlugs)->exists();
            });
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($acceptableSlugs) {
                $query->whereIn('slug', $acceptableSlugs);
            })
            ->exists();
    }
}
