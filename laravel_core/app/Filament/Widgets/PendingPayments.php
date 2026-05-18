<?php

namespace App\Filament\Widgets;

use App\Models\UserPackage;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Notifications\Notification;

class PendingPayments extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = null;
    protected static string $view = 'filament.widgets.pending-payments-wrapper';

    public function table(Table $table): Table
    {
        return $table
            ->query(UserPackage::query()->where('is_paid', 0)->latest())
            ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Alumno')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('package.name')->label('Bono Activado')->badge()->color('warning'),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de Activación')->dateTime('d/m/Y H:i')->description(fn ($record) => $record->created_at->diffForHumans()),
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->label('Confirmar Pago')
                    ->icon('heroicon-m-currency-euro')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (UserPackage $record) {
                        $record->update(['is_paid' => true]);

                        // 👉 INYECCIÓN: Registrar el Cobro en el Historial
                        $log = \App\Models\PaymentLog::where('gym_id', $record->gym_id)
                            ->where('client_name', $record->user->name)
                            ->where('bono_name', $record->package->name)
                            ->whereNull('paid_at')
                            ->latest()
                            ->first();

                        if ($log) {
                            $log->update([
                                'paid_at' => now(),
                                'paid_by' => auth()->user()->name ?? 'Gestor',
                            ]);
                        } else {
                            // Por si se activó por otra vía, no perdemos el cobro
                            \App\Models\PaymentLog::create([
                                'gym_id' => $record->gym_id,
                                'client_name' => $record->user->name,
                                'bono_name' => $record->package->name,
                                'paid_at' => now(),
                                'paid_by' => auth()->user()->name ?? 'Gestor',
                            ]);
                        }
                        Notification::make()->title('Pago confirmado')->success()->send();
                    }),
            ])
            ->emptyStateHeading('¡Todo al día!')
            ->emptyStateIcon('heroicon-o-face-smile');
    }
}
