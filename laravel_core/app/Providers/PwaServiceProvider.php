<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

class PwaServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Inyectamos el Manifest en el <head>
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): string => '<link rel="manifest" href="/manifest.json"><meta name="theme-color" content="#f59e0b">'
        );

        // Activamos el Service Worker al cargar la página
        FilamentView::registerRenderHook(
            'panels::scripts.after',
            fn (): string => "<script>if ('serviceWorker' in navigator) { window.addEventListener('load', function() { navigator.serviceWorker.register('/sw.js'); }); }</script>"
        );
    }
}
