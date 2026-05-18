<?php
namespace App\Filament\Resources;

use App\Filament\Resources\GymSessionResource\Pages;
use App\Filament\Resources\GymSessionResource\RelationManagers;
use App\Models\GymSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Collection;

class GymSessionResource extends Resource
{
    protected static ?string $model = GymSession::class;
    protected static ?string $modelLabel = 'Clase Programada';
    protected static ?string $pluralModelLabel = 'Calendario de Clases';
    protected static ?string $navigationLabel = 'Calendario';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Gestión Deportiva';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('class_type_id')->relationship('classType', 'name')->required(),
            Forms\Components\DateTimePicker::make('start_time')->label('Inicio')->required(),
            Forms\Components\DateTimePicker::make('end_time')->label('Fin')->required(),
            Forms\Components\TextInput::make('capacity')->label('Aforo')->numeric()->default(20)->required(),
            
            Forms\Components\Section::make('Repetición Automática')->hiddenOn('edit')->schema([
                Forms\Components\Toggle::make('is_recurring')->live(),
                Forms\Components\CheckboxList::make('days_of_week')
                    ->options([1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',0=>'Domingo'])
                    ->visible(fn(Forms\Get $get) => $get('is_recurring')),
                Forms\Components\DatePicker::make('repeat_until')
                    ->visible(fn(Forms\Get $get) => $get('is_recurring')),
            ])->collapsed(),

            // NUEVA SECCIÓN WOD
            Forms\Components\Section::make('Entrenamiento (WOD)')->schema([
                Forms\Components\Toggle::make('is_workout_visible')
                    ->label('Visible para los atletas en la App')
                    ->default(false),
                Forms\Components\Select::make('workout_id')
                    ->label('Seleccionar Entrenamiento (WOD)')
                    ->relationship('workout', 'name')
                    ->searchable()
                    ->preload()
                    ->editOptionForm([
                        Forms\Components\TextInput::make('name')->label('Nombre')->required(),
                        Forms\Components\RichEditor::make('content')->label('Desarrollo del WOD')->required()
                    ])
                    ->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('classType.name')->label('Disciplina'),
            Tables\Columns\TextColumn::make('start_time')->label('Día y Hora')->dateTime('d/m/Y H:i'),
            
            // Columna rápida para ver si tiene WOD y si está visible
            Tables\Columns\IconColumn::make('is_workout_visible')
                ->label('WOD Visible')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-eye-slash'),

            Tables\Columns\TextColumn::make('ocupacion')
                ->label('Plazas Ocupadas')
                ->state(function (GymSession $record) {
                    $ocupadas = $record->bookings()->where('status', 'booked')->count();
                    return "{$ocupadas} / {$record->capacity}";
                })
                ->badge()
                ->color(function (GymSession $record) {
                    $ocupadas = $record->bookings()->where('status', 'booked')->count();
                    return $ocupadas >= $record->capacity ? 'danger' : 'success';
                }),
        ])->filters([
            Filter::make("start_time")
                ->form([
                    Forms\Components\DatePicker::make("created_from")->label("Desde fecha"),
                    Forms\Components\DatePicker::make("created_until")->label("Hasta fecha"),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data["created_from"],
                            fn (Builder $query, $date): Builder => $query->whereDate("start_time", ">=", $date),
                        )
                        ->when(
                            $data["created_until"],
                            fn (Builder $query, $date): Builder => $query->whereDate("start_time", "<=", $date),
                        );
                })
        ])->actions([
            Tables\Actions\EditAction::make()->label('Ver/Editar'),
            
            // NUEVA ACCIÓN: CLONAR WOD
            Tables\Actions\Action::make('cloneWorkout')
                ->label('Clonar WOD')
                ->icon('heroicon-o-document-duplicate')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Clonar Entrenamiento a otros días')
                ->modalDescription('Selecciona las clases a las que quieres pegar este mismo entrenamiento y visibilidad.')
                ->form([
                    Forms\Components\Select::make('target_sessions')
                        ->label('Selecciona las clases destino')
                        ->multiple()
                        ->searchable()
                        ->options(function (GymSession $record) {
                            // Buscar clases futuras del mismo gimnasio que NO sean la actual
                            return GymSession::where('gym_id', $record->gym_id)
                                ->where('id', '!=', $record->id)
                                ->where('start_time', '>=', now())
                                ->orderBy('start_time')
                                ->get()
                                ->mapWithKeys(function ($session) {
                                    $formattedDate = \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i');
                                    $className = $session->classType->name ?? 'Clase';
                                    return [$session->id => "{$className} - {$formattedDate}"];
                                })->toArray();
                        })
                        ->required(),
                ])
                ->action(function (GymSession $record, array $data): void {
                    // Acción de clonado masivo
                    GymSession::whereIn('id', $data['target_sessions'])->update([
                        'workout_id' => $record->workout_id,
                        'is_workout_visible' => $record->is_workout_visible,
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Entrenamiento clonado con éxito')
                        ->success()
                        ->send();
                })
                // Solo mostrar si esta clase tiene un entrenamiento escrito
                ->visible(fn (GymSession $record): bool => !empty($record->workout_id)),
                
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGymSessions::route('/'),
            'create' => Pages\CreateGymSession::route('/create'),
            'edit' => Pages\EditGymSession::route('/{record}/edit'),
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
