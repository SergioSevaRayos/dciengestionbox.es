<?php

namespace App\Filament\App\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\GymSession;
use App\Models\Booking;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Facades\Filament;

class TodayClassesWidget extends BaseWidget
{
    protected static ?string $heading = 'Próximas Clases (24h)';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 0;

    public function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->paginated(false)
            ->query(
                GymSession::query()
                    ->whereBetween('start_time', [now(), now()->addHours(24)])
                    ->orderBy('start_time', 'asc')
            )
            ->contentGrid(['md' => 2, 'xl' => 3])
            ->columns([
                Stack::make([
                    Tables\Columns\TextColumn::make('classType.name')->weight('bold')->size(Tables\Columns\TextColumn\TextColumnSize::Large),
                    Split::make([
                        Tables\Columns\TextColumn::make('start_time')
                            ->icon('heroicon-m-clock')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d/m H:i'))
                            ->badge()->color('info')->grow(false),

                        Tables\Columns\TextColumn::make('plazas')
                            ->state(function (GymSession $record) {
                                $ocupadas = $record->bookings()->where('status', 'booked')->count();
                                $espera = $record->bookings()->where('status', 'waiting')->count();
                                $libres = max(0, $record->capacity - $ocupadas);

                                if ($libres > 0) return "{$libres} de {$record->capacity} libres";
                                if ($espera < 5) return "Lleno ({$espera}/5 espera)";
                                return "100% Completo";
                            })
                            ->badge()
                            ->color(fn($state) => str_contains($state, 'libres') ? 'success' : (str_contains($state, 'Completo') ? 'danger' : 'warning'))
                            ->alignEnd(),
                    ]),
                ])->space(3),
            ])
            ->actions([
                Tables\Actions\Action::make('apuntarse')
                    ->label(function (GymSession $record) {
                        $user = Auth::user();
                        if (!$user->activePackages()->exists()) return 'Sin Clases';

                        $apertura = Carbon::parse($record->start_time)->subHours(12);
                        if (now()->lessThan($apertura)) return 'Abre ' . $apertura->format('H:i');

                        $ocupadas = $record->bookings()->where('status', 'booked')->count();
                        $espera = $record->bookings()->where('status', 'waiting')->count();

                        if (($ocupadas + $espera) < $record->capacity) return '¡Apuntarse!';
                        if ($espera < 5) return 'Entrar en Espera';
                        return 'Aforo Completo';
                    })
                    ->button()
                    ->color(function (GymSession $record) {
                        $user = Auth::user();
                        if (!$user->activePackages()->exists()) return 'danger';

                        $apertura = Carbon::parse($record->start_time)->subHours(12);
                        if (now()->lessThan($apertura)) return 'gray';

                        $ocupadas = $record->bookings()->where('status', 'booked')->count();
                        $espera = $record->bookings()->where('status', 'waiting')->count();

                        if (($ocupadas + $espera) < $record->capacity) return 'success';
                        if ($espera < 5) return 'warning';
                        return 'gray';
                    })
                    ->disabled(function (GymSession $record) {
                        $user = Auth::user();
                        if (!$user->activePackages()->exists()) return true;

                        $apertura = Carbon::parse($record->start_time)->subHours(12);
                        if (now()->lessThan($apertura)) return true;

                        $ocupadas = $record->bookings()->where('status', 'booked')->count();
                        $espera = $record->bookings()->where('status', 'waiting')->count();
                        return $ocupadas >= $record->capacity && $espera >= 5;
                    })
                    ->visible(fn($record) => !$record->bookings()->where('user_id', Auth::id())->exists())
                    ->action(function (GymSession $record) {
                        $user = Auth::user();
                        $package = $user->activePackages()->orderBy('expires_at', 'asc')->first();

                        if (!$package) {
                            Notification::make()->title('No tienes pases activos.')->danger()->send();
                            return;
                        }

                        // Lógica Sincronizada con BD
                        if ($package->type === 'tarifa') {
                            $sessionDate = Carbon::parse($record->start_time);
                            $limitAmount = (int) $package->limit_amount;
                            
                            if ($limitAmount > 0) {
                                $reservas = $user->bookings()->where('status', 'booked')
                                    ->whereHas('gymSession', function ($q) use ($sessionDate, $package) {
                                        if ($package->limit_type === 'semanal') {
                                            $q->whereBetween('start_time', [$sessionDate->copy()->startOfWeek(), $sessionDate->copy()->endOfWeek()]);
                                        } elseif ($package->limit_type === 'mensual') {
                                            $q->whereBetween('start_time', [$sessionDate->copy()->startOfMonth(), $sessionDate->copy()->endOfMonth()]);
                                        } elseif ($package->limit_type === 'anual') {
                                            $q->whereBetween('start_time', [$sessionDate->copy()->startOfYear(), $sessionDate->copy()->endOfYear()]);
                                        }
                                    })->count();

                                if ($reservas >= $limitAmount) {
                                    Notification::make()->title("Has alcanzado tu límite {$package->limit_type} de {$limitAmount} clases.")->warning()->send();
                                    return;
                                }
                            }
                        } elseif ($package->type === 'bono' && $package->remaining_credits <= 0) {
                            Notification::make()->title('No te quedan clases en tu bono.')->danger()->send();
                            return;
                        }

                        DB::transaction(function () use ($record, $user, $package) {
                            $session = GymSession::lockForUpdate()->find($record->id);
                            if (!$session || $session->bookings()->where('user_id', $user->id)->exists()) return;

                            $ocupadas = $session->bookings()->where('status', 'booked')->count();
                            $espera = $session->bookings()->where('status', 'waiting')->count();
                            $gymId = Filament::getTenant()?->id ?? $user->gyms->first()->id;

                            if (($ocupadas + $espera) < $session->capacity) {
                                $booking = new Booking();
                                $booking->user_id = $user->id;
                                $booking->gym_session_id = $session->id;
                                $booking->status = 'booked';
                                $booking->gym_id = $gymId;

                                if (\Schema::hasColumn('bookings', 'user_package_id')) {
                                    $booking->user_package_id = $package->id;
                                }

                                if (!$booking->save()) {
                                    return; 
                                }

                                $this->dispatch('credits-updated');
                                $gym = Filament::getTenant() ?? $user->gyms->first();
                                $settings = $gym ? \App\Models\GymSetting::where('gym_id', $gym->id)->first() : null;
                                $logoUrl = ($settings && $settings->logo) ? asset('storage/' . $settings->logo) : asset('images/dcien-logo.png');

                                Notification::make()
                                    ->title('¡Reserva Confirmada!')
                                    ->success()
                                    ->body(new \Illuminate\Support\HtmlString('
                                        <div class="flex flex-col items-center justify-center mt-6 mb-2">
                                            <div class="p-3 bg-gray-900 dark:bg-black rounded-xl shadow-2xl ring-1 ring-white/10 text-center">
                                                <img src="' . $logoUrl . '" alt="Logo del Box" class="h-20 w-auto object-contain mx-auto">
                                            </div>
                                            <p class="mt-5 text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest text-center">
                                                &#161;Plaza asegurada! 🔥
                                            </p>
                                        </div>
                                    '))
                                    ->duration(5000)
                                    ->send();
                            } elseif ($espera < 5) {
                                Booking::create(['user_id' => $user->id, 'gym_session_id' => $session->id, 'status' => 'waiting', 'gym_id' => $gymId]);
                                Notification::make()->title('Entras en Lista de Espera.')->warning()->send();
                            }
                        });
                    }),

                Tables\Actions\Action::make('aceptar_plaza')
                    ->label('¡Aceptar Plaza!')
                    ->button()->color('success')->icon('heroicon-m-check-badge')
                    ->visible(fn($record) => $record->bookings()->where('user_id', Auth::id())->where('status', 'waiting')->exists() && $record->bookings()->where('status', 'booked')->count() < $record->capacity)
                    ->action(function (GymSession $record) {
                        $user = Auth::user();
                        $package = $user->activePackages()->first();

                        if (!$package) {
                            Notification::make()->title('No tienes pases para confirmar.')->danger()->send();
                            return;
                        }

                        DB::transaction(function () use ($record, $user, $package) {
                            $session = GymSession::lockForUpdate()->find($record->id);
                            if (!$session) return;

                            $booking = $session->bookings()->where('user_id', $user->id)->where('status', 'waiting')->first();
                            if ($booking) {
                                $booking->status = 'booked';
                                if (\Schema::hasColumn('bookings', 'user_package_id')) {
                                    $booking->user_package_id = $package->id;
                                }
                                
                                if (!$booking->save()) return;

                                if ($package->type === 'bono') {
                                    $package->decrement('remaining_credits');
                                }

                                $this->dispatch('credits-updated');
                                Notification::make()->title('¡Plaza tuya!')->success()->send();
                            }
                        });
                    }),

                Tables\Actions\Action::make('desapuntarse')
                    ->label('Cancelar Reserva')
                    ->button()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function (GymSession $record) {
                        $user = Auth::user();
                        if (!$user) return false;
                        
                        $booking = $record->bookings()->where('user_id', $user->id)->whereIn('status', ['booked', 'waiting'])->first();
                        if (!$booking) return false;
                        
                        // Solo bloqueamos la cancelación en la última hora para plazas confirmadas
                        if ($booking->status === 'booked' && $record->start_time && now()->greaterThanOrEqualTo(Carbon::parse($record->start_time)->subMinutes(60))) {
                            return false;
                        }
                        
                        return true;
                    })
                    ->action(function (GymSession $record) {
                        $user = Auth::user();
                        $booking = $record->bookings()->where('user_id', $user->id)->whereIn('status', ['booked', 'waiting'])->first();
                        if ($booking && $booking->delete()) {
                            $this->dispatch('credits-updated');
                            Notification::make()->title('Reserva cancelada')->danger()->send();
                        }
                    }),
            ]);
    }
}