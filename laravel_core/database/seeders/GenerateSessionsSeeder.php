<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymSchedule;
use App\Models\GymSession;
use Carbon\Carbon;

class GenerateSessionsSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = GymSchedule::all();
        
        if ($schedules->isEmpty()) {
            echo "⚠️ ERROR: No hay horarios maestros creados.\n";
            return;
        }

        $generadas = 0;
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(30);

        echo "📅 Generando clases desde " . $startDate->format('d/m/Y') . " hasta " . $endDate->format('d/m/Y') . "...\n";

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeekIso;

            foreach ($schedules as $schedule) {
                // Si ya es un array (Laravel casting), lo usamos. Si no, lo decodificamos.
                $days = is_array($schedule->days_of_week) 
                        ? $schedule->days_of_week 
                        : (json_decode($schedule->days_of_week, true) ?? []);

                if (in_array((string)$dayOfWeek, $days)) {
                    $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->start_time);
                    $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->end_time);

                    $session = GymSession::firstOrCreate([
                        'gym_id' => $schedule->gym_id,
                        'class_type_id' => $schedule->class_type_id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ], [
                        'capacity' => $schedule->capacity,
                    ]);

                    if ($session->wasRecentlyCreated) {
                        $generadas++;
                    }
                }
            }
        }

        echo "🚀 ¡Éxito! Se han dibujado $generadas clases nuevas en tu calendario.\n";
    }
}
