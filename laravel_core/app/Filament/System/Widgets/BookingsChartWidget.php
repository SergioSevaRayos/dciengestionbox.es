<?php
namespace App\Filament\System\Widgets;
use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class BookingsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Tráfico del Sistema (Reservas Diarias)';
    protected static ?string $pollingInterval = '15s';
    protected static ?int $sort = 2; // Mismo sort que el donut
    protected int | string | array $columnSpan = 1; 

    protected function getData(): array
    {
        $data = []; $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->translatedFormat('d M'); 
            $data[] = Booking::whereDate('created_at', $date->toDateString())->count();
        }
        return ['datasets' => [['label' => 'Nuevas Reservas', 'data' => $data, 'borderColor' => '#10b981', 'tension' => 0.4]], 'labels' => $labels];
    }
    protected function getType(): string { return 'line'; }
}
