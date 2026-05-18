<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $modelLabel = 'Bono o Tarifa';
    protected static ?string $pluralModelLabel = 'Bonos y Tarifas';
    protected static ?string $navigationLabel = 'Bonos y Tarifas';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Ventas y Facturación';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos Principales')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Tarifa/Bono')
                            ->placeholder('Ej: Mensual 3 Días, Bono 10 Clases...')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('price')
                            ->label('Precio (€)')
                            ->numeric()
                            ->prefix('€')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Naturaleza del Pase')
                    ->schema([
                        Forms\Components\Radio::make('type')
                            ->label('¿Qué tipo de pase es?')
                            ->options([
                                'bono' => '🎫 Bono de Clases (Bolsa de puntos que se van restando)',
                                'tarifa' => '🗓️ Tarifa Recurrente (Clases limitadas por semana/mes)',
                            ])
                            ->default('bono')
                            ->live() // Hace que el formulario reaccione al instante
                            ->required(),

                        // --- CAMPO PARA BONOS ---
                        Forms\Components\TextInput::make('credits')
                            ->label('Total de Clases (Créditos)')
                            ->placeholder('Ej: 10')
                            ->numeric()
                            ->visible(fn (Get $get): bool => $get('type') === 'bono')
                            ->required(fn (Get $get): bool => $get('type') === 'bono')
                            ->dehydrated(true)
                            ->dehydrateStateUsing(fn ($state) => $state ?: 0),

                        // --- CAMPOS PARA TARIFAS ---
                        Forms\Components\Grid::make(2)
                            ->visible(fn (Get $get): bool => $get('type') === 'tarifa')
                            ->schema([
                                Forms\Components\TextInput::make('limit_amount')
                                    ->label('Límite de Clases')
                                    ->numeric()
                                    ->default(3)
                                    ->helperText('Pon "0" para acceso Ilimitado.')
                                    ->required(fn (Get $get): bool => $get('type') === 'tarifa'),

                                Forms\Components\Select::make('limit_type')
                                    ->label('Periodo')
                                    ->options([
                                        'semanal' => 'A la semana (L a D)',
                                        'mensual' => 'Al mes (Mes natural)',
                                        'anual' => 'Al año',
                                    ])
                                    ->default('semanal')
                                    ->required(fn (Get $get): bool => $get('type') === 'tarifa'),
                            ]),
                    ]),

                Forms\Components\Section::make('Caducidad (Renovación)')
                    ->schema([
                        Forms\Components\Select::make('expiry_type')
                            ->label('¿Cuándo caduca este pase una vez activado?')
                            ->options([
                                'end_of_month' => '📅 Caduca a final del mes en curso',
                                'custom_days' => '⏳ Caduca en un número de días fijo',
                                'never' => '♾️ No caduca nunca (Ilimitado)',
                            ])
                            ->live()
                            ->afterStateHydrated(function (Forms\Components\Select $component, Get $get, Set $set) {
                                $days = $get('validity_days');
                                if ($days === 0 || $days === '0') {
                                    $set('expiry_type', 'never');
                                } elseif (is_null($days)) {
                                    $set('expiry_type', 'end_of_month');
                                } else {
                                    $set('expiry_type', 'custom_days');
                                }
                            })
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state === 'never') $set('validity_days', 0);
                                if ($state === 'end_of_month') $set('validity_days', null);
                                if ($state === 'custom_days') $set('validity_days', 30);
                            })
                            ->dehydrated(false)
                            // 🚀 EL FIX ESTÁ AQUÍ (El signo ? y la protección contra null)
                            ->saveRelationshipsUsing(function (?\Illuminate\Database\Eloquent\Model $record, $state, Get $get) {
                                if (! $record) return; // Si aún no se ha creado en la BBDD, no hacemos nada

                                $val = null;
                                if ($state === 'never') $val = 0;
                                elseif ($state === 'end_of_month') $val = null;
                                else {
                                    $days = $get('validity_days');
                                    $val = filled($days) ? (int) $days : null;
                                }

                                DB::table('packages')
                                    ->where('id', $record->id)
                                    ->update(['validity_days' => $val]);
                            })
                            ->required(),

                        Forms\Components\TextInput::make('validity_days')
                            ->label('¿Cuántos días dura?')
                            ->numeric()
                            ->visible(fn (Get $get): bool => $get('expiry_type') === 'custom_days')
                            ->required(fn (Get $get): bool => $get('expiry_type') === 'custom_days')
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Pase')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Naturaleza')
                    ->colors([
                        'warning' => 'bono',
                        'success' => 'tarifa',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state ?? 'bono')),

                Tables\Columns\TextColumn::make('credits')
                    ->label('Configuración')
                    ->formatStateUsing(function ($record) {
                        if ($record->type === 'tarifa') {
                            $lim = $record->limit_amount == 0 ? '♾️ Ilim.' : $record->limit_amount;
                            $tipo = ucfirst($record->limit_type ?? 'semanal');
                            return "{$lim} / {$tipo}";
                        }
                        return "{$record->credits} Clases";
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('validity_days')
                    ->label('Caducidad')
                    ->badge()
                    ->color(fn ($state) => $state === 0 ? 'success' : (is_null($state) ? 'warning' : 'info'))
                    ->formatStateUsing(function ($state) {
                        if ($state === 0 || $state === '0') return '♾️ Ilimitado';
                        return is_null($state) ? '📅 Fin de mes' : "⏳ {$state} días";
                    }),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool { return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false; }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool { return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false; }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false; }

    public static function getNavigationLabel(): string {
        $label = parent::getNavigationLabel();
        $isSubscribed = \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false;
        return $isSubscribed ? $label : $label . " 🔒";
    }
}
