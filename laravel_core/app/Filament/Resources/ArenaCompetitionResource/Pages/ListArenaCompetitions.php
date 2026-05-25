<?php
namespace App\Filament\Resources\ArenaCompetitionResource\Pages;
use App\Filament\Resources\ArenaCompetitionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArenaCompetitions extends ListRecords
{
    protected static string $resource = ArenaCompetitionResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Nueva Competición')];
    }
}
