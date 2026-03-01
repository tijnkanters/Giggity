<?php

namespace App\Models;

use App\Enums\OrganizationRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'current_organization_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->organizations()->exists();
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function currentOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'current_organization_id');
    }

    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by_user_id');
    }

    public function currentRole(): ?OrganizationRole
    {
        if (!$this->current_organization_id) {
            return null;
        }

        $pivot = $this->organizations()
            ->where('organizations.id', $this->current_organization_id)
            ->first()?->pivot;

        return $pivot ? OrganizationRole::from($pivot->role) : null;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(OrganizationRole::ADMIN);
    }

    public function isManager(): bool
    {
        return $this->hasRole(OrganizationRole::MANAGER);
    }

    public function isArtist(): bool
    {
        return $this->hasRole(OrganizationRole::ARTIST);
    }

    public function hasRole(OrganizationRole $role): bool
    {
        return $this->currentRole() === $role;
    }
}
