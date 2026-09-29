<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'first_name', 'last_name', 'email', 'phone', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function isCertified(): bool
    {
        return in_array($this->role, ['PROVIDER', 'ADMIN'], true);
    }

    public function getHandleAttribute(): string
    {
        if ($this->role === 'PROVIDER') {
            return '@eneocameroon';
        }
        if ($this->role === 'ADMIN') {
            return '@delestalert_hq';
        }

        $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $this->name ?? 'user'));

        return '@'.trim($clean, '_');
    }

    /**
     * Determine whether the user holds one of the supplied roles.
     *
     * Provider and Administrator accounts inherit Client capabilities while retaining their own controls.
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true)
            || (in_array('CLIENT', $roles, true) && in_array($this->role, ['PROVIDER', 'ADMIN'], true));
    }

    public function locations(): HasMany
    {
        return $this->hasMany(SavedLocation::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(OutageReport::class);
    }

    public function communityPosts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function communityComments(): HasMany
    {
        return $this->hasMany(CommunityComment::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function billPayments(): HasMany
    {
        return $this->hasMany(BillPayment::class);
    }

    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

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
        ];
    }
}
