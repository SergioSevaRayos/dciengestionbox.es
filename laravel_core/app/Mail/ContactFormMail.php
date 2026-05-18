<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            // Dejamos que Laravel use el MAIL_FROM_ADDRESS del .env automáticamente
            replyTo: [
                new Address($this->data['email'], $this->data['name']),
            ],
            subject: '📩 Nuevo mensaje de contacto: ' . $this->data['name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form', // Asegúrate de que esta vista existe
        );
    }
}
