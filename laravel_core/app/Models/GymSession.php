<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymSession extends Model
{
    use \App\Traits\BelongsToGym;

    protected $fillable = [
        'gym_id',
        'class_type_id',
        'start_time',
        'end_time',
        'capacity',
        'gym_schedule_id',
        'workout_id',      // AÑADIDO: Guardar el WOD
        'is_workout_visible'    // AÑADIDO: Guardar la visibilidad
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_workout_visible' => 'boolean', // Aseguramos que se guarde como true/false
    ];

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function classType(): BelongsTo
    {
        return $this->belongsTo(ClassType::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(GymSchedule::class, 'gym_schedule_id');
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
