<?php

namespace App\Providers\Filament;

use App\Models\Gym;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->brandLogo(asset('logo-training-nav.png'))->brandLogoHeight('4rem')
            ->favicon(asset('logo-app.png'))
            ->favicon(asset('favicon.png?v=2'))
            ->authGuard('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile()
            ->registration(\App\Filament\Pages\Auth\RegisterGymOwner::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugin(\Saade\FilamentFullCalendar\FilamentFullCalendarPlugin::make())
            ->tenant(Gym::class, slugAttribute: 'slug')
            ->tenantMenu(false)
            ->tenantMiddleware([
                \App\Http\Middleware\ApplyGymBranding::class,
            ])
            ->navigationGroups([
                'Gestión Deportiva',
                'Entrenamiento',
                'Gestión De Usuarios',
                'Ventas y Facturación',
                'Configuración',
            ])
            ->renderHook('panels::head.end', fn (): string => '<style>@media(max-width:768px){.fc .fc-header-toolbar{flex-direction:column !important;gap:15px !important}.fc .fc-toolbar-chunk{display:flex !important;justify-content:center !important;width:100% !important;flex-wrap:wrap !important;gap:5px !important}.fc-toolbar-title{font-size:1.2rem !important;text-align:center !important}}</style>')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render("<x-cookie-banner />")
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): string => view('filament.footer.contact')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => view('filament.components.pwa-install-button')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render('<link rel="manifest" href="/manifest-admin.json">
<meta name="theme-color" content="#111827">
<script>if ("serviceWorker" in navigator) { window.addEventListener("load", () => { navigator.serviceWorker.register("/sw.js"); }); }</script>')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => view('filament.components.pwa-login-banner')
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
