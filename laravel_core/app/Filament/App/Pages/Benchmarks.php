<?php

namespace App\Filament\App\Pages;

use App\Models\BenchmarkRecord;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;

class Benchmarks extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static ?string $navigationGroup = 'Entrenamiento';
    protected static string $view = 'filament.app.pages.benchmarks';
    protected static ?string $navigationLabel = 'Benchmarks';
    protected static ?string $title = 'Benchmarks de Referencia';

    protected function getViewData(): array
    {
        $heroes = []; $girls = []; $especiales = []; $qualifiers = [];

        $pathH = storage_path('app/benchmarks/heroes.json');
        if (file_exists($pathH)) $heroes = json_decode(file_get_contents($pathH), true) ?? [];

        $pathG = storage_path('app/benchmarks/girls.json');
        if (file_exists($pathG)) $girls = json_decode(file_get_contents($pathG), true) ?? [];

        $pathE = storage_path('app/benchmarks/especiales.json');
        if (file_exists($pathE)) $especiales = json_decode(file_get_contents($pathE), true) ?? [];

        $pathQ = storage_path('app/benchmarks/qualifiers.json');
        if (file_exists($pathQ)) $qualifiers = json_decode(file_get_contents($pathQ), true) ?? [];
        
        $completedWods = BenchmarkRecord::where('user_id', auth()->id())->pluck('wod_name')->toArray();

        return [
            'heroes' => $heroes,
            'girls' => $girls,
            'especiales' => $especiales,
            'qualifiers' => $qualifiers,
            'completedWods' => $completedWods,
        ];
    }

    public function registrarMarcaAction(): Action
    {
        return Action::make('registrarMarca')
            ->modalHeading(fn (array $arguments) => 'Nueva Marca: ' . ($arguments['wod'] ?? ''))
            ->modalSubmitActionLabel('Guardar Marca')
            ->slideOver()
            ->form([
                Grid::make(2)->schema([
                    TextInput::make('score')->label('Resultado')->required(),
                    Select::make('modality')->label('Modalidad')->options(['Rx' => 'Rx', 'Scaled' => 'Scaled'])->default('Rx')->required(),
                ]),
                DatePicker::make('date')->label('Fecha del WOD')->default(now())->required(),
                Textarea::make('notes')->label('Notas / Sensaciones (Opcional)')->columnSpanFull(),
            ])
            ->action(function (array $data, array $arguments) {
                BenchmarkRecord::create([
                    'user_id' => auth()->id(),
                    'wod_name' => $arguments['wod'],
                    'score' => $data['score'],
                    'modality' => $data['modality'],
                    'date' => $data['date'],
                    'notes' => $data['notes'] ?? null,
                ]);
                Notification::make()->title('¡Marca registrada!')->success()->send();
            });
    }

    public function verHistorialAction(): Action
    {
        return Action::make('verHistorial')
            ->modalHeading(fn (array $arguments) => 'Historial: ' . ($arguments['wod'] ?? ''))
            ->slideOver()->modalWidth('lg')->modalSubmitAction(false) 
            ->modalContent(function (array $arguments) {
                $records = BenchmarkRecord::where('user_id', auth()->id())
                    ->where('wod_name', $arguments['wod'])
                    ->orderBy('date', 'desc')->get();
                return view('filament.app.components.benchmark-history', ['records' => $records]);
            });
    }

    public function borrarMarca($id)
    {
        BenchmarkRecord::find($id)?->delete();
        Notification::make()->title('Marca eliminada')->danger()->send();
    }
}
