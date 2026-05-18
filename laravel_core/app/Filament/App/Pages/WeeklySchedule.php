<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use App\Models\GymSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WeeklySchedule extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Horarios';
    protected static ?string $title = 'Próximos 7 Días';
    protected static ?string $slug = 'horarios';
    protected static ?int $navigationSort = 2;
    
    protected static string $view = 'filament.app.pages.weekly-schedule';

    public function getClassesByDayProperty()
    {
        $user = Auth::user();
        if (!$user) return collect();

        // Obtenemos los IDs de los gimnasios vinculados al usuario desde la tabla intermedia
        $gymIds = DB::table('gym_user')
            ->where('user_id', $user->id)
            ->pluck('gym_id')
            ->toArray();

        if (empty($gymIds)) return collect();

        $start = now()->startOfDay();
        $end = now()->addDays(6)->endOfDay();

        $sessions = GymSession::with(['bookings', 'classType'])
            ->whereIn('gym_id', $gymIds) // Buscamos en todos sus gimnasios
            ->whereBetween('start_time', [$start, $end])
            ->orderBy('start_time')
            ->get();

        return $sessions->groupBy(function($session) {
            return Carbon::parse($session->start_time)->format('Y-m-d');
        });
    }
}
