<?php

namespace App\Filament\Resources\GymScheduleResource\Pages;

use App\Filament\Resources\GymScheduleResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\GymSession;
use Carbon\Carbon;

class CreateGymSchedule extends CreateRecord
{
    protected static string $resource = GymScheduleResource::class;

    protected function afterCreate(): void
    {
        $schedule = $this->record;
        
        // Generamos las sesiones desde el día de hoy hasta la fecha fin
        $currentDate = now()->startOfDay();
        $endDate = Carbon::parse($schedule->repeat_until)->endOfDay();

        while ($currentDate->lte($endDate)) {
            // Comprobamos si el día de la semana coincide con los elegidos
            if (in_array($currentDate->dayOfWeek, $schedule->days_of_week)) {
                GymSession::create([
                    'gym_schedule_id' => $schedule->id,
                    'class_type_id' => $schedule->class_type_id,
                    'capacity' => $schedule->capacity,
                    'start_time' => $currentDate->copy()->setTimeFromTimeString($schedule->start_time),
                    'end_time' => $currentDate->copy()->setTimeFromTimeString($schedule->end_time),
                ]);
            }
            $currentDate->addDay();
        }
    }
}