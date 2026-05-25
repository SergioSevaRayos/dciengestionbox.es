<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('arena_competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['internal', 'open'])->default('internal');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('arena_wods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arena_competition_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('score_type', ['time', 'amrap', 'weight', 'reps', 'none'])->default('time');
            $table->timestamps();
        });

        Schema::create('arena_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arena_wod_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('score'); // String porque puede ser '12:30', '100', o '5+12'
            $table->enum('category', ['rx_plus', 'rx', 'scaled'])->default('rx');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            
            // Un usuario solo puede tener una puntuación por WOD
            $table->unique(['arena_wod_id', 'user_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('arena_scores');
        Schema::dropIfExists('arena_wods');
        Schema::dropIfExists('arena_competitions');
    }
};