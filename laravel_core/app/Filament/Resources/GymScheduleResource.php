<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GymScheduleResource\Pages;
use App\Models\GymSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GymScheduleResource extends Resource
{
    protected static ?string $model = GymSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Horarios Maestros';
    protected static ?string $modelLabel = 'Horario';
    protected static ?string $pluralModelLabel = 'Horarios Maestros';
    protected static ?string $navigationGroup = 'Gestión Deportiva';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración del Horario')
                    ->description('Define la regla de repetición para generar las clases automáticamente.')
                    ->schema([
                        Forms\Components\Select::make('class_type_id')
                            ->relationship('classType', 'name')
                            ->label('Disciplina')
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\TimePicker::make('start_time')
                            ->label('Hora de Inicio')
                            ->required()
                            ->seconds(false),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('Hora de Fin')
                            ->required()
                            ->seconds(false),

                        Forms\Components\CheckboxList::make('days_of_week')
                            ->label('Días de la Semana')
                            ->options([
                                1 => 'Lunes',
                                2 => 'Martes',
                                3 => 'Miércoles',
                                4 => 'Jueves',
                                5 => 'Viernes',
                                6 => 'Sábado',
                                0 => 'Domingo',
                            ])
                            ->required()
                            ->columns(4)
                            ->columnSpan(2),

                        Forms\Components\DatePicker::make('repeat_until')
                            ->label('Fecha Fin del Ciclo (Repetir hasta...)')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\TextInput::make('capacity')
                            ->label('Aforo por clase')
                            ->numeric()
                            ->default(20)
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classType.name')
                    ->label('Disciplina')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Horario')
                    ->formatStateUsing(fn($record) => substr($record->start_time, 0, 5) . ' - ' . substr($record->end_time, 0, 5)),

                Tables\Columns\TextColumn::make('days_of_week')
                    ->label('Frecuencia')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        '1' => 'Lun', '2' => 'Mar', '3' => 'Mié', '4' => 'Jue', '5' => 'Vie', '6' => 'Sáb', '0' => 'Dom',
                        default => $state,
                    })
                    ->color('success'),

                Tables\Columns\TextColumn::make('repeat_until')
                    ->label('Finaliza')
                    ->date('d/M/Y')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Plazas')
                    ->badge(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGymSchedules::route('/'),
            'create' => Pages\CreateGymSchedule::route('/create'),
            'edit' => Pages\EditGymSchedule::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false;
    }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false;
    }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false;
    }


    


    public static function getNavigationLabel(): string
    {
        $label = parent::getNavigationLabel();
        $isSubscribed = \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false;
        if ($isSubscribed) {
            return $label;
        }
        return $isSubscribed ? $label : $label . " 🔒";
    }

}