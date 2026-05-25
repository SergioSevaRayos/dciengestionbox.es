<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArenaCompetitionResource\Pages;
use App\Models\ArenaCompetition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;

class ArenaCompetitionResource extends Resource
{
    protected static ?string $model = ArenaCompetition::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Arena';
    protected static ?string $modelLabel = 'Competición';
    protected static ?string $pluralModelLabel = 'Competiciones';
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        try {
            $gym = \Filament\Facades\Filament::getTenant();
            return $gym?->settings?->enable_arena ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos de la competición')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')->required()->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->label('Descripción')->rows(2)->columnSpanFull(),
                    Forms\Components\Select::make('type')
                        ->label('Tipo')
                        ->options([
                            'internal' => '🏠 Interna (solo este box)',
                            'open'     => '🌍 Open (ranking global)',
                        ])
                        ->default('internal')->required(),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha inicio')->required()->native(false)->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha fin')->native(false)->displayFormat('d/m/Y'),
                    ])->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Competición activa')->default(true),
                    Forms\Components\Toggle::make('registration_open')
                        ->label('Inscripciones abiertas')->default(true),
                ]),

            Forms\Components\Section::make('WODs')
                ->description('Define los entrenamientos que se puntuarán.')
                ->schema([
                    Forms\Components\Repeater::make('wods')
                        ->relationship('wods')
                        ->label('')
                        ->schema([
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre del WOD')->required(),
                                Forms\Components\Select::make('score_type')
                                    ->label('Tipo de puntuación')
                                    ->options([
                                        'time'   => '⏱ Tiempo (menor = mejor)',
                                        'amrap'  => '🔄 AMRAP (rondas+reps)',
                                        'weight' => '🏋️ Peso (mayor = mejor)',
                                        'reps'   => '💪 Repeticiones (mayor = mejor)',
                                        'points' => '🏆 Puntos directos',
                                    ])
                                    ->default('time')->required(),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Activo')->default(true)->inline(false),
                            ]),
                            Forms\Components\Textarea::make('description')
                                ->label('Descripción del WOD')->rows(2)->columnSpanFull(),
                        ])
                        ->addActionLabel('+ Añadir WOD')
                        ->reorderable('order')
                        ->collapsible()
                        ->defaultItems(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid(['md' => 2, 'xl' => 3])
            ->columns([
                Stack::make([
                    // Fila 1: Nombre + Badge tipo
                    Split::make([
                        Tables\Columns\TextColumn::make('name')
                            ->label('Competición')->searchable()->sortable()
                            ->weight('bold')->size('lg')->grow(true),
                        Tables\Columns\BadgeColumn::make('type')
                            ->label('Tipo')
                            ->colors(['warning' => 'internal', 'success' => 'open'])
                            ->formatStateUsing(fn($state) => $state === 'open' ? '🌍 Open' : '🏠 Interna')
                            ->grow(false),
                    ]),
                    // Fila 2: Fechas
                    Split::make([
                        Tables\Columns\TextColumn::make('start_date')
                            ->label('Inicio')->date('d/m/Y')->sortable()
                            ->icon('heroicon-m-calendar')->color('gray')->grow(true),
                        Tables\Columns\TextColumn::make('end_date')
                            ->label('Fin')->date('d/m/Y')->placeholder('Sin fecha fin')
                            ->icon('heroicon-m-flag')->color('gray')->grow(false),
                    ]),
                    // Fila 3: Contadores
                    Split::make([
                        Tables\Columns\TextColumn::make('wods_count')
                            ->counts('wods')->label('WODs')
                            ->icon('heroicon-m-clipboard-document-list')
                            ->formatStateUsing(fn($state) => $state . ' WODs')
                            ->color('gray')->grow(true),
                        Tables\Columns\TextColumn::make('teams_count')
                            ->counts('teams')->label('Equipos')
                            ->icon('heroicon-m-users')
                            ->formatStateUsing(fn($state) => $state . ' equipos')
                            ->color('gray')->grow(false),
                    ]),
                    // Fila 4: Estado
                    Split::make([
                        Tables\Columns\IconColumn::make('is_active')
                            ->label('Activa')->boolean()->grow(false),
                        Tables\Columns\TextColumn::make('is_active')
                            ->formatStateUsing(fn($state) => $state ? 'Activa' : 'Inactiva')
                            ->color(fn($state) => $state ? 'success' : 'gray')
                            ->weight('medium')->grow(true),
                        Tables\Columns\IconColumn::make('registration_open')
                            ->label('Inscripciones')->boolean()->grow(false),
                        Tables\Columns\TextColumn::make('registration_open')
                            ->formatStateUsing(fn($state) => $state ? 'Inscripciones abiertas' : 'Cerradas')
                            ->color(fn($state) => $state ? 'success' : 'danger')
                            ->weight('medium')->grow(false),
                    ]),
                ])->space(2),
            ])
            ->defaultSort('start_date', 'desc')
            ->actions([
                Tables\Actions\Action::make('gestionar')
                    ->label('Gestionar')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('gray')
                    ->url(fn(ArenaCompetition $record) => static::getUrl('manage', ['record' => $record])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListArenaCompetitions::route('/'),
            'create' => Pages\CreateArenaCompetition::route('/create'),
            'edit'   => Pages\EditArenaCompetition::route('/{record}/edit'),
            'manage' => Pages\ManageArenaCompetition::route('/{record}/manage'),
        ];
    }
}
