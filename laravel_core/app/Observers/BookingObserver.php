<?php
namespace App\Observers;
use App\Models\Booking;
use App\Models\BookingLog;

class BookingObserver {
    public function created(Booking $booking) {
        $action = $booking->status === 'waiting' ? 'Lista de Espera' : 'Reserva';
        BookingLog::create([
            'gym_id' => $booking->gym_id,
            'user_id' => $booking->user_id,
            'gym_session_id' => $booking->gym_session_id,
            'action' => $action
        ]);
    }
    public function deleted(Booking $booking) {
        BookingLog::create([
            'gym_id' => $booking->gym_id,
            'user_id' => $booking->user_id,
            'gym_session_id' => $booking->gym_session_id,
            'action' => 'Cancelación'
        ]);
    }
}
