<?php

namespace App\Filament\Widgets;

use App\Models\GymSession;
use App\Filament\Resources\GymSessionResource;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Livewire\Attributes\On;

class GymCalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected string $pollingInterval = '5s';

    #[On('credits-updated')]
    public function refreshCalendar() 
    {
        $this->refreshEvents();
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return GymSession::with('classType')
            ->whereBetween('start_time', [$fetchInfo['start'], $fetchInfo['end']])
            ->get()
            ->map(function (GymSession $session) {
                $startTime = \Carbon\Carbon::parse($session->start_time)->format('H:i');

                return [
                    'id' => $session->id,
                    'title' => $session->classType->name,
                    'start' => $session->start_time->toIso8601String(),
                    'end' => $session->end_time->toIso8601String(),
                    'backgroundColor' => $session->classType->color ?? '#3b82f6',
                    'borderColor' => 'transparent',
                    'allDay' => false,
                    'display' => 'block',
                    'url' => GymSessionResource::getUrl('edit', ['record' => $session]),
                ];
            })
            ->toArray();
    }

    public function config(): array
    {
        return [
            'initialView' => 'timeGridDay',
            'locale' => 'es',
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'timeGridDay,timeGridWeek,listWeek,dayGridMonth',
            ],
            'eventDisplay' => 'block',
            'dayMaxEvents' => 3,
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'meridiem' => false,
                'hour12' => false
            ],
            'slotMinTime' => '06:00:00',
            'slotMaxTime' => '24:00:00',
            'allDaySlot' => false,
            'displayEventTime' => false,
            'expandRows' => true,
            'views' => [
                'timeGridWeek' => ['dayHeaderFormat' => ['weekday' => 'short', 'day' => 'numeric']],
            ],
            'height' => 'auto',
        ];
    }
}
