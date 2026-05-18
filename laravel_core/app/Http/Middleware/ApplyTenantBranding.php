<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use App\Models\GymSetting;
use Illuminate\Support\Facades\Storage;
use Filament\Support\Colors\Color;

class ApplyTenantBranding
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Intentamos sacar el gimnasio de la URL (Panel del Gestor)
        $tenant = Filament::getTenant();

        // 2. Si no hay tenant en la URL (Panel de Alumno), miramos el usuario logueado
        if (!$tenant && auth()->check()) {
            $tenant = auth()->user()->gyms->first();
        }

        // 3. Buscamos la configuración en la base de datos
        if ($tenant) {
            $settings = GymSetting::where('gym_id', $tenant->id)->first();
        } else {
            // 4. Pantalla de Login del alumno (sin usuario). Cogemos el diseño principal.
            $settings = GymSetting::first();
        }

        if ($settings) {
            $panel = Filament::getCurrentPanel();
            
            if ($panel) {
                if ($settings->primary_color) {
                    $panel->colors([
                        'primary' => Color::hex($settings->primary_color),
                    ]);
                }

                if ($settings->logo) {
                    $panel->brandLogo(Storage::disk('public')->url($settings->logo));
                    $panel->brandLogoHeight('2.5rem'); 
                }

                if ($settings->favicon) {
                    $panel->favicon(Storage::disk('public')->url($settings->favicon));
                }
                
                if ($settings->gym_name) {
                    $panel->brandName($settings->gym_name);
                }
            }
        }

        return $next($request);
    }
}
