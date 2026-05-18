<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_sessions', function (Blueprint $table) {
            $table->index(['gym_id', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::table('gym_sessions', function (Blueprint $table) {
            $table->dropIndex(['gym_id', 'start_time']);
        });
    }
};
