<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';
    protected static ?string $navigationLabel = 'Soporte DCIEN';
    protected static ?string $modelLabel = 'Ticket de Soporte';
    protected static ?string $pluralModelLabel = 'Centro de Soporte';
    protected static ?int $navigationSort = 99; // Lo mandamos abajo del menú

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles de la Incidencia')->schema([
                    Forms\Components\TextInput::make('subject')
                        ->label('Asunto')
                        ->placeholder('Ej: Problema al crear una clase')
                        ->required()
                        ->maxLength(255),
                        
                    Forms\Components\Select::make('priority')
                        ->label('Nivel de Urgencia')
                        ->options([
                            'baja' => 'Baja (Duda o sugerencia)',
                            'media' => 'Media (Problema no bloqueante)',
                            'alta' => 'Alta (Sistema caído o bloqueo total)',
                        ])
                        ->default('media')
                        ->required(),
                        
                    Forms\Components\Textarea::make('description')
                        ->label('Descripción detallada')
                        ->placeholder('Explica qué estabas intentando hacer y qué error te apareció...')
                        ->required()
                        ->columnSpanFull()
                        ->rows(4),
                        
                    Forms\Components\FileUpload::make('attachment')
                        ->label('Captura de pantalla (Opcional)')
                        ->directory('tickets-attachments')
                        ->image()
                        ->maxSize(2048)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('resolution_notes')
                        ->label('Respuesta del Equipo Técnico')
                        ->disabled() 
                        ->visible(fn ($record) => $record && $record->resolution_notes)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->label('Asunto')
                    ->searchable()
                    ->weight('bold'),
                    
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
                    }),
                    
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baja' => 'gray',
                        'media' => 'info',
                        'alta' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enviado el')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'abierto' => 'Abiertos',
                        'en_proceso' => 'En Revisión',
                        'resuelto' => 'Resueltos',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver'),
                Tables\Actions\EditAction::make()->label('Editar')->hidden(fn ($record) => $record->status === 'resuelto'),
            ])
            ->emptyStateHeading('No hay incidencias reportadas')
            ->emptyStateDescription('Si tienes algún problema con el sistema, crea un ticket y te ayudaremos lo antes posible.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
