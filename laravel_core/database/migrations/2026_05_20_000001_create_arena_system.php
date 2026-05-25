<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // COMPETICIONES
        Schema::create('arena_competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['internal', 'open'])->default('internal');
            // internal = solo miembros del gym, open = ranking global entre todos los boxes
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('registration_open')->default(true);
            $table->timestamps();
        });

        // WODs DE LA COMPETICIÓN
        Schema::create('arena_wods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arena_competition_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('score_type', ['time', 'amrap', 'weight', 'reps', 'points'])->default('time');
            $table->unsignedTinyInteger('order')->default(0); // orden de presentación
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // EQUIPOS / PARTICIPANTES
        // Un "team" puede ser 1 (individual), 2 (pareja) o 3-4 (equipo)
        Schema::create('arena_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arena_competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gym_id')->constrained()->cascadeOnDelete();
            // gym_id del equipo (para competiciones open, saber de qué box vienen)
            $table->string('name'); // nombre del equipo o nombre del atleta si es individual
            $table->enum('format', ['individual', 'pairs', 'team'])->default('individual');
            $table->enum('category', ['male', 'female', 'mixed'])->default('male');
            // male = todos hombres, female = todas mujeres, mixed = mixto
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // MIEMBROS DEL EQUIPO (tabla pivote correcta)
        Schema::create('arena_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arena_team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('position')->default(1); // 1-4
            $table->timestamps();

            $table->unique(['arena_team_id', 'user_id']);
            // Un atleta no puede estar dos veces en el mismo equipo
        });

        // RESULTADOS: un equipo, un WOD, un resultado
        Schema::create('arena_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arena_team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('arena_wod_id')->constrained()->cascadeOnDelete();
            $table->string('result_value');
            // Formato libre: "12:30" para tiempo, "150" para peso, "5+12" para AMRAP
            $table->enum('category', ['rx_plus', 'rx', 'scaled'])->default('rx');
            $table->text('notes')->nullable(); // observaciones del gestor
            $table->boolean('is_verified')->default(true); // el gestor lo introduce, va verificado
            $table->unsignedSmallInteger('rank_in_wod')->nullable(); // posición calculada
            $table->unsignedSmallInteger('points')->nullable(); // puntos asignados
            $table->timestamps();

            $table->unique(['arena_team_id', 'arena_wod_id']);
            // Un equipo solo tiene un resultado por WOD
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arena_results');
        Schema::dropIfExists('arena_team_members');
        Schema::dropIfExists('arena_teams');
        Schema::dropIfExists('arena_wods');
        Schema::dropIfExists('arena_competitions');
    }
};
