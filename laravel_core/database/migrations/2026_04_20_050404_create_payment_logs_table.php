<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("payment_logs", function (Blueprint $table) {
            $table->id();
            $table->foreignId("gym_id")->constrained()->cascadeOnDelete();
            $table->string("bono_name");
            $table->string("client_name");
            $table->timestamp("activated_at")->nullable();
            $table->string("activated_by")->nullable();
            $table->timestamp("paid_at")->nullable();
            $table->string("paid_by")->nullable();
            $table->text("observations")->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists("payment_logs"); }
};