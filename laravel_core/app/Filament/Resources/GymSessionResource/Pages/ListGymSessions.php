<?php
namespace App\Filament\Resources\GymSessionResource\Pages;

use App\Filament\Resources\GymSessionResource;
use App\Filament\Widgets\GymCalendarWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGymSessions extends ListRecords
{
    protected static string $resource = GymSessionResource::class;

    // 1. EL BOTÓN PARA CREAR CLASES
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Nueva Clase')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    // 2. ¡AQUÍ INYECTAMOS EL CALENDARIO VISUAL!
    protected function getHeaderWidgets(): array
    {
        return [
            GymCalendarWidget::class,
        ];
    }
}
