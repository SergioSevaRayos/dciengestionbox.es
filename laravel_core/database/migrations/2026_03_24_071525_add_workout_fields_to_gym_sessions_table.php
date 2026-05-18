<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_sessions', function (Blueprint $table) {
            $table->longText('workout_content')->nullable()->after('capacity');
            $table->boolean('is_workout_visible')->default(false)->after('workout_content');
        });
    }

    public function down(): void
    {
        Schema::table('gym_sessions', function (Blueprint $table) {
            $table->dropColumn(['workout_content', 'is_workout_visible']);
        });
    }
};
