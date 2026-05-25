<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ArenaTeam extends Model
{
    protected $fillable = ['arena_competition_id','gym_id','name','format','category','is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function competition(): BelongsTo { return $this->belongsTo(ArenaCompetition::class, 'arena_competition_id'); }
    public function gym(): BelongsTo { return $this->belongsTo(Gym::class); }
    public function results(): HasMany { return $this->hasMany(ArenaResult::class); }
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'arena_team_members')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    public function getFormatLabel(): string
    {
        return ['individual' => 'Individual', 'pairs' => 'Parejas', 'team' => 'Equipo'][$this->format] ?? $this->format;
    }

    public function getCategoryLabel(): string
    {
        return ['male' => 'Masculino', 'female' => 'Femenino', 'mixed' => 'Mixto'][$this->category] ?? $this->category;
    }

    public function getTotalPoints(): int
    {
        return $this->results->sum('points') ?? 0;
    }
}
