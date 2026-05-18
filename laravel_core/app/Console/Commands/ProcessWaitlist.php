<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Notifications\ClassSlotAvailable;
use Illuminate\Support\Facades\Notification;

class ProcessWaitlist extends Command
{
    // El nombre del comando que usaremos en la terminal
    protected $signature = 'gym:process-waitlist';
    protected $description = 'Pasa el turno al siguiente si han pasado 5 minutos desde el aviso.';

    public function handle()
    {
        // 1. Buscamos a los que se les avisó hace 5 minutos o más y siguen en waiting
        $expiredBookings = Booking::with('gymSession')
            ->where('status', 'waiting')
            ->whereNotNull('notified_at')
            ->where('notified_at', '<=', now()->subMinutes(5))
            ->get();

        $count = 0;

        foreach ($expiredBookings as $expired) {
            // 2. Le quitamos el turno cambiando su estado
            $expired->update(['status' => 'expired_waitlist']);

            // 3. Buscamos al siguiente de la cola para ESA misma clase
            $session = $expired->gymSession;
            if ($session) {
                $nextInWaitlist = Booking::where('gym_session_id', $session->id)
                    ->where('status', 'waiting')
                    ->whereNull('notified_at')
                    ->orderBy('created_at', 'asc')
                    ->first();

                // 4. Si hay alguien, le disparamos el email
                if ($nextInWaitlist && $nextInWaitlist->user) {
                    Notification::send($nextInWaitlist->user, new ClassSlotAvailable($session));
                    $nextInWaitlist->update(['notified_at' => now()]);
                }
            }
            $count++;
        }

        $this->info("Se han procesado $count turnos caducados.");
    }
}
