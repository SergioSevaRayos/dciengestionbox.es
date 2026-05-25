<?php
namespace App\Filament\Resources\ArenaCompetitionResource\Pages;
use App\Filament\Resources\ArenaCompetitionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArenaCompetition extends CreateRecord
{
    protected static string $resource = ArenaCompetitionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['gym_id'] = \Filament\Facades\Filament::getTenant()->id;
        return $data;
    }
}
