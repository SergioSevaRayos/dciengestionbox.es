<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Models\Gym;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->brandLogo(asset('logo-training-nav.png'))->brandLogoHeight('5.5rem')
            ->favicon(asset('logo-app.png'))
            ->favicon(asset('favicon.png?v=2'))
            ->path('app')
            ->login()
            ->passwordReset()
            ->profile(\App\Filament\App\Pages\Auth\EditProfile::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->tenant(Gym::class, ownershipRelationship: 'gyms', slugAttribute: 'slug')
            ->tenantMiddleware([
                \App\Http\Middleware\ApplyGymBranding::class,
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => view('filament.components.pwa-login-banner')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render(<<<'BLADE'
<link rel="manifest" href="{{ filament()->getTenant() ? '/manifest/' . filament()->getTenant()->slug . '.json' : '/manifest-user.json' }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ filament()->getTenant() ? filament()->getTenant()->name : 'DCIEN' }}">
<link rel="apple-touch-icon" href="{{ filament()->getTenant() && filament()->getTenant()->logo ? asset('storage/' . filament()->getTenant()->logo) : '/logo-app.png' }}">
<meta name="theme-color" content="#111827">
<script>if ("serviceWorker" in navigator) { window.addEventListener("load", () => { navigator.serviceWorker.register("/sw.js"); }); }</script>
BLADE
                )
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
