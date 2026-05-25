<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create("arena_registrations", function (Blueprint $table) {
            $table->id();
            $table->foreignId("arena_competition_id")->constrained()->cascadeOnDelete();
            $table->string("team_name")->nullable();
            $table->enum("type", ["individual", "pairs", "team"]);
            $table->enum("category", ["MM", "MF", "FF", "IndividualM", "IndividualF"]);
            $table->foreignId("athlete_1_id")->constrained("users");
            $table->foreignId("athlete_2_id")->nullable()->constrained("users");
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists("arena_registrations"); }
};