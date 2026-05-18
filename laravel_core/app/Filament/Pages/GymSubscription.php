<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Checkout\Session;

class GymSubscription extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $title = 'Suscripción y Pagos';
    protected static ?int $navigationSort = 100;
    protected static string $view = 'filament.pages.gym-subscription';

    public $tenant;

    public function mount()
    {
        $this->tenant = Filament::getTenant();

        // 1. Atrapamos el éxito
        if (request()->query('success') === 'true') {
            $this->tenant->update(['is_subscribed' => true]);

            Notification::make()
                ->title('¡Suscripción activada con éxito!')
                ->body('Tu gimnasio ya tiene acceso total a la plataforma. ¡A darle caña!')
                ->success()
                ->send();

            return redirect()->to(static::getUrl());
        }

        // 2. Atrapamos la cancelación
        if (request()->query('canceled') === 'true') {
            Notification::make()
                ->title('Proceso cancelado')
                ->body('No se ha realizado ningún cargo en tu tarjeta.')
                ->warning()
                ->send();

            return redirect()->to(static::getUrl());
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pay')
                ->label('Activar Suscripción (10€/mes)')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->visible(fn () => !$this->tenant->is_subscribed)
                ->action(function () {
                    Stripe::setApiKey(env('STRIPE_SECRET'));

                    $user = auth()->user();
                    $customers = Customer::all(['email' => $user->email, 'limit' => 1]);

                    if (count($customers->data) > 0) {
                        $stripeCustomerId = $customers->data[0]->id;
                    } else {
                        $customer = Customer::create([
                            'name' => $user->name,
                            'email' => $user->email,
                            'metadata' => [
                                'gym_id' => $this->tenant->id,
                                'gym_name' => $this->tenant->name,
                            ]
                        ]);
                        $stripeCustomerId = $customer->id;
                    }

                    $session = Session::create([
                        'customer' => $stripeCustomerId,
                        'payment_method_types' => ['card'],
                        'line_items' => [[
                            'price' => 'price_1TNzNQArDT71kBB9RLU8lqnB',
                            'quantity' => 1,
                        ]],
                        'mode' => 'subscription',
                        'discounts' => [['coupon' => 'JPCpmuFV']],
                        'subscription_data' => ['trial_period_days' => 30],
                        'success_url' => static::getUrl(['success' => 'true']),
                        'cancel_url' => static::getUrl(['canceled' => 'true']),
                        'client_reference_id' => $this->tenant->id,
                    ]);

                    return redirect()->away($session->url);
                }),

            // AQUÍ ESTÁ LA MAGIA DEL PORTAL DE STRIPE
            Action::make('manage_subscription')
                ->label('Gestionar Suscripción / Cancelar')
                ->icon('heroicon-o-cog-8-tooth')
                ->color('warning')
                ->visible(fn () => $this->tenant->is_subscribed)
                ->action(function () {
                    Stripe::setApiKey(env('STRIPE_SECRET'));
                    
                    $user = auth()->user();
                    // Buscamos al cliente en Stripe para poder abrirle su portal
                    $customers = Customer::all(['email' => $user->email, 'limit' => 1]);

                    if (count($customers->data) > 0) {
                        $stripeCustomerId = $customers->data[0]->id;
                        
                        // Creamos la sesión del Portal del Cliente
                        $portalSession = \Stripe\BillingPortal\Session::create([
                            'customer' => $stripeCustomerId,
                            'return_url' => static::getUrl(), // Para que vuelva a tu app al terminar
                        ]);

                        return redirect()->away($portalSession->url);
                    } else {
                        Notification::make()
                            ->title('Error')
                            ->body('No se encontró tu cuenta en Stripe. Contacta con soporte.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
