<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('gym_settings', function (Blueprint $table) {
            $table->boolean('enable_arena')->default(false)->after('gym_id');
            $table->json('arena_config')->nullable()->after('enable_arena');
        });
    }
    public function down(): void {
        Schema::table('gym_settings', function (Blueprint $table) {
            $table->dropColumn(['enable_arena', 'arena_config']);
        });
    }
};