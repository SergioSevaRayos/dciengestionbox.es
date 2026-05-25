<?php
namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Booking;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';
    protected static ?string $title = 'Historial de Reservas';
    protected static ?string $icon = 'heroicon-o-calendar-days';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('gymSession.classType.name')->label('Clase')->weight('bold')->searchable(),
                Tables\Columns\TextColumn::make('gymSession.start_time')->label('Fecha y Hora')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Estado')->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) { 'booked' => 'Confirmada', 'waiting' => 'En Espera', 'cancelled' => 'Cancelada', default => $state })
                    ->color(fn (string $state): string => match ($state) { 'booked' => 'success', 'waiting' => 'warning', 'cancelled' => 'danger', default => 'gray' }),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de Acción')->dateTime('d/m/Y H:i')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('cancelar_reserva')->label('Cancelar')->icon('heroicon-o-x-circle')->color('danger')
                    ->requiresConfirmation()->modalHeading('¿Cancelar reserva del alumno?')->modalDescription('Devolverá la clase a su tarifa/bono y avisará a la lista de espera.')
                    ->visible(fn (Booking $record) => in_array($record->status, ['booked', 'waiting']))
                    ->action(fn (Booking $record) => $record->delete()),
            ])
            ->emptyStateHeading('Sin reservas')
            ->emptyStateDescription('Este alumno aún no se ha apuntado a ninguna clase.');
    }
}
