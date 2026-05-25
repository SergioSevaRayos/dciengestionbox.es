<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable implements FilamentUser, HasTenants, HasAvatar
{
    use Notifiable;

    protected $fillable = ['gym_id', 'name', 'email', 'password', 'role', 'avatar'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['password' => 'hashed'];

    // Filament avatar
    public function getFilamentAvatarUrl(): ?string
    {
        if (!$this->avatar) return null;
        return asset('storage/' . $this->avatar);
    }

    public function gyms(): BelongsToMany { return $this->belongsToMany(Gym::class, 'gym_user'); }
    public function gym(): BelongsTo { return $this->belongsTo(Gym::class); }
    public function userPackages(): HasMany { return $this->hasMany(UserPackage::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }

    public function getTenants(Panel $panel): Collection {
        if ($this->role === 'super_admin') return \App\Models\Gym::all();
        return $this->gyms;
    }

    public function canAccessTenant(Model $tenant): bool {
        if ($this->role === 'super_admin') return true;
        return $this->gyms->contains($tenant);
    }

    public function canAccessPanel(Panel $panel): bool {
        if ($panel->getId() === 'system') return $this->role === 'super_admin';
        if ($panel->getId() === 'admin') return in_array($this->role, ['admin', 'super_admin']);
        if ($panel->getId() === 'app') return $this->role === 'student';
        return false;
    }

    public function activePackages() {
        return $this->userPackages()->where('expires_at', '>', now())->where(function ($query) {
            $query->where('remaining_credits', '>', 0)->orWhere('type', 'tarifa');
        });
    }

    public function getRealCreditsAttribute(): int {
        return (int) $this->activePackages()->sum('remaining_credits');
    }
}
