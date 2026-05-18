<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class PackageExpiringNotification extends Notification
{
    use Queueable;

    protected $package;

    public function __construct($package)
    {
        $this->package = $package;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $credits = $this->package->remaining_credits;
        $isAgotado = $credits <= 0;

        $mail = (new MailMessage)
            ->subject($isAgotado ? '🚨 Tu bono se ha agotado - dCien Gestión Box' : '⚠️ Tu bono está cerca de caducar - dCien Gestión Box')
            ->greeting('¡Hola ' . $notifiable->name . '!');

        if ($isAgotado) {
            $mail->line('Te informamos que has agotado todas las clases de tu bono actual.')
                 ->line('¡Gran trabajo en los entrenamientos! Para seguir reservando y no perder el ritmo, necesitas solicitar tu renovación.');
        } else {
            $mail->line('Te informamos que tu bono actual caducará pronto.')
                 ->line('📅 Fecha de caducidad: ' . Carbon::parse($this->package->expires_at)->format('d/m/Y'))
                 ->line('Aún te quedan ' . $credits . ' créditos por disfrutar.');
        }

        // AQUÍ ESTÁ LA URL CORREGIDA HACIA LA APP
        return $mail->action('Renovar bono ahora', url('/app/dcien'))
            ->line('No esperes al último momento para asegurar tus plazas en el box.')
            ->salutation('¡Nos vemos entrenando!');
    }
}
