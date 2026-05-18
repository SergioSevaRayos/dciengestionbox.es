<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $blueprint) {
            // Añadimos la categoría después del nombre
            $blueprint->string('category')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $blueprint) {
            $blueprint->dropColumn('category');
        });
    }
};
