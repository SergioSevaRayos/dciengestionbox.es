<?php
use App\Models\User;
use App\Models\GymSession;
use App\Models\Booking;
use App\Models\ClassType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// 1. Limpiamos las notificaciones antiguas
DB::table('notifications')->truncate();

// 2. Aseguramos la clase y limpiamos reservas
$tipo = ClassType::firstOrCreate(['name' => 'Prueba Definitiva']);
$hoy20h = Carbon::today()->setTime(20, 0, 0);

$clase = GymSession::updateOrCreate(
    ['start_time' => $hoy20h],
    ['end_time' => $hoy20h->copy()->addHour(), 'capacity' => 2, 'class_type_id' => $tipo->id]
);
$clase->bookings()->delete();

// 3. Apuntamos a 2 Titulares
for ($i = 1; $i <= 2; $i++) {
    $u = User::updateOrCreate(
        ['email' => "titular$i@gym.com"], 
        ['name' => "Titular $i", 'password' => Hash::make('password'), 'role' => 'student', 'credits' => 10]
    );
    Booking::create(['user_id' => $u->id, 'gym_session_id' => $clase->id, 'status' => 'booked']);
}

// 4. Apuntamos a Paco en Espera
$espera = User::updateOrCreate(
    ['email' => "espera@gym.com"], 
    ['name' => "Paco Espera", 'password' => Hash::make('12345678'), 'role' => 'student', 'credits' => 10]
);
Booking::create(['user_id' => $espera->id, 'gym_session_id' => $clase->id, 'status' => 'waiting']);

echo "\n✔️ Escenario 100% limpio y listo para el flujo final.\n";
