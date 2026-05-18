<?php
namespace App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Asignamos el usuario actual automáticamente
        $data['user_id'] = auth()->id();
        
        // 2. Asignamos el gimnasio actual (Tenant) automáticamente
        $data['gym_id'] = filament()->getTenant()->id;
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // Al enviar el ticket, los devolvemos a la tabla en lugar de quedarse en el formulario
        return $this->getResource()::getUrl('index');
    }
}
