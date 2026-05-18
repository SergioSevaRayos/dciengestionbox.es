<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassType;
use App\Models\GymSchedule;
use App\Models\Gym;
use Illuminate\Support\Facades\DB;

class AirfitScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $gym = Gym::first();
        if (!$gym) {
            echo "⚠️ ERROR: No hay ningún gimnasio creado.\n";
            return;
        }

        // 1. Recuperar las disciplinas
        $classMap = ClassType::where('gym_id', $gym->id)->pluck('id', 'name')->toArray();

        if (empty($classMap)) {
            echo "⚠️ ERROR: No se encontraron las disciplinas.\n";
            return;
        }

        // 2. Limpiar horarios anteriores
        DB::table('gym_schedules')->where('gym_id', $gym->id)->delete();

        // 3. MAPA DE HORARIOS (Día, Inicio, Fin, Disciplina, Aforo)
        $horarios = [
            // LUNES (1)
            [1, '07:00', '08:00', 'Cross Training', 20],
            [1, '08:00', '09:00', 'Cross Training', 20],
            [1, '09:30', '10:30', 'Partner WOD', 24],
            [1, '16:00', '17:00', 'Cross Training', 20],
            [1, '17:00', '18:00', 'Cross Training', 20],
            [1, '18:00', '19:00', 'Halterofilia', 15],
            [1, '19:00', '20:00', 'Cross Training', 20],
            [1, '20:00', '21:00', 'Cross Training', 20],
            [1, '21:00', '22:00', 'Cross Training', 20],

            // MARTES (2)
            [2, '07:00', '08:00', 'Cross Training', 20],
            [2, '08:00', '09:00', 'Cross Training', 20],
            [2, '09:30', '10:30', 'Halterofilia', 15],
            [2, '11:30', '13:00', 'Open Box', 30],
            [2, '16:00', '17:00', 'Cross Training', 20],
            [2, '17:00', '18:00', 'Cross Training', 20],
            [2, '18:00', '19:00', 'Cross Training', 20],
            [2, '19:00', '20:00', 'Cross Training', 20],
            [2, '20:00', '21:00', 'Cross Training', 20],
            [2, '21:00', '22:00', 'Cross Training', 20],

            // MIÉRCOLES (3)
            [3, '07:00', '08:00', 'Cross Training', 20],
            [3, '08:00', '09:00', 'Cross Training', 20],
            [3, '09:30', '10:30', 'Cross Training', 20],
            [3, '11:30', '13:00', 'Open Box', 30],
            [3, '16:00', '17:00', 'Cross Training', 20],
            [3, '17:00', '18:00', 'Cross Training', 20],
            [3, '18:00', '19:00', 'Cross Training', 20],
            [3, '19:00', '20:00', 'Cross Training', 20],
            [3, '20:00', '21:00', 'Gymnastic', 15],
            [3, '21:00', '22:00', 'Cross Training', 20],

            // JUEVES (4)
            [4, '07:00', '08:00', 'Endurance', 20],
            [4, '08:00', '09:00', 'Endurance', 20],
            [4, '09:30', '10:30', 'Gymnastic', 15],
            [4, '11:30', '13:00', 'Open Box', 30],
            [4, '16:00', '17:00', 'Endurance', 20],
            [4, '17:00', '18:00', 'Endurance', 20],
            [4, '18:00', '19:00', 'Endurance', 20],
            [4, '19:00', '20:00', 'Endurance', 20],
            [4, '20:00', '21:00', 'Endurance', 20],
            [4, '21:00', '22:00', 'Endurance', 20],

            // VIERNES (5)
            [5, '07:00', '08:00', 'Cross Training', 20],
            [5, '08:00', '09:00', 'Cross Training', 20],
            [5, '09:30', '10:30', 'Endurance', 20],
            [5, '11:30', '13:00', 'Open Box', 30],
            [5, '16:00', '17:00', 'Cross Training', 20],
            [5, '17:00', '18:00', 'Cross Training', 20],
            [5, '18:00', '19:00', 'Cross Training', 20],
            [5, '19:00', '20:00', 'Cross Training', 20],
            [5, '20:00', '21:00', 'Cross Training', 20],

            // SÁBADO (6)
            [6, '09:30', '10:30', 'Powerlifting', 12],
            [6, '10:00', '11:00', 'Partner WOD', 24],
            [6, '11:30', '13:00', 'Open Box', 30],
        ];

        // 4. Inyectar en la base de datos
        foreach ($horarios as $h) {
            if (!isset($classMap[$h[3]])) continue;

            DB::table('gym_schedules')->insert([
                'gym_id' => $gym->id,
                'days_of_week' => json_encode([(string)$h[0]]),
                'start_time' => $h[1] . ':00',
                'end_time' => $h[2] . ':00',
                'class_type_id' => $classMap[$h[3]],
                'capacity' => $h[4],
                'repeat_until' => '2031-12-31', // <- ¡LA SOLUCIÓN!
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "🚀 ¡Éxito! Horario maestro cargado al 100%.\n";
    }
}
