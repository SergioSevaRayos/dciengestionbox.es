<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\GymSession;
use Carbon\Carbon;

class SpotAvailableNotification extends Notification
{
    use Queueable;

    public $gymSession;

    public function __construct(GymSession $gymSession)
    {
        $this->gymSession = $gymSession;
    }

    public function via(object $notifiable): array
    {
        return ['mail']; // Indicamos que es solo por correo electrónico
    }

    public function toMail(object $notifiable): MailMessage
    {
        $className = $this->gymSession->classType->name;
        $time = Carbon::parse($this->gymSession->start_time)->format('d/m/Y H:i');

        return (new MailMessage)
            ->subject('¡Tienes plaza en ' . $className . '!')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se ha liberado un hueco en la clase de **' . $className . '** del ' . $time . '.')
            ->line('Tienes exactamente 5 minutos para entrar en la App y confirmar tu asistencia antes de que la plaza pase al siguiente en la lista de espera.')
            ->action('Confirmar mi plaza', url('/app'))
            ->line('¡Date prisa, que vuelan!')
            ->salutation('El equipo de DCIEN');
    }
}
