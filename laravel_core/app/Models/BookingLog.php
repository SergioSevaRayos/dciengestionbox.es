<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;

class BookingLog extends Model {
    use \App\Traits\BelongsToGym;
    use MassPrunable;
    
    protected $guarded = [];

    /**
     * Define la consulta para los registros que deben ser eliminados.
     */
    public function prunable()
    {
        // Limpia automáticamente registros con más de 30 días
        return static::where('created_at', '<=', now()->subDays(30));
    }

    public function user() { return $this->belongsTo(User::class); }
    public function gymSession() { return $this->belongsTo(GymSession::class); }
}
