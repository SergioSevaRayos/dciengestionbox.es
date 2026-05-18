<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workout extends Model
{
    use \App\Traits\BelongsToGym;

    // ✨ AÑADIDO: 'date' a la lista de campos rellenables
    protected $fillable = ['gym_id', 'name', 'date', 'content'];

    public function gymSessions(): HasMany
    {
        return $this->hasMany(GymSession::class);
    }
}
