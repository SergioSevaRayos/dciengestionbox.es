<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Usamos una consulta cruda para asegurar la compatibilidad con MySQL/MariaDB
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('booked', 'cancelled', 'waiting') NOT NULL DEFAULT 'booked'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('booked', 'cancelled') NOT NULL DEFAULT 'booked'");
    }
};
