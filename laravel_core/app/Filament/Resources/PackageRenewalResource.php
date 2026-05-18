<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageRenewalResource\Pages;
use App\Models\PackageRenewal;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class PackageRenewalResource extends Resource
{
    protected static ?string $model = PackageRenewal::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Ventas y Facturación';
    protected static ?string $modelLabel = 'Solicitud de Renovación';
    protected static ?string $pluralModelLabel = 'Renovaciones Pendientes';
    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Alumno')->searchable(),
                Tables\Columns\TextColumn::make('package.name')->label('Bono Solicitado'),
                Tables\Columns\TextColumn::make('package.price')->label('Precio')->money('EUR'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'aprobado',
                        'danger' => 'rechazado',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha Solicitud')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pendiente' => 'Pendientes', 'aprobado' => 'Aprobadas', 'rechazado' => 'Rechazadas'])
                    ->default('pendiente'),
            ])
            ->poll('5s') // ⚡ La tabla se refresca sola cada 5 segundos
            ->bulkActions([
                Tables\Actions\BulkAction::make('aprobar_masivo')
                    ->label('Activar Bonos Seleccionados')
                    ->icon('heroicon-o-bolt')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Activar renovaciones')
                    ->modalDescription('¿Confirmas que deseas activar estos bonos? Se irán directos a la lista de impagados.')
                    ->modalSubmitActionLabel('Sí, Activar Bonos')
                    ->action(function (Collection $records, \Livewire\Component $livewire) {
                        $count = 0;
                        $gymId = \Filament\Facades\Filament::getTenant()->id;

                        foreach ($records as $record) {
                            if ($record->status === 'pendiente') {
                                $user = $record->user;
                                $package = $record->package;

                                $record->update(['status' => 'aprobado']);

                                // 🧠 Lógica inteligente de Caducidad (Infinito, fin de mes o fijo)
                                $expiresAt = null;
                                if ($package->validity_days === 0 || $package->validity_days === '0') {
                                    $expiresAt = now()->addYears(100);
                                } elseif (is_null($package->validity_days)) {
                                    $expiresAt = now()->endOfMonth();
                                } else {
                                    $expiresAt = now()->addDays((int) $package->validity_days);
                                }

                                // BILLETERA ESTRICTA: Creamos el sobre sin tocar los créditos del usuario
                                DB::table('user_packages')->insert([
                                    'gym_id' => $record->gym_id,
                                    'user_id' => $user->id,
                                    'package_id' => $package->id,
                    'type' => $package->type,
                    'limit_type' => $package->limit_type,
                    'limit_amount' => $package->limit_amount,
                                    'is_paid' => false,
                                    'remaining_credits' => $package->credits,
                                    'expires_at' => $expiresAt,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);

                                // Registrar Activación en el Historial de Cobros
                                \App\Models\PaymentLog::create([
                                    'gym_id' => $record->gym_id,
                                    'bono_name' => $package->name,
                                    'client_name' => $user->name,
                                    'activated_at' => now(),
                                    'activated_by' => auth()->user()->name ?? 'Gestor',
                                ]);

                                $count++;
                            }
                        }

                        // Forzamos el refresco de componentes Livewire vinculados
                        $livewire->dispatch('credits-updated');

                        Notification::make()
                            ->title("$count bonos activados correctamente")
                            ->body('Recuerda confirmar los pagos en tu lista de morosos.')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackageRenewals::route('/'),
        ];
    }

    // --- ⚡ CONTADOR ASÍNCRONO DEL MENÚ LATERAL ---

    public static function getNavigationBadge(): ?string
    {
        // Contamos solo las peticiones pendientes del gimnasio actual (SaaS estricto)
        $tenant = \Filament\Facades\Filament::getTenant();
        if (!$tenant) return null;

        $count = static::getModel()::where('gym_id', $tenant->id)
            ->where('status', 'pendiente')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success'; // El badge se pintará en verde para llamar la atención
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        // 🔮 MAGIA LIVEWIRE: Instruye al navegador a consultar el servidor cada 5 segundos
        return 'wire:poll.5s';
    }

    // ----------------------------------------------

    public static function canCreate(): bool { return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false; }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool { return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false; }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false; }

    public static function getNavigationLabel(): string
    {
        $label = parent::getNavigationLabel();
        $isSubscribed = \Filament\Facades\Filament::getTenant()?->is_subscribed ?? false;
        return $isSubscribed ? $label : $label . " 🔒";
    }
}
