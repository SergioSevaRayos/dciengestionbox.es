<?php
namespace App\Filament\System\Widgets;
use App\Models\Gym;
use App\Models\User;
use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    protected static ?int $sort = 1; 

    protected function getStats(): array
    {
        return [
            Stat::make('Gimnasios Activos', Gym::count())->description('Centros registrados')->color('primary'),
            Stat::make('Usuarios Registrados', User::count())->description('Total global')->color('success'),
            Stat::make('Reservas (30d)', Booking::where('created_at', '>=', now()->subDays(30))->count())->color('warning'),
        ];
    }
}
