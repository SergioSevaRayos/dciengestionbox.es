<?php
namespace App\Filament\System\Resources\TicketResource\Pages;
use App\Filament\System\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;
    
    protected function getHeaderActions(): array
    {
        // El Super Admin no crea tickets, los recibe. Dejamos esto vacío.
        return [];
    }
}
