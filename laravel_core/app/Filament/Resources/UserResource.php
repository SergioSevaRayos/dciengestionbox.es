<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Package;
use App\Filament\Resources\UserResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Set;
use Filament\Forms\Get;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static bool $isScopedToTenant = true;
    protected static ?string $tenantOwnershipRelationshipName = 'gyms';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Gestión De Usuarios';
    protected static ?string $modelLabel = 'Alumno / Usuario';
    protected static ?string $pluralModelLabel = 'Gestión De Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del Usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nombre completo')->required(),
                        Forms\Components\TextInput::make('email')->label('Correo Electrónico')->email()->required(),
                        Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->options(['student' => 'Alumno', 'admin' => 'Administrador'])
                            ->required()->default('student'),
                            
                        Forms\Components\TextInput::make('password')
                            ->label('Nueva Contraseña')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(false)
                            ->hiddenOn('create')
                            ->formatStateUsing(fn () => null),
                    ])->columns(2),

                Forms\Components\Section::make('Gestión de Bonos y Tarifas')
                    ->description('Los cambios son asíncronos. Recuerda Guardar al finalizar.')
                    ->hiddenOn('create')
                    ->schema([
                        Forms\Components\Repeater::make('userPackages')
                            ->relationship('userPackages')
                            ->label('Pases Activos')
                            ->schema([
                                Forms\Components\Select::make('package_id')
                                    ->relationship('package', 'name')
                                    ->label('Nombre del Pase')
                                    ->disabled()->dehydrated(),
                                    
                                Forms\Components\TextInput::make('remaining_credits')
                                    ->label('Clases')
                                    ->numeric()
                                    ->visible(fn (Get $get) => ($get('type') ?? 'bono') === 'bono'),

                                Forms\Components\Placeholder::make('tariff_info')
                                    ->label('Info Tarifa')
                                    ->content(function (Get $get) {
                                        if ($get('type') !== 'tarifa') return '-';
                                        $amount = $get('limit_amount') ?? '0';
                                        $type = ucfirst($get('limit_type') ?? 'semanal');
                                        return "{$amount} clases / {$type}";
                                    })
                                    ->visible(fn (Get $get) => ($get('type') ?? 'bono') === 'tarifa'),

                                Forms\Components\DateTimePicker::make('expires_at')
                                    ->label('Caducidad')
                                    ->required(),

                                Forms\Components\Hidden::make('type'),
                                Forms\Components\Hidden::make('limit_amount'),
                                Forms\Components\Hidden::make('limit_type'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->disableItemCreation()
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                $type = data_get($state, 'type', 'bono');
                                if ($type === 'tarifa') {
                                    $lim = data_get($state, 'limit_amount', 0);
                                    $period = ucfirst(data_get($state, 'limit_type', 'semanal'));
                                    return "🗓️ TARIFA: " . ($lim == 0 ? 'Ilimitada' : $lim) . " clases / " . $period;
                                }
                                return "🎫 BONO: " . data_get($state, 'remaining_credits', 0) . " clases";
                            }),

                        Forms\Components\Select::make('assign_new_package')
                            ->label('➕ Asignar un nuevo Pase')
                            ->options(fn() => Package::where('gym_id', \Filament\Facades\Filament::getTenant()->id)->pluck('name', 'id'))
                            ->placeholder('Selecciona para añadir al instante...')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if (!$state) return;

                                $package = Package::find($state);
                                if (!$package) return;

                                $currentPackages = $get('userPackages') ?? [];
                                
                                $expiresAt = $package->validity_days > 0 
                                    ? now()->addDays($package->validity_days) 
                                    : now()->endOfMonth();

                                // Añadimos el nuevo elemento al final del array
                                $currentPackages[] = [
                                    'package_id' => $package->id,
                                    'type' => $package->type,
                                    'limit_amount' => $package->limit_amount,
                                    'limit_type' => $package->limit_type,
                                    'remaining_credits' => $package->type === 'bono' ? $package->credits : 0,
                                    'expires_at' => $expiresAt->toDateTimeString(),
                                ];

                                $set('userPackages', array_values($currentPackages));
                                $set('assign_new_package', null);
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('welcome_email_sent')
                    ->label('Aviso Email')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                
                Tables\Columns\TextColumn::make('status_pases')
                    ->label('Estado Pases')
                    ->badge()
                    ->getStateUsing(function (User $record): string {
                        $active = $record->userPackages()->where('expires_at', '>', now())->get();
                        if ($active->isEmpty()) return 'Sin pases';
                        $tarifa = $active->where('type', 'tarifa')->first();
                        return $tarifa ? "Tarifa: " . ($tarifa->limit_amount ?: '∞') : $active->sum('remaining_credits') . " Clases";
                    })
                    ->color(fn (User $record): string => $record->userPackages()->where('expires_at', '>', now())->exists() ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('created_at')->label('Registro')->date('d/m/Y'),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
