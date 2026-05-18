<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Gym;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buscamos al gestor específico
        $gestor = User::where('email', 'sergiosevarayos@gmail.com')->first();
        
        if (!$gestor) {
            echo "⚠️ ERROR: No se ha encontrado al gestor sergiosevarayos@gmail.com. ¿Seguro que está registrado?\n";
            return;
        }

        // 2. Buscamos el gimnasio asociado a este gestor (en la tabla intermedia)
        $gymUser = DB::table('gym_user')->where('user_id', $gestor->id)->first();
        
        if (!$gymUser) {
            echo "⚠️ ERROR: Sergio existe, pero no tiene ningún gimnasio asociado todavía.\n";
            return;
        }

        $gymId = $gymUser->gym_id;
        echo "✅ Gimnasio encontrado (ID: $gymId). Generando 20 alumnos de prueba...\n";

        // 3. Creamos los 20 alumnos
        $alumnos = User::factory()->count(20)->create([
            'role' => 'student',
            'credits' => 15, // Les damos 15 créditos para que pruebes las reservas
        ]);

        // 4. Los metemos todos en el gimnasio de Sergio
        foreach ($alumnos as $alumno) {
            DB::table('gym_user')->insert([
                'gym_id' => $gymId,
                'user_id' => $alumno->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "🚀 ¡Éxito! 20 alumnos inyectados perfectamente en tu gimnasio.\n";
    }
}
