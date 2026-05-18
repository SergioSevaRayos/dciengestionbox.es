<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Crear tabla de gimnasios
        Schema::create('gyms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('stripe_id')->nullable(); // Para el futuro pago mensual
            $table->timestamps();
        });

        // 2. Crear tabla intermedia (Qué usuarios tienen acceso a qué gimnasios)
        Schema::create('gym_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Añadir gym_id a TODAS las tablas operativas
        $tables = [
            'packages', 'class_types', 'gym_schedules', 'gym_sessions', 
            'bookings', 'exercises', 'personal_records', 'user_packages', 
            'package_renewals', 'gym_settings'
        ];

        foreach($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    // Lo hacemos nullable temporalmente para no romper los datos existentes
                    $table->foreignId('gym_id')->nullable()->constrained()->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void {
        $tables = [
            'packages', 'class_types', 'gym_schedules', 'gym_sessions', 
            'bookings', 'exercises', 'personal_records', 'user_packages', 
            'package_renewals', 'gym_settings'
        ];

        foreach($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'gym_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['gym_id']);
                    $table->dropColumn('gym_id');
                });
            }
        }
        Schema::dropIfExists('gym_user');
        Schema::dropIfExists('gyms');
    }
};
