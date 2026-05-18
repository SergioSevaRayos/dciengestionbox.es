<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\GymSession;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;

class DashboardWodWidget extends Widget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.dashboard-wod-widget';

    protected function getViewData(): array
    {
        $gymId = Filament::getTenant()?->id;
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $missingWods = GymSession::with('classType')
            ->where('gym_id', $gymId)
            ->whereBetween('start_time', [$today, $tomorrow->copy()->endOfDay()])
            ->where(function($q) {
                $q->whereNull('workout_id')->orWhere('workout_id', '');
            })
            ->orderBy('start_time')
            ->get();

        $todaysWods = GymSession::with('classType')
            ->where('gym_id', $gymId)
            ->whereDate('start_time', $today)
            ->whereNotNull('workout_id')
            ->where('workout_id', '!=', '')
            ->get()
            ->unique('workout_id');

        return [
            'missingWods' => $missingWods,
            'todaysWods' => $todaysWods,
        ];
    }
}
