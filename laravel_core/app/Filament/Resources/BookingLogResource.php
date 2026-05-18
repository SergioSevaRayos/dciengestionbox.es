<?php
namespace App\Filament\Resources;
use App\Filament\Resources\BookingLogResource\Pages;
use App\Models\BookingLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class BookingLogResource extends Resource
{
    protected static ?string $model = BookingLog::class;
    protected static ?string $tenantOwnershipRelationshipName = 'gym';
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?string $navigationGroup = 'Gestión De Usuarios';
    protected static ?string $navigationLabel = 'Auditoría Reservas';
    protected static ?string $pluralModelLabel = 'Auditoría de Reservas';

    public static function canCreate(): bool { return false; } 

    public static function table(Table $table): Table
    {
        return $table->poll('5s')
            ->columns([
                TextColumn::make('user.name')->label('Atleta')->searchable()->sortable(),
                TextColumn::make('action')
                    ->label('Acción')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Reserva' => 'success',
                        'Lista de Espera' => 'warning',
                        'Cancelación' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('gymSession.classType.name')->label('Clase')->searchable(),
                TextColumn::make('gymSession.start_time')->label('Hora de Clase')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('created_at')->label('Fecha/Hora del Clic')->dateTime('d/m/Y H:i:s')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->label('Filtrar por Acción')
                    ->options([
                        'Reserva' => 'Reservas Confirmadas',
                        'Lista de Espera' => 'Apuntados en Lista de Espera',
                        'Cancelación' => 'Solo Cancelaciones (Lista Negra)',
                    ]),
            ])
            ->actions([]) 
            ->bulkActions([]); 
    }
    public static function getPages(): array {
        return [
            'index' => Pages\ManageBookingLogs::route('/'),
        ];
    }
}
