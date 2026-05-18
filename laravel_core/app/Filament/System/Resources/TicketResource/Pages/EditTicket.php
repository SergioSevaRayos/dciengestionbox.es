<?php
namespace App\Filament\System\Resources\TicketResource\Pages;
use App\Filament\System\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Borrar Ticket'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        // Al guardar la respuesta, te devolvemos a la lista
        return $this->getResource()::getUrl('index');
    }
}
