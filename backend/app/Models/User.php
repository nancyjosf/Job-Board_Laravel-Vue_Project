<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'skills',
        'company_name',
        'cv',
        'image',
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
            'role' => UserRole::class,
        ];
    }

    public function hasRole(UserRole|string ...$roles): bool
    {
        $currentRole = $this->role instanceof UserRole ? $this->role->value : (string) $this->role;

        $allowedRoles = array_map(
            static fn (UserRole|string $role): string => $role instanceof UserRole ? $role->value : $role,
            $roles,
        );

        return in_array($currentRole, $allowedRoles, true);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }
}