<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PaymentLogResource\Pages;
use App\Models\PaymentLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;

class PaymentLogResource extends Resource
{
    protected static ?string $model = PaymentLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Ventas y Facturación';
    protected static ?string $navigationLabel = 'Historial de Pagos';
    protected static ?string $pluralModelLabel = 'Historial de Pagos';
    protected static ?int $navigationSort = 4;

    
    

    

    
    

    

    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    // BLOQUE 1: Alumno y Bono
                    Stack::make([
                        Tables\Columns\TextColumn::make('client_name')
                            ->weight('bold')
                            ->searchable()
                            ->sortable()
                            ->color(fn (PaymentLog $record): string => $record->status === 'cancelado' ? 'danger' : 'default'),
                        
                        Tables\Columns\TextColumn::make('bono_name')
                            ->size('sm')
                            ->color('gray')
                            ->description(fn (PaymentLog $record): ?string => $record->status === 'cancelado' ? 'ANULADO: ' . $record->cancellation_reason : null)
                            ->extraAttributes(fn (PaymentLog $record) => $record->status === 'cancelado' ? ['style' => 'text-decoration: line-through;'] : []),
                    ])->space(1),

                    // BLOQUE 2: Activación
                    Stack::make([
                        Tables\Columns\TextColumn::make('activated_at')
                            ->icon('heroicon-m-bolt')
                            ->dateTime('d/m/y H:i')
                            ->color('gray')
                            ->size('sm'),
                            
                        Tables\Columns\TextColumn::make('activated_by')
                            ->size('xs')
                            ->color('gray')
                            ->formatStateUsing(fn ($state) => 'Activado por: ' . ($state ?? 'Sistema')),
                    ])->space(1),

                    // BLOQUE 3: Cobro
                    Stack::make([
                        Tables\Columns\TextColumn::make('paid_at')
                            ->icon('heroicon-m-currency-euro')
                            ->dateTime('d/m/y H:i')
                            ->color(fn (PaymentLog $record) => $record->paid_at ? 'success' : ($record->status === 'cancelado' ? 'gray' : 'warning'))
                            ->placeholder(fn (PaymentLog $record) => $record->status === 'cancelado' ? 'Cobro Cancelado' : 'Cobro Pendiente')
                            ->size('sm'),
                            
                        Tables\Columns\TextColumn::make('paid_by')
                            ->size('xs')
                            ->color('gray')
                            ->formatStateUsing(fn ($state) => 'Cobrado por: ' . $state),
                    ])->space(1),
                ])->from('md') // Esto le dice que use formato columnas solo a partir de pantallas medianas (PC/Tablet)
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Anular Registro de Pago')
                    ->modalDescription('Este registro quedará marcado como inactivo. Por favor, explica el motivo.')
                    ->modalSubmitActionLabel('Confirmar Anulación')
                    ->form([
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('Motivo de la anulación')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (PaymentLog $record, array $data): void {
                        $record->update([
                            'status' => 'cancelado',
                            'cancellation_reason' => $data['cancellation_reason'],
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Registro anulado')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (PaymentLog $record): bool => $record->status === 'activo'),
            ])
            ->headerActions([
                Tables\Actions\BulkAction::make('export_csv')
                    ->label('Descargar CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $csv = "Estado,Alumno,Bono,Activacion,Activado Por,Cobro,Cobrado Por,Motivo Anulacion\n";
                        foreach ($records as $row) {
                            $estado = strtoupper($row->status);
                            $csv .= "{$estado},{$row->client_name},{$row->bono_name},{$row->activated_at},{$row->activated_by},{$row->paid_at},{$row->paid_by},{$row->cancellation_reason}\n";
                        }
                        return response()->streamDownload(fn () => print($csv), "historial_pagos_" . now()->format('Y-m-d') . ".csv");
                    }),
            ]);
    }

    public static function getPages(): array { return ['index' => Pages\ListPaymentLogs::route('/')]; }
}
