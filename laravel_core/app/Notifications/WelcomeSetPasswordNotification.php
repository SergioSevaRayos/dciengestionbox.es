<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Facades\Filament;

class WelcomeSetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // 1. Obtenemos el panel del alumno ('app')
        $panel = Filament::getPanel('app');

        // 2. Le pedimos al panel que genere SU enlace oficial de reset de contraseña
        $url = $panel->getResetPasswordUrl($this->token, $notifiable);

        return (new MailMessage)
            ->subject('¡Bienvenido! Establece tu contraseña')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Te damos la bienvenida a tu nuevo panel de reservas del gimnasio.')
            ->line('Para poder acceder y empezar a reservar tus clases, por favor establece tu contraseña haciendo clic en el siguiente botón:')
            ->action('Establecer mi Contraseña', $url)
            ->line('Este enlace es seguro y caducará en 60 minutos.')
            ->line('Si tienes alguna duda, consúltanos en tu próxima visita.')
            ->salutation('El equipo del Gimnasio');
    }
}
