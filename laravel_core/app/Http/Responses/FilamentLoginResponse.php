<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class FilamentLoginResponse implements LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();

        // Si es el dueño del gimnasio o un administrador, al panel de gestión
        if (in_array($user->role, ['gestor', 'admin'])) {
            return redirect()->to('/admin');
        }

        // Si es un cliente/alumno, a la app de reservas
        return redirect()->to('/app');
    }
}
