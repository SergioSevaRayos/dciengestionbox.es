<?php
namespace App\Filament\App\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivePackagesWidget extends BaseWidget {
    protected function getStats(): array {
        $user = Auth::user();
        $packages = $user->activePackages()->with('package')->get();

        return $packages->map(function ($p) use ($user) {
            $daysLeft = round(now()->diffInDays($p->expires_at, false));
            $expirationLabel = $daysLeft < 0 ? "Caducado" : "Caduca en {$daysLeft} días";

            if ($p->type === 'tarifa') {
                $start = ($p->limit_type === 'mensual') ? now()->startOfMonth() : now()->startOfWeek();
                $end = ($p->limit_type === 'mensual') ? now()->endOfMonth() : now()->endOfWeek();

                $consumed = $user->bookings()
                    ->where('status', 'booked')
                    ->whereHas('gymSession', function ($q) use ($start, $end) {
                        $q->whereBetween('start_time', [$start, $end]);
                    })->count();

                $total = $p->limit_amount;
                $remaining = max(0, $total - $consumed);
                $periodo = ($p->limit_type === 'mensual') ? 'este mes' : 'esta semana';

                if ($total == 0) {
                    $label = "Clases Ilimitadas";
                    $description = "Has entrenado {$consumed} veces {$periodo}";
                } else {
                    $label = "{$remaining} de {$total} disponibles";
                    $description = "Llevas {$consumed} clases consumidas {$periodo}";
                }

                return Stat::make($p->package->name ?? 'Tarifa', $label)
                    ->description($description . " (" . $p->expires_at->format('d/m') . ")")
                    ->descriptionIcon('heroicon-m-calendar-days')
                    ->color($remaining > 0 ? 'success' : 'warning');
            } else {
                return Stat::make($p->package->name ?? 'Bono', "{$p->remaining_credits} clases restantes")
                    ->description($expirationLabel . " (" . $p->expires_at->format('d/m') . ")")
                    ->descriptionIcon('heroicon-m-ticket')
                    ->color($p->remaining_credits > 0 ? 'info' : 'danger');
            }
        })->toArray();
    }
}
