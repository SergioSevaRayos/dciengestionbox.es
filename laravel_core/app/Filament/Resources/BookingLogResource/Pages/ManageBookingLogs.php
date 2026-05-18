<?php

namespace App\Filament\Resources\BookingLogResource\Pages;

use App\Filament\Resources\BookingLogResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\Action;
use App\Models\BookingLog;
use Filament\Notifications\Notification;

class ManageBookingLogs extends ManageRecords
{
    protected static string $resource = BookingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('vaciarHistorial')
                ->label('Vaciar Historial')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('¿Eliminar todos los registros?')
                ->modalDescription('Esta acción borrará de forma permanente todo el historial de auditoría de reservas actual. Esta acción no se puede deshacer. ¿Estás seguro?')
                ->modalSubmitActionLabel('Sí, borrar todo')
                ->action(function () {
                    // Usamos query()->delete() para que respete el filtro del gimnasio actual
                    BookingLog::query()->delete();
                    
                    Notification::make()
                        ->title('Historial limpiado correctamente')
                        ->success()
                        ->send();
                }),
        ];
    }
}
