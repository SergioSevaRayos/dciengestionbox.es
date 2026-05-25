<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArenaWod extends Model
{
    protected $fillable = ['arena_competition_id','name','description','score_type','order','is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function competition(): BelongsTo { return $this->belongsTo(ArenaCompetition::class, 'arena_competition_id'); }
    public function results(): HasMany { return $this->hasMany(ArenaResult::class); }

    public function getScoreTypeLabel(): string
    {
        return [
            'time'   => '⏱ Tiempo',
            'amrap'  => '🔄 AMRAP',
            'weight' => '🏋️ Peso',
            'reps'   => '💪 Reps',
            'points' => '🏆 Puntos',
        ][$this->score_type] ?? $this->score_type;
    }

    // Convierte el resultado a número comparable para ranking
    public function resultToSortValue(string $value): float
    {
        if ($this->score_type === 'time') {
            $parts = explode(':', $value);
            if (count($parts) === 2) return (int)$parts[0] * 60 + (int)$parts[1];
            if (count($parts) === 3) return (int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2];
        }
        if ($this->score_type === 'amrap') {
            // formato "5+12" = 5 rondas + 12 reps
            $parts = explode('+', $value);
            return isset($parts[1]) ? (int)$parts[0] * 1000 + (int)$parts[1] : (float)$value;
        }
        return (float)$value;
    }

    // ¿Gana el valor más bajo? (solo en tiempo)
    public function lowerIsBetter(): bool
    {
        return $this->score_type === 'time';
    }
}
