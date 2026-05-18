<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('personal_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->decimal('weight', 8, 2); // Ejemplo: 100.50 kg
            $table->date('achieved_at'); // Fecha en la que consiguió la marca
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('personal_records');
    }
};
