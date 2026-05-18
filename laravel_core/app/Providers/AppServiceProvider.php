<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forzamos HTTPS en producción para evitar el error 403 en Hostinger
        if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // 🛡️ ESCUDO UX TOTAL: Captura Errores 500, Sesiones Caducadas y Cortes de Red (Estado 0)
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => Blade::render('
                <style>
                    /* Aniquilamos cualquier cartel oscuro nativo de offline */
                    [wire\:offline] { display: none !important; }
                    .fi-offline-indicator { display: none !important; }
                </style>
                <script>
                    document.addEventListener("livewire:init", () => {
                        
                        // Capturamos cualquier intento de petición fallida al servidor
                        Livewire.hook("request", ({ fail }) => {
                            fail(({ status, preventDefault }) => {
                                
                                // CASO 1: Sin internet al pulsar un botón (Status 0)
                                if (status === 0) {
                                    preventDefault();
                                    new FilamentNotification()
                                        .title("Acción cancelada")
                                        .body("No tienes conexión a internet. Revisa tu red y vuelve a intentarlo.")
                                        .warning()
                                        .duration(5000)
                                        .send();
                                }
                                // CASO 2: Error 500 (Fallo interno del servidor)
                                else if (status >= 500) {
                                    preventDefault();
                                    new FilamentNotification()
                                        .title("Error del Servidor")
                                        .body("Se ha producido un error técnico. El equipo ha sido notificado.")
                                        .danger()
                                        .duration(8000)
                                        .send();
                                }
                                // CASO 3: Error 419 (Token CSRF caducado por dejar la web abierta mucho tiempo)
                                else if (status === 419) {
                                    preventDefault();
                                    new FilamentNotification()
                                        .title("Sesión caducada")
                                        .body("Tu sesión ha estado inactiva. Por favor, recarga la página.")
                                        .warning()
                                        .duration(8000)
                                        .send();
                                }
                            });
                        });

                        // Notificación pasiva cuando el navegador pierde o recupera la red
                        window.addEventListener("offline", () => {
                            new FilamentNotification()
                                .title("Te has desconectado")
                                .body("Has perdido la señal de internet.")
                                .warning()
                                .send();
                        });

                        window.addEventListener("online", () => {
                            new FilamentNotification()
                                .title("Conexión Restaurada")
                                .body("Vuelves a estar en línea.")
                                .success()
                                .send();
                        });
                    });
                </script>
            ')
        );
    }
}
