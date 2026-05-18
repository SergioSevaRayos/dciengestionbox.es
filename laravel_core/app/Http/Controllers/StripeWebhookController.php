<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Gym;
use Stripe\Webhook;
use Stripe\Stripe;
use Stripe\Customer;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        try {
            // Verificamos que la llamada viene 100% de Stripe
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch(\UnexpectedValueException $e) {
            return response('Payload inválido', 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            return response('Firma inválida', 400);
        }

        // ¿Qué ha pasado con la suscripción?
        switch ($event->type) {
            case 'customer.subscription.deleted': // El cliente canceló o se agotaron los reintentos
            case 'invoice.payment_failed':        // La tarjeta dio error
                $this->bloquearGimnasio($event->data->object);
                break;
            case 'invoice.payment_succeeded':     // Cobro mensual exitoso
                $this->desbloquearGimnasio($event->data->object);
                break;
        }

        return response('Webhook recibido', 200);
    }

    protected function bloquearGimnasio($stripeObject)
    {
        $customerId = $stripeObject->customer;
        if($customerId) {
            try {
                // Buscamos a quién pertenece este ID en Stripe
                $customer = Customer::retrieve($customerId);
                if(isset($customer->metadata->gym_id)) {
                    $gym = Gym::find($customer->metadata->gym_id);
                    if($gym && $gym->is_subscribed) {
                        $gym->update(['is_subscribed' => false]);
                        Log::info("🔴 SUSCRIPCIÓN BLOQUEADA por impago - Gym ID: " . $gym->id);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error en Webhook Stripe: " . $e->getMessage());
            }
        }
    }

    protected function desbloquearGimnasio($stripeObject)
    {
        $customerId = $stripeObject->customer;
        if($customerId) {
            try {
                $customer = Customer::retrieve($customerId);
                if(isset($customer->metadata->gym_id)) {
                    $gym = Gym::find($customer->metadata->gym_id);
                    if($gym && !$gym->is_subscribed) {
                        $gym->update(['is_subscribed' => true]);
                        Log::info("🟢 SUSCRIPCIÓN RENOVADA con éxito - Gym ID: " . $gym->id);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error en Webhook Stripe: " . $e->getMessage());
            }
        }
    }
}
