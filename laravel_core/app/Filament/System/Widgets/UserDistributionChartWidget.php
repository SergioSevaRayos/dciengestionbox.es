<?php
namespace App\Filament\System\Widgets;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserDistributionChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Usuarios';
    protected static ?string $pollingInterval = '15s';
    protected static ?int $sort = 2; // Mismo sort que el de reservas
    protected int | string | array $columnSpan = 1; 

    protected function getData(): array
    {
        return [
            'datasets' => [[
                'label' => 'Usuarios',
                'data' => [
                    User::where('role', 'super_admin')->count(),
                    User::where('role', 'admin')->count(),
                    User::where('role', 'student')->count()
                ],
                'backgroundColor' => ['#ef4444', '#f59e0b', '#10b981'],
            ]],
            'labels' => ['Super Admins', 'Admins Gym', 'Alumnos'],
        ];
    }
    protected function getType(): string { return 'doughnut'; }
}
