<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arena_wods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arena_competition_id')->constrained('arena_competitions')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('score_type', ['time', 'amrap', 'weight', 'reps', 'points'])->default('time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arena_wods');
    }
};
