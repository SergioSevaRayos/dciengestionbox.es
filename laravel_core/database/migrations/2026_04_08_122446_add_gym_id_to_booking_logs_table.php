<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('booking_logs', function (Blueprint $table) {
            $table->foreignId('gym_id')->nullable()->after('id')->constrained('gyms')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::table('booking_logs', function (Blueprint $table) {
            $table->dropForeign(['gym_id']);
            $table->dropColumn('gym_id');
        });
    }
};
