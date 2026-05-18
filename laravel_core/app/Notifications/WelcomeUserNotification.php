<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUserNotification extends Notification
{
    use Queueable;

    protected $gymName;
    protected $password;

    public function __construct($gymName, $password)
    {
        $this->gymName = $gymName;
        $this->password = $password;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 ¡Bienvenido a ' . $this->gymName . '!')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Te damos la bienvenida oficial a nuestra plataforma de reservas.')
            ->line('Tu cuenta ha sido creada con éxito por el gestor del box. A continuación, tienes tus datos de acceso provisionales:')
            ->line('📧 **Email:** ' . $notifiable->email)
            ->line('🔑 **Contraseña:** ' . $this->password)
            ->action('Acceder a la App', url('/app'))
            ->line('💡 **Recomendación:** Inicia sesión con esta contraseña temporal y dirígete a la sección "Perfil" (arriba a la derecha) para cambiarla por una más segura.')
            ->salutation('¡Nos vemos en el próximo WOD!');
    }
}
