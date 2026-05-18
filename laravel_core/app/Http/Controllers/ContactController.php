<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        try {
            Mail::to('contacto@d-cien.es')->send(new ContactFormMail($validated));
            return back()->with('success', '¡Mensaje enviado con éxito! Nos pondremos en contacto pronto.');
        } catch (\Exception $e) {
            Log::error("Error enviando formulario de contacto: " . $e->getMessage());
            return back()->withErrors(['error' => 'No se pudo enviar el mensaje. Inténtalo más tarde.']);
        }
    }
}
