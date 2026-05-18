<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('gym_settings', function (Blueprint $table) {
            $table->string('background_color')->nullable()->default('#030712'); // Gris casi negro por defecto
        });
    }
    public function down(): void {
        Schema::table('gym_settings', function (Blueprint $table) {
            $table->dropColumn('background_color');
        });
    }
};
