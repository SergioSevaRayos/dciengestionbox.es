<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // A. Crear la tabla de WODs
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->longText('content');
            $table->timestamps();
        });

        // B. Añadir la relación a las clases
        Schema::table('gym_sessions', function (Blueprint $table) {
            $table->foreignId('workout_id')->nullable()->constrained('workouts')->nullOnDelete();
        });

        // C. TRASPASO DE DATOS INTELIGENTE (Agrupa WODs idénticos del mismo gimnasio)
        $uniqueWods = DB::table('gym_sessions')
            ->whereNotNull('workout_content')->where('workout_content', '!=', '')
            ->select('gym_id', 'workout_content', DB::raw('MIN(start_time) as first_date'))
            ->groupBy('gym_id', 'workout_content')->get();

        foreach($uniqueWods as $wod) {
            $workoutId = DB::table('workouts')->insertGetId([
                'gym_id' => $wod->gym_id,
                'name' => 'WOD ' . date('d/m/Y', strtotime($wod->first_date)),
                'content' => $wod->workout_content,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('gym_sessions')
                ->where('gym_id', $wod->gym_id)->where('workout_content', $wod->workout_content)
                ->update(['workout_id' => $workoutId]);
        }

        // D. Limpiar la columna vieja
        Schema::table('gym_sessions', function (Blueprint $table) {
            $table->dropColumn('workout_content');
        });
    }

    public function down(): void {
        // En caso de rollback
    }
};
