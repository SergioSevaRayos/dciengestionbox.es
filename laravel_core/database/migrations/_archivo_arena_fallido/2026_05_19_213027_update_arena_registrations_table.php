<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table("arena_registrations", function (Blueprint $table) {
            $table->string("team_name")->nullable()->change();
            $table->foreignId("athlete_3_id")->nullable()->constrained("users");
            $table->foreignId("athlete_4_id")->nullable()->constrained("users");
        });
    }
    public function down(): void {
        Schema::table("arena_registrations", function (Blueprint $table) {
            $table->dropColumn(["athlete_3_id", "athlete_4_id"]);
        });
    }
};