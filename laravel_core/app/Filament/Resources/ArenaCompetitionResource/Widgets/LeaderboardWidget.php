<?php
namespace App\Filament\Resources\ArenaCompetitionResource\Widgets;

use App\Models\ArenaCompetition;
use App\Models\ArenaRegistration;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LeaderboardWidget extends BaseWidget
{
    public ?int $recordId = null;

    public function table(Table $table): Table
    {
        // Usamos un Builder real de Eloquent, que es lo que Filament espera
        $query = ArenaRegistration::query()->where('arena_competition_id', $this->recordId ?? 0);

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('team_name')
                    ->label('Atleta / Equipo')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total_points')
                    ->label('Puntos Totales')
                    ->getStateUsing(function (ArenaRegistration $record) {
                        $competition = ArenaCompetition::find($this->recordId);
                        if (!$competition) return 0;
                        
                        $standings = $competition->getStandings();
                        $standing = $standings->firstWhere('id', $record->id);
                        return $standing ? $standing->total_points : 0;
                    })
                    ->extraAttributes(['class' => 'font-bold']),
            ]);
    }
}
