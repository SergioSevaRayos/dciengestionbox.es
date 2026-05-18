<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\ClassSlotAvailable;
use Illuminate\Support\Facades\Notification as SystemNotification;
use Filament\Notifications\Notification as FilamentNotification;
use Carbon\Carbon;
use App\Models\UserPackage;
use App\Models\BookingLog;

class Booking extends Model
{
    use \App\Traits\BelongsToGym;

    protected $fillable = ['gym_id','user_id', 'gym_session_id', 'status', 'notified_at'];
    protected function casts(): array { return ['notified_at' => 'datetime']; }

    public function user() { return $this->belongsTo(User::class); }
    public function gymSession() { return $this->belongsTo(GymSession::class); }

    protected static function booted()
    {
        static::creating(function ($booking) {
            $user = User::find($booking->user_id);
            if (!$user) return;

            $activePackage = tap(UserPackage::where('user_id', $user->id)
                ->where('expires_at', '>', now())->orderBy('expires_at', 'asc')->first(), function ($package) {
                    if (!$package) return UserPackage::where('user_id', request()->user()->id ?? 0)->latest()->first();
            });

            if (!$activePackage) return;

            if ($activePackage->type === 'bono') {
                if ($activePackage->remaining_credits > 0) {
                    $activePackage->decrement('remaining_credits');
                } else {
                    FilamentNotification::make()->title('Reserva Denegada')->body('No te quedan clases en tu bono.')->danger()->send();
                    return false;
                }
            } else if ($activePackage->type === 'tarifa') {
                $session = GymSession::find($booking->gym_session_id);
                if (!$session || !$session->start_time) return;

                $sessionDate = Carbon::parse($session->start_time);
                $limitAmount = (int) $activePackage->limit_amount;

                if ($limitAmount > 0) {
                    $query = Booking::where('user_id', $user->id)->where('status', 'booked')
                        ->whereHas('gymSession', function ($q) use ($sessionDate, $activePackage) {
                            if ($activePackage->limit_type === 'semanal') {
                                $q->whereBetween('start_time', [$sessionDate->copy()->startOfWeek(), $sessionDate->copy()->endOfWeek()]);
                            } elseif ($activePackage->limit_type === 'mensual') {
                                $q->whereBetween('start_time', [$sessionDate->copy()->startOfMonth(), $sessionDate->copy()->endOfMonth()]);
                            } elseif ($activePackage->limit_type === 'anual') {
                                $q->whereBetween('start_time', [$sessionDate->copy()->startOfYear(), $sessionDate->copy()->endOfYear()]);
                            }
                        });

                    if ($query->count() >= $limitAmount) {
                        FilamentNotification::make()->title('Reserva Denegada')->body("Has alcanzado tu límite {$activePackage->limit_type} de {$limitAmount} clases.")->danger()->send();
                        return false;
                    }
                }
            }
        });

        static::created(function ($booking) {
            BookingLog::create(['gym_id' => $booking->gym_id, 'user_id' => $booking->user_id, 'gym_session_id' => $booking->gym_session_id, 'action' => 'reserva']);
        });

        static::deleting(function ($booking) {
            if (auth()->check() && auth()->user()->role === 'student') {
                $session = $booking->gymSession;
                if ($session && $session->start_time && now()->greaterThanOrEqualTo(Carbon::parse($session->start_time)->subMinutes(60))) {
                    FilamentNotification::make()->title('Cancelación Bloqueada')->body('Falta menos de 1 hora para la clase.')->danger()->send();
                    return false;
                }
            }
        });

        static::deleted(function ($booking) {
            BookingLog::create(['gym_id' => $booking->gym_id, 'user_id' => $booking->user_id, 'gym_session_id' => $booking->gym_session_id, 'action' => 'cancelacion']);
            if ($booking->status === 'booked' && $booking->user) {
                $packageToRefund = UserPackage::where('user_id', $booking->user_id)->where('expires_at', '>', now())->orderBy('expires_at', 'asc')->first();
                if ($packageToRefund && $packageToRefund->type === 'bono') $packageToRefund->increment('remaining_credits');
            }

            $session = $booking->gymSession()->with(['gym', 'classType'])->first();
            if (!$session) return;

            if ($session->bookings()->where('status', 'booked')->count() < $session->capacity) {
                $nextInWaitlist = Booking::where('gym_session_id', $session->id)->where('status', 'waiting')->whereNull('notified_at')->orderBy('created_at', 'asc')->first();
                if ($nextInWaitlist && $nextInWaitlist->user) {
                    SystemNotification::send($nextInWaitlist->user, new ClassSlotAvailable($session));
                    $nextInWaitlist->update(['notified_at' => now()]);
                }
            }
        });
    }
}
