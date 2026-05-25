<?php

namespace App\Filament\Resources\ArenaCompetitionResource\Pages;

use App\Filament\Resources\ArenaCompetitionResource;
use App\Models\ArenaCompetition;
use App\Models\ArenaTeam;
use App\Models\ArenaResult;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Facades\Filament;


class ManageArenaCompetition extends Page
{
    protected static string $resource = ArenaCompetitionResource::class;
    protected static string $view = 'filament.admin.pages.manage-arena-competition';

    public ArenaCompetition $record;
    public ?int $activeWodId = null;

    public function mount(ArenaCompetition $record): void
    {
        $this->record = $record;
        $this->activeWodId = $this->record->wods->first()?->id;
    }

    public function selectWod(int $wodId): void
    {
        $this->activeWodId = $wodId;
    }

    protected function getViewData(): array
    {
        $this->record->load(['wods', 'teams.members', 'teams.results']);
        $activeWod = $this->record->wods->firstWhere('id', $this->activeWodId);
        $leaderboard = [];

        if ($activeWod) {
            $results = ArenaResult::where('arena_wod_id', $activeWod->id)
                ->with('team.members', 'team.gym')
                ->get();

            $sorted = $results->sortBy(function($r) use ($activeWod) {
                $v = $activeWod->resultToSortValue($r->result_value);
                return $activeWod->lowerIsBetter() ? $v : -$v;
            })->values();

            // Calcular puntos: 1º = N equipos, 2º = N-1...
            $total = $sorted->count();
            foreach ($sorted as $i => $result) {
                $result->rank_in_wod = $i + 1;
                $result->points = max(1, $total - $i);
                $result->save();
                $leaderboard[] = $result;
            }
        }

        // Ranking general: suma de puntos por equipo
        $generalRanking = $this->record->teams()
            ->with(['members', 'results', 'gym'])
            ->get()
            ->map(function($team) {
                $team->total_points = $team->results->sum('points');
                return $team;
            })
            ->sortByDesc('total_points')
            ->values();

        return [
            'competition'    => $this->record,
            'activeWod'      => $activeWod,
            'leaderboard'    => $leaderboard,
            'generalRanking' => $generalRanking,
            'gymUsers'       => User::where('gym_id', $this->record->gym_id)->orderBy('name')->get(),
        ];
    }

    public function crearEquipoAction(): Action
    {
        return Action::make('crearEquipo')
            ->label('+ Inscribir equipo')
            ->icon('heroicon-o-user-plus')
            ->color('primary')
            ->slideOver()
            ->modalHeading('Inscribir participante / equipo')
            ->form(function() {
                $gymUsers = User::where('gym_id', $this->record->gym_id)
                    ->orderBy('name')->pluck('name', 'id');
                return [
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre del equipo / atleta')->required(),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('format')
                            ->label('Formato')
                            ->options([
                                'individual' => 'Individual',
                                'pairs'      => 'Parejas',
                                'team'       => 'Equipo (3-4)',
                            ])
                            ->default('individual')->required()
                            ->live(),
                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'male'   => 'Masculino',
                                'female' => 'Femenino',
                                'mixed'  => 'Mixto',
                            ])
                            ->default('male')->required(),
                    ]),
                    Forms\Components\Section::make('Atletas')->schema([
                        Forms\Components\Select::make('athlete_1')
                            ->label('Atleta 1')->options($gymUsers)->searchable()->required(),
                        Forms\Components\Select::make('athlete_2')
                            ->label('Atleta 2')->options($gymUsers)->searchable()
                            ->visible(fn(Forms\Get $get) => in_array($get('format'), ['pairs','team'])),
                        Forms\Components\Select::make('athlete_3')
                            ->label('Atleta 3')->options($gymUsers)->searchable()
                            ->visible(fn(Forms\Get $get) => $get('format') === 'team'),
                        Forms\Components\Select::make('athlete_4')
                            ->label('Atleta 4')->options($gymUsers)->searchable()
                            ->visible(fn(Forms\Get $get) => $get('format') === 'team'),
                    ]),
                ];
            })
            ->action(function(array $data) {
                $team = ArenaTeam::create([
                    'arena_competition_id' => $this->record->id,
                    'gym_id'   => $this->record->gym_id,
                    'name'     => $data['name'],
                    'format'   => $data['format'],
                    'category' => $data['category'],
                ]);

                $athletes = array_filter([
                    $data['athlete_1'] ?? null,
                    $data['athlete_2'] ?? null,
                    $data['athlete_3'] ?? null,
                    $data['athlete_4'] ?? null,
                ]);

                foreach (array_values($athletes) as $i => $userId) {
                    $team->members()->attach($userId, ['position' => $i + 1]);
                }

                Notification::make()->title('Equipo inscrito')->success()->send();
            });
    }

    public function registrarResultadoAction(): Action
    {
        return Action::make('registrarResultado')
            ->label('+ Registrar resultado')
            ->icon('heroicon-o-pencil-square')
            ->color('warning')
            ->slideOver()
            ->modalHeading('Registrar resultado')
            ->form(function() {
                $teams = $this->record->teams()->with('members')->get()
                    ->mapWithKeys(fn($t) => [
                        $t->id => $t->name . ' (' . $t->members->pluck('name')->join(', ') . ')'
                    ]);
                $wods = $this->record->wods()->where('is_active', true)
                    ->get()->mapWithKeys(fn($w) => [$w->id => $w->name . ' — ' . $w->getScoreTypeLabel()]);
                return [
                    Forms\Components\Select::make('arena_team_id')
                        ->label('Equipo')->options($teams)->searchable()->required(),
                    Forms\Components\Select::make('arena_wod_id')
                        ->label('WOD')->options($wods)
                        ->default($this->activeWodId)->required(),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('result_value')
                            ->label('Resultado')
                            ->helperText('Tiempo: 12:30 · AMRAP: 5+12 · Peso/Reps: número')
                            ->required(),
                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->options(['rx_plus' => 'Rx+', 'rx' => 'Rx', 'scaled' => 'Scaled'])
                            ->default('rx')->required(),
                    ]),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas (opcional)')->rows(2),
                ];
            })
            ->action(function(array $data) {
                ArenaResult::updateOrCreate(
                    ['arena_team_id' => $data['arena_team_id'], 'arena_wod_id' => $data['arena_wod_id']],
                    ['result_value' => $data['result_value'], 'category' => $data['category'],
                     'notes' => $data['notes'] ?? null, 'is_verified' => true]
                );
                Notification::make()->title('Resultado guardado')->success()->send();
            });
    }

    public function eliminarEquipo(int $teamId): void
    {
        ArenaTeam::find($teamId)?->delete();
        Notification::make()->title('Equipo eliminado')->danger()->send();
    }

    public function eliminarResultado(int $resultId): void
    {
        ArenaResult::find($resultId)?->delete();
        Notification::make()->title('Resultado eliminado')->danger()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('← Volver')
                ->url(ArenaCompetitionResource::getUrl('index'))
                ->color('gray'),
            $this->crearEquipoAction(),
            $this->registrarResultadoAction(),
        ];
    }
}
