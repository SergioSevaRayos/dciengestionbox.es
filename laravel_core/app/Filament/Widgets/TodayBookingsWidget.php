<?php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\GymSession;
use Carbon\Carbon;

class TodayBookingsWidget extends Widget
{
    protected static ?int $sort = 3;
    protected static string $view = 'filament.admin.widgets.today-bookings';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '10s';

    public function getSessions(): array
    {
        $now = Carbon::now();

        return GymSession::query()
            ->whereDate('start_time', Carbon::today())
            ->orderBy('start_time', 'asc')
            ->with(['classType', 'bookings.user'])
            ->get()
            ->map(function ($session) use ($now) {
                $booked = $session->bookings->where('status', 'booked');
                $users  = $booked->map(fn($b) => $b->user)->filter()->values();

                return [
                    'id'          => $session->id,
                    'name'        => $session->classType?->name ?? 'Sin tipo',
                    'time'        => Carbon::parse($session->start_time)->format('H:i'),
                    'capacity'    => $session->capacity,
                    'booked'      => $booked->count(),
                    'full'        => $booked->count() >= $session->capacity,
                    'past'        => Carbon::parse($session->start_time)->lt($now),
                    'edit_url'    => \App\Filament\Resources\GymSessionResource::getUrl('edit', ['record' => $session]),
                    'users'       => $users->map(fn($u) => [
                        'name'   => $u->name,
                        'avatar' => $u->avatar ? asset('storage/' . $u->avatar) : null,
                    ])->toArray(),
                ];
            })
            ->toArray();
    }
}
