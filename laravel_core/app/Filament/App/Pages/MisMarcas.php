<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\PersonalRecordChart;
use App\Models\Exercise;
use App\Models\PersonalRecord;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class MisMarcas extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Entrenamiento';
    protected static string $view = 'filament.app.pages.mis-marcas';
    protected static ?string $title = 'Mis Marcas Personales';

    public ?int $selectedExerciseId = null;

    public function getOverviewDataProperty()
    {
        $user = Auth::user();
        $gymId = Filament::getTenant()->id;

        return collect(Exercise::withoutGlobalScopes()->get()->map(function ($exercise) use ($user, $gymId) {
            $bestWeight = PersonalRecord::where('user_id', $user->id)
                ->where('exercise_id', $exercise->id)
                ->where('gym_id', $gymId)
                ->max('weight') ?? 0;

            $nameStr = $exercise->name;
            $icon = 'heroicon-m-bolt';

            return (object) [
                'exercise' => $exercise,
                'exercise_id' => $exercise->id,
                'exercise_name' => (string) $nameStr,
                'icon_name' => $icon,
                'weight' => (float) $bestWeight,
                'max_weight' => (float) $bestWeight,
                'max' => $bestWeight > 0 ? (float)$bestWeight + 20 : 100,
            ];
        }))->filter(fn ($item) => $item->weight > 0)->values();
    }

    public function getRecordDataProperty(): array
    {
        if (!$this->selectedExerciseId) {
            return ['history' => collect(), 'max' => 100, 'name' => '', 'weight' => 0, 'max_weight' => 0, 'icon_name' => 'heroicon-m-bolt', 'exercise' => null];
        }

        $user = Auth::user();
        $gymId = Filament::getTenant()->id;
        $exercise = Exercise::withoutGlobalScopes()->find($this->selectedExerciseId);

        $history = PersonalRecord::where('user_id', $user->id)
            ->where('exercise_id', $this->selectedExerciseId)
            ->where('gym_id', $gymId)
            ->orderBy('achieved_at', 'desc')
            ->get();

        $bestWeight = $history->max('weight') ?? 0;

        return [
            'exercise' => $exercise,
            'name' => (string) $exercise->name,
            'icon_name' => 'heroicon-m-bolt',
            'max' => $bestWeight > 0 ? (float)$bestWeight : 100,
            'history' => $history,
            'weight' => (float)$bestWeight,
            'max_weight' => (float)$bestWeight,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nuevaMarca')
                ->label('Registrar Marca')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->form([
                    Select::make('exercise_id')
                        ->label('Ejercicio')
                        ->options(fn() => Exercise::withoutGlobalScopes()->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    TextInput::make('weight')
                        ->label('Peso (kg)')
                        ->numeric()
                        ->required()
                        ->prefix('Kg'),
                    DatePicker::make('achieved_at')
                        ->label('Fecha')
                        ->default(now())
                        ->required(),
                ])
                ->action(function (array $data) {
                    PersonalRecord::create([
                        'user_id' => Auth::id(),
                        'gym_id' => Filament::getTenant()->id, // ASIGNACIÓN DEL GIMNASIO
                        'exercise_id' => $data['exercise_id'],
                        'weight' => $data['weight'],
                        'achieved_at' => $data['achieved_at'],
                    ]);
                    Notification::make()->title('¡Récord guardado!')->success()->send();
                    $this->dispatch('record-updated');
                }),
        ];
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->icon('heroicon-m-pencil-square')
            ->color('gray')
            ->iconButton()
            ->form([
                TextInput::make('weight')->label('Peso (kg)')->numeric()->required()->prefix('Kg'),
                DatePicker::make('achieved_at')->label('Fecha')->required(),
            ])
            ->fillForm(fn (array $arguments) => PersonalRecord::find($arguments['record'])?->toArray())
            ->action(function (array $arguments, array $data) {
                $record = PersonalRecord::find($arguments['record']);
                if ($record && $record->user_id === Auth::id()) {
                    $record->update($data);
                    Notification::make()->title('Actualizado')->success()->send();
                    $this->dispatch('record-updated');
                }
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->iconButton()
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $record = PersonalRecord::find($arguments['record']);
                if ($record && $record->user_id === Auth::id()) {
                    $record->delete();
                    Notification::make()->title('Eliminado')->success()->send();
                    $this->dispatch('record-updated');
                }
            });
    }

    protected function getHeaderWidgets(): array { return [PersonalRecordChart::class]; }
    public function selectExercise($id) { $this->selectedExerciseId = $id; $this->dispatch('update-chart-filter', filter: $id); }
}
