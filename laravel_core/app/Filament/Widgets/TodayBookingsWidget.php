<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\GymSession;
use Carbon\Carbon;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;

class TodayBookingsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Resumen de Clases - Hoy';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->query(GymSession::query()->whereDate('start_time', Carbon::today())->orderBy('start_time', 'asc'))
            ->contentGrid(['sm' => 1, 'md' => 2, 'xl' => 3])
            ->columns([
                Stack::make([
                    Split::make([
                        Tables\Columns\TextColumn::make('classType.name')->weight('bold')->grow(true),
                        Tables\Columns\TextColumn::make('ocupacion')
                            ->state(fn (GymSession $record) => $record->bookings()->where('status', 'booked')->count() . " / " . $record->capacity)
                            ->badge()
                            ->color(fn($record) => $record->bookings()->where('status', 'booked')->count() >= $record->capacity ? 'danger' : 'success')
                            ->grow(false),
                    ]),
                    Tables\Columns\TextColumn::make('start_time')->icon('heroicon-m-clock')->time('H:i')->color('gray'),

                    Tables\Columns\TextColumn::make('alumnos')
                        ->state(function (GymSession $record) {
                            $nombres = $record->bookings()
                                ->where('status', 'booked')
                                ->with('user')
                                ->get()
                                ->pluck('user.name')
                                ->toArray();

                            return empty($nombres) ? ['Sin reservas aún'] : $nombres;
                        })
                        ->badge()
                        ->color(fn (string $state): string => $state === 'Sin reservas aún' ? 'gray' : 'info')
                        ->wrap()
                        ->extraAttributes(['class' => 'pt-3 mt-1 border-t border-gray-100 dark:border-gray-800']),
                ])->space(3),
            ])
            ->actions([
                Tables\Actions\Action::make('gestionar')
                    ->label('Gestionar')
                    ->icon('heroicon-m-pencil-square')
                    ->size('sm')
                    ->url(fn (GymSession $record): string => \App\Filament\Resources\GymSessionResource::getUrl('edit', ['record' => $record])),
            ])->paginated(false);
    }
}
