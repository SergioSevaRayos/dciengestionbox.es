<?php

namespace App\Filament\App\Widgets;

use App\Models\PersonalRecord;
use App\Models\Exercise;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class PersonalRecordChart extends ChartWidget
{
    protected static ?int $sort = 10;
    protected static ?string $heading = 'Evolución de Fuerza';
    protected static string $color = 'success';
    
    public ?string $filter = null;

    public static function canView(): bool
    {
        return request()->path() !== 'app';
    }

    protected function getFilters(): ?array
    {
        return collect(Exercise::all())->mapWithKeys(function ($exercise) {
            $label = $exercise->name instanceof \UnitEnum ? $exercise->name->value : $exercise->name;
            return [$exercise->id => $label];
        })->toArray();
    }

    #[On('record-updated')]
    public function refreshChart() {}

    // MAGIA: Escucha el clic de la tarjeta y actualiza su propio filtro
    #[On('update-chart-filter')]
    public function updateFilter($filter)
    {
        $this->filter = $filter;
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? Exercise::first()?->id;

        if (!$activeFilter) return ['datasets' => [], 'labels' => []];

        $records = PersonalRecord::where('user_id', Auth::id())
            ->where('exercise_id', $activeFilter)
            ->orderBy('achieved_at', 'asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Peso (kg)',
                    'data' => $records->pluck('weight')->map(fn($w) => (float) $w)->toArray(),
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $records->pluck('achieved_at')->map(fn($date) => $date->format('d/m/y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
