<?php

namespace App\Filament\Resources\PaymentLogResource\Pages;

use App\Filament\Resources\PaymentLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentLogs extends ListRecords
{
    protected static string $resource = PaymentLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Lo dejamos vacío intencionadamente para que no haya botón de "Nuevo Pago" manual.
            // Los pagos se registrarán solos al pulsar "Activar" o "Cobrar".
        ];
    }
}
