<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gym_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gym_name')->default('Mi Gimnasio');
            $table->string('primary_color')->default('#f59e0b'); // Naranja por defecto
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('gym_settings');
    }
};
