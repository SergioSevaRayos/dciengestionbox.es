<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gym_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_type_id')->constrained()->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->json('days_of_week'); // [1, 3] para Lunes y Miércoles
            $table->date('repeat_until');
            $table->integer('capacity');
            $table->timestamps();
        });

        // Añadimos una conexión en la tabla de sesiones para saber de qué horario vienen
        Schema::table('gym_sessions', function (Blueprint $table) {
            $table->foreignId('gym_schedule_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_schedules');
    }
};
