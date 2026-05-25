<?php
namespace App\Filament\Resources\ArenaCompetitionResource\Pages;
use App\Filament\Resources\ArenaCompetitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArenaCompetition extends EditRecord
{
    protected static string $resource = ArenaCompetitionResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('gestionar')
                ->label('Gestionar competición')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(fn() => ArenaCompetitionResource::getUrl('manage', ['record' => $this->record])),
            Actions\DeleteAction::make(),
        ];
    }
}
