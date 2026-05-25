<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArenaResult extends Model
{
    protected $fillable = ['arena_team_id','arena_wod_id','result_value','category','notes','is_verified','rank_in_wod','points'];
    protected $casts = ['is_verified' => 'boolean'];

    public function team(): BelongsTo { return $this->belongsTo(ArenaTeam::class, 'arena_team_id'); }
    public function wod(): BelongsTo { return $this->belongsTo(ArenaWod::class, 'arena_wod_id'); }

    public function getCategoryLabel(): string
    {
        return ['rx_plus' => 'Rx+', 'rx' => 'Rx', 'scaled' => 'Scaled'][$this->category] ?? $this->category;
    }
}
