<?php

namespace App\Filament\System\Resources;

use App\Filament\System\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = 'Buzón de Soporte';
    protected static ?string $navigationGroup = 'Atención al Cliente';
    protected static ?string $modelLabel = 'Ticket';
    protected static ?string $pluralModelLabel = 'Tickets de Soporte';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'abierto')->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Reporte del Cliente')
                    ->description('Detalles de la incidencia enviada desde el panel del gimnasio.')
                    ->schema([
                        // 🚀 CAMBIO AQUÍ: Usamos Select con relationship para forzar la carga del nombre
                        Forms\Components\Select::make('gym_id')
                            ->relationship('gym', 'name')
                            ->label('Gimnasio')
                            ->disabled(),
                            
                        // 🚀 CAMBIO AQUÍ: Usamos Select con relationship
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Reportado por')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('subject')
                            ->label('Asunto')
                            ->disabled()
                            ->columnSpanFull(),
                            
                        Forms\Components\Select::make('priority')
                            ->label('Urgencia')
                            ->options([
                                'baja' => 'Baja',
                                'media' => 'Media',
                                'alta' => 'Alta',
                            ])
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Fecha de reporte')
                            ->disabled(),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción del problema')
                            ->disabled()
                            ->columnSpanFull()
                            ->rows(4),
                            
                        Forms\Components\FileUpload::make('attachment')
                            ->label('Captura de pantalla')
                            ->directory('tickets-attachments')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Resolución del Equipo (Tú)')
                    ->description('Cambia el estado y responde al cliente. Verán este mensaje en su panel.')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado actual')
                            ->options([
                                'abierto' => '🔴 Abierto (Pendiente)',
                                'en_proceso' => '🟡 En Revisión (Trabajando en ello)',
                                'resuelto' => '🟢 Resuelto (Cerrado)',
                            ])
                            ->required(),
                            
                        Forms\Components\Textarea::make('resolution_notes')
                            ->label('Tu respuesta (Visible para el cliente)')
                            ->placeholder('Ej: Hemos vaciado la caché y el problema de las reservas está solucionado...')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gym.name')
                    ->label('Gimnasio')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('subject')
                    ->label('Asunto')
                    ->limit(40)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baja' => 'gray',
                        'media' => 'info',
                        'alta' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'abierto' => 'danger',
                        'en_proceso' => 'warning',
                        'resuelto' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'abierto' => 'Abierto',
                        'en_proceso' => 'En Revisión',
                        'resuelto' => 'Resuelto',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d M H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrar por Estado')
                    ->options([
                        'abierto' => 'Solo Abiertos',
                        'en_proceso' => 'En Revisión',
                        'resuelto' => 'Resueltos',
                    ]),
                Tables\Filters\SelectFilter::make('gym_id')
                    ->label('Filtrar por Gimnasio')
                    ->relationship('gym', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Gestionar'),
            ])
            ->emptyStateHeading('Bandeja limpia')
            ->emptyStateDescription('No hay tickets de soporte en este momento.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
