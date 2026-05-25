<?php

namespace App\Filament\App\Pages;

use App\Models\ArenaCompetition;
use App\Models\ArenaResult;
use Filament\Pages\Page;
use Filament\Facades\Filament;

class Arena extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Entrenamiento';
    protected static string $view = 'filament.app.pages.arena';
    protected static ?string $navigationLabel = 'Arena';
    protected static ?string $title = 'Arena';
    protected static ?int $navigationSort = 5;

    public ?int $activeCompetitionId = null;
    public ?int $activeWodId = null;
    public string $activeFormat = 'all';

    public static function shouldRegisterNavigation(): bool
    {
        try {
            $gym = Filament::getTenant();
            return $gym?->settings?->enable_arena ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function mount(): void
    {
        $gym = Filament::getTenant();

        $competition = ArenaCompetition::where(function($q) use ($gym) {
                $q->where('gym_id', $gym->id)->orWhere('type', 'open');
            })
            ->where('is_active', true)
            ->with('wods')
            ->latest('start_date')
            ->first();

        $this->activeCompetitionId = $competition?->id;
        $this->activeWodId = $competition?->wods->first()?->id;
    }

    public function selectCompetition(int $id): void
    {
        $this->activeCompetitionId = $id;
        $competition = ArenaCompetition::with('wods')->find($id);
        $this->activeWodId = $competition?->wods->first()?->id;
    }

    public function selectWod(int $id): void
    {
        $this->activeWodId = $id;
    }

    public function selectFormat(string $format): void
    {
        $this->activeFormat = $format;
    }

    protected function getViewData(): array
    {
        $gym = Filament::getTenant();

        $competitions = ArenaCompetition::where(function($q) use ($gym) {
                $q->where('gym_id', $gym->id)->orWhere('type', 'open');
            })
            ->where('is_active', true)
            ->with(['wods'])
            ->latest('start_date')
            ->get();

        $activeCompetition = $competitions->firstWhere('id', $this->activeCompetitionId)
            ?? $competitions->first();

        $activeWod   = null;
        $leaderboard = collect();

        if ($activeCompetition) {
            $this->activeCompetitionId = $activeCompetition->id;
            $activeWod = $activeCompetition->wods->firstWhere('id', $this->activeWodId)
                ?? $activeCompetition->wods->first();

            if ($activeWod) {
                $this->activeWodId = $activeWod->id;
                $results = ArenaResult::where('arena_wod_id', $activeWod->id)
                    ->with('team.members', 'team.gym')
                    ->get();

                $leaderboard = $results->sortBy(function($r) use ($activeWod) {
                    $v = $activeWod->resultToSortValue($r->result_value);
                    return $activeWod->lowerIsBetter() ? $v : -$v;
                })->values();
            }
        }

        $allTeams = $activeCompetition
            ? $activeCompetition->teams()
                ->with(['members', 'results', 'gym'])
                ->get()
                ->map(function($team) {
                    $team->total_points = $team->results->sum('points');
                    return $team;
                })
            : collect();

        $generalRanking = [
            'individual' => $allTeams->where('format', 'individual')->sortByDesc('total_points')->values(),
            'pairs'      => $allTeams->where('format', 'pairs')->sortByDesc('total_points')->values(),
            'team'       => $allTeams->where('format', 'team')->sortByDesc('total_points')->values(),
        ];

        return [
            'competitions'      => $competitions,
            'activeCompetition' => $activeCompetition,
            'activeWod'         => $activeWod,
            'leaderboard'       => $leaderboard,
            'generalRanking'    => $generalRanking,
            'currentGymId'      => $gym->id,
            'activeFormat'      => $this->activeFormat,
        ];
    }
}