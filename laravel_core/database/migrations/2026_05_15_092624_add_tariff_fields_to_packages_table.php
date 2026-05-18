<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // 'bono' (por clases) o 'tarifa' (por periodos)
            $table->string('type')->default('bono')->after('name'); 
            
            // 'semanal', 'mensual', 'anual' o null (para bonos)
            $table->string('limit_type')->nullable()->after('type'); 
            
            // El límite numérico (ej: 3 clases a la semana, 0 = ilimitado)
            $table->integer('limit_amount')->nullable()->after('limit_type'); 
        });

        // Hacemos lo mismo en el "sobre" que se le entrega al usuario
        Schema::table('user_packages', function (Blueprint $table) {
            $table->string('type')->default('bono')->after('package_id');
            $table->string('limit_type')->nullable()->after('type');
            $table->integer('limit_amount')->nullable()->after('limit_type');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['type', 'limit_type', 'limit_amount']);
        });
        
        Schema::table('user_packages', function (Blueprint $table) {
            $table->dropColumn(['type', 'limit_type', 'limit_amount']);
        });
    }
};
