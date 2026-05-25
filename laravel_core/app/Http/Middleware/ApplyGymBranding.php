<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Colors\Color;
use App\Models\GymSetting;
use Illuminate\Support\Facades\File;

class ApplyGymBranding
{
    public function handle(Request $request, Closure $next)
    {
        // Obtenemos el gimnasio al que acabas de entrar
        $gym = Filament::getTenant();

        if ($gym) {
            $settings = GymSetting::where('gym_id', $gym->id)->first();

            if ($settings) {
                // 1. Aplicamos el color en tiempo real
                if ($settings->primary_color) {
                    FilamentColor::register([
                        'primary' => Color::hex($settings->primary_color),
                    ]);
                }

                // 2. Aplicamos el Logo y Favicon al panel actual
                $panel = Filament::getCurrentPanel();
                if ($panel) {
                    if ($settings->logo) {
                        $panel->brandLogo(asset('storage/' . $settings->logo));
                        $panel->brandLogoHeight('2.5rem');
                    }

                    if ($settings->favicon) {
                        $panel->favicon(asset('storage/' . $settings->favicon));
                    }
                }
            }
        }

                return $next($request);
    }
}
