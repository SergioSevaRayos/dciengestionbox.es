<?php

namespace App\Filament\App\Widgets;

use App\Models\PersonalRecord;
use App\Models\Exercise;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class DashboardProgressStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $tenant = Filament::getTenant();

        if (!$tenant) {
            return [];
        }

        // Filtramos ejercicios SOLO del gimnasio actual
        $exercises = Exercise::where('gym_id', $tenant->id)->get();
        $stats = [];

        // Construimos la URL correcta con el slug del gimnasio
        $url = "/app/{$tenant->slug}/mis-marcas";

        foreach ($exercises as $exercise) {
            $records = PersonalRecord::where('user_id', $user->id)
                ->where('exercise_id', $exercise->id)
                ->where('gym_id', $tenant->id)
                ->orderBy('achieved_at', 'asc')
                ->get();

            if ($records->isNotEmpty()) {
                $bestWeight = $records->max('weight');
                $nameStr = $exercise->name;

                $chartData = $records->pluck('weight')->map(fn($w) => (float) $w)->toArray();

                $firstWeight = $chartData[0] ?? 0;
                $lastWeight = end($chartData) ?: 0;
                $isImproving = $lastWeight >= $firstWeight;

                $color = $isImproving ? 'success' : 'danger';
                $icon = $isImproving ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

                $stats[] = Stat::make((string) $nameStr, $bestWeight . ' Kg')
                    ->description('Curva de progreso')
                    ->descriptionIcon($icon)
                    ->color($color)
                    ->chart($chartData)
                    ->url($url) // Usamos la función nativa de Filament para el enlace
                    ->extraAttributes([
                        'class' => 'cursor-pointer hover:ring-2 hover:ring-' . $color . '-500 transition-all duration-200',
                    ]);
            }
        }

        return $stats;
    }
}
