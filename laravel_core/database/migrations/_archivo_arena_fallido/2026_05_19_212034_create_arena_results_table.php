<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create("arena_results", function (Blueprint $table) {
            $table->id();
            $table->foreignId("arena_registration_id")->constrained()->cascadeOnDelete();
            $table->foreignId("arena_wod_id")->constrained()->cascadeOnDelete();
            $table->string("result_value"); // Aquí guardaremos "10:20" o "150"
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists("arena_results"); }
};