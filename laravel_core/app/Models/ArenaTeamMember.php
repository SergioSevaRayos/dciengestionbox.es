<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArenaTeamMember extends Model
{
    protected $fillable = ['arena_team_id','user_id','position'];

    public function team(): BelongsTo { return $this->belongsTo(ArenaTeam::class, 'arena_team_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
