<?php

namespace App\Filament\Resources\GymScheduleResource\Pages;

use App\Filament\Resources\GymScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\GymSession;
use Carbon\Carbon;

class EditGymSchedule extends EditRecord
{
    protected static string $resource = GymScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * SINCRONIZACIÓN: Al editar el horario, actualizamos el calendario
     */
    protected function afterSave(): void
    {
        $schedule = $this->record;

        // 1. Borramos todas las sesiones futuras de este horario
        // (Dejamos las pasadas como historial)
        GymSession::where('gym_schedule_id', $schedule->id)
            ->where('start_time', '>=', now()->startOfDay())
            ->delete();

        // 2. Volvemos a generar las sesiones con los nuevos datos
        $currentDate = now()->startOfDay();
        $endDate = Carbon::parse($schedule->repeat_until)->endOfDay();

        while ($currentDate->lte($endDate)) {
            if (in_array($currentDate->dayOfWeek, $schedule->days_of_week)) {
                GymSession::create([
                    'gym_id' => $schedule->gym_id, // <-- ¡LA CLAVE! Ahora las clases no se pierden
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
