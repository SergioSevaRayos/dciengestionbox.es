<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Gym extends Model
{
    protected $fillable = ['name', 'slug', 'stripe_id', 'is_subscribed', 'subscription_ends_at'];

    protected $casts = [
        'is_subscribed' => 'boolean',
        'subscription_ends_at' => 'datetime',
    ];

    // Relación con los Gestores/Usuarios
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    // --- RELACIONES SAAS (Para que Filament pueda guardar datos aislados) ---

    public function classTypes(): HasMany
    {
        return $this->hasMany(ClassType::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function gymSessions(): HasMany
    {
        return $this->hasMany(GymSession::class);
    }

    public function gymSchedules(): HasMany
    {
        return $this->hasMany(GymSchedule::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }

    public function packageRenewals(): HasMany
    {
        return $this->hasMany(PackageRenewal::class);
    }

    public function personalRecords(): HasMany
    {
        return $this->hasMany(PersonalRecord::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }

    /**
     * Relación con los tickets de soporte.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(\App\Models\Ticket::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(\App\Models\GymSetting::class);
    }

    public function arenaCompetitions(): HasMany
    {
        return $this->hasMany(ArenaCompetition::class);
    }

    public function arenaTeams(): HasMany
    {
        return $this->hasMany(ArenaTeam::class);
    }
}