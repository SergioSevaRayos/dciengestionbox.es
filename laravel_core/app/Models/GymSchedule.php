<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GymSchedule extends Model
{
    use \App\Traits\BelongsToGym;

    protected $fillable = [
        'gym_id','class_type_id', 'start_time', 'end_time', 'days_of_week', 'repeat_until', 'capacity'];

    protected $casts = [
        'days_of_week' => 'array',
        'repeat_until' => 'date',
    ];

    // --- ESTE ES EL SEGURO DE BORRADO ---
        protected static function booted()
    {
        static::saved(function ($schedule) {
            // 1. Limpiamos sesiones futuras vinculadas a este horario para no duplicar
            \App\Models\GymSession::where("gym_schedule_id", $schedule->id)
                ->where("start_time", ">=", now())
                ->delete();
            // 2. Generamos sesiones para los próximos 30 días basándonos en days_of_week
            $days = $schedule->days_of_week;
            if ($days && is_array($days)) {
                for ($i = 0; $i < 30; $i++) {
                    $currentDate = now()->addDays($i);
                    // format("N") devuelve 1 (Lunes) hasta 7 (Domingo)
                    if (in_array($currentDate->format("N"), $days)) {
                        \App\Models\GymSession::create([
                            "gym_id" => $schedule->gym_id,
                            "gym_schedule_id" => $schedule->id,
                            "class_type_id" => $schedule->class_type_id,
                            "start_time" => $currentDate->format("Y-m-d") . " " . $schedule->start_time,
                            "end_time" => $currentDate->format("Y-m-d") . " " . $schedule->end_time,
                            "capacity" => $schedule->capacity,
                        ]);
                    }
                }
            }
        });
        static::deleting(function ($schedule) {
            // Limpieza total al eliminar el horario maestro
            \App\Models\GymSession::where("gym_schedule_id", $schedule->id)->delete();
        });
    }

    public function classType(): BelongsTo
    {
        return $this->belongsTo(ClassType::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(GymSession::class);
    }

    
}