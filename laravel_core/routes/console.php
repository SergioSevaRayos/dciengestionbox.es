<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\PackageExpiringNotification;
use Carbon\Carbon;

Artisan::command('packages:expire', function () {

    // 1. BUSCAR A QUIÉN AVISAR
    $packagesToNotify = DB::table('user_packages')
        ->where('expiration_notified', false)
        ->where(function($query) {
            $query->where(function($q) {
                $q->whereDate('expires_at', Carbon::now()->addDays(3)->toDateString())
                  ->where('remaining_credits', '>', 0);
            })
            ->orWhere('remaining_credits', '<=', 0);
        })
        ->get();

    foreach ($packagesToNotify as $package) {
        $user = User::find($package->user_id);
        if ($user) {
            try {
                // Intentamos enviar el correo
                $user->notify(new PackageExpiringNotification($package));
                $this->info("📧 Aviso enviado a: {$user->email}");
                
                // Si se envía bien, lo marcamos para no repetir
                DB::table('user_packages')
                    ->where('id', $package->id)
                    ->update(['expiration_notified' => true]);
                    
            } catch (\Exception $e) {
                // SI HOSTINGER FALLA, EL CÓDIGO NO SE ROMPE. Solo avisa y sigue.
                $this->error("❌ Error enviando email a {$user->email}: " . $e->getMessage());
            }
        }
    }

    // 2. CADUCAR LOS YA VENCIDOS (A esto ahora llegará siempre, aunque fallen los correos)
    $expiredPackages = DB::table('user_packages')
        ->where('expires_at', '<', now())
        ->where('remaining_credits', '>', 0)
        ->get();

    foreach ($expiredPackages as $package) {
        DB::transaction(function () use ($package) {
            $user = User::find($package->user_id);
            if ($user) {
                $user->decrement('credits', $package->remaining_credits);
                if ($user->credits < 0) $user->update(['credits' => 0]);
            }
            DB::table('user_packages')->where('id', $package->id)->update(['remaining_credits' => 0]);
        });
    }

    $this->info("✅ Proceso completado.");
});

Schedule::command('gym:process-waitlist')->everyMinute();
Schedule::command('packages:expire')->everyMinute();


// Ejecuta la limpieza de modelos "prunables" todos los días a las 03:00 AM
Schedule::command('model:prune')->dailyAt('03:00');

Schedule::command('payments:clear-old')->daily();
