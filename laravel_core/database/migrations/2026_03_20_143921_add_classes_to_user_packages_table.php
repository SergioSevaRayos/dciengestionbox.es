<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('user_packages', 'total_classes')) {
                $table->integer('total_classes')->default(0);
            }
            if (!Schema::hasColumn('user_packages', 'remaining_classes')) {
                $table->integer('remaining_classes')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_packages', function (Blueprint $table) {
            $table->dropColumn(['total_classes', 'remaining_classes']);
        });
    }
};
