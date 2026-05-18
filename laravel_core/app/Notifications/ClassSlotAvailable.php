<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ClassSlotAvailable extends Notification
{
    use Queueable;

    protected $scheduledClass;

    public function __construct($scheduledClass)
    {
        $this->scheduledClass = $scheduledClass;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Ahora sí usamos la relación correcta (classType)
        $className = optional($this->scheduledClass->classType)->name ?? 'tu clase';
        $gymName = optional($this->scheduledClass->gym)->name ?? 'tu gimnasio';
        $url = url('/app'); 

        return (new MailMessage)
            ->subject('¡Hueco disponible en ' . $className . '!')
            ->greeting('Hola ' . $notifiable->name . '!')
            ->line('Se ha liberado una plaza en la clase que te interesaba:')
            ->line('📅 Fecha: ' . optional($this->scheduledClass->start_time)->format('d/m/Y'))
            ->line('⏰ Hora: ' . optional($this->scheduledClass->start_time)->format('H:i'))
            ->action('Reservar mi plaza ahora', $url)
            ->line('¡Date prisa, el primero que confirme se queda el hueco!')
            ->salutation('Saludos, el equipo de ' . $gymName);
    }
}
