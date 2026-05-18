<?php
namespace App\Filament\System\Widgets;
use App\Models\Gym;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestGymsWidget extends BaseWidget
{
    protected static ?int $sort = 3; // Baja al nivel 3
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Últimos Gimnasios Registrados';

    public function table(Table $table): Table
    {
        return $table
            ->query(Gym::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Centro')->weight('bold'),
                Tables\Columns\TextColumn::make('users_count')->counts('users')->label('Usuarios')->badge()->color('success'),
                Tables\Columns\TextColumn::make('created_at')->label('Alta')->dateTime('d M Y'),
            ])
            ->paginated(false);
    }
}
