<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToGym;

class ArenaCompetition extends Model
{
    use BelongsToGym;

    protected $fillable = ['gym_id','name','description','type','start_date','end_date','is_active','registration_open'];
    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'is_active'         => 'boolean',
        'registration_open' => 'boolean',
    ];

    public function gym(): BelongsTo { return $this->belongsTo(Gym::class); }
    public function wods(): HasMany { return $this->hasMany(ArenaWod::class)->orderBy('order'); }
    public function teams(): HasMany { return $this->hasMany(ArenaTeam::class); }

    public function getTypeLabel(): string
    {
        return $this->type === 'open' ? 'Open' : 'Interna';
    }
}
