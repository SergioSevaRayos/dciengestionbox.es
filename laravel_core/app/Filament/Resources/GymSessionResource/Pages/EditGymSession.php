<?php
namespace App\Filament\Resources\GymSessionResource\Pages;

use App\Filament\Resources\GymSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use App\Models\GymSession;
use Illuminate\Support\Carbon;

class EditGymSession extends EditRecord
{
    protected static string $resource = GymSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // MAGIA: Acción de clonado inteligente
            Actions\Action::make('propagateWod')
                ->label('Propagar WOD')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Propagación Inteligente')
                ->modalDescription('Copia este entrenamiento a futuras clases. Selecciona la disciplina, los días y hasta cuándo quieres repetirlo.')
                ->form([
                    Forms\Components\Select::make('class_type_id')
                        ->label('Disciplina')
                        ->options(\App\Models\ClassType::pluck('name', 'id'))
                        ->default(fn () => $this->record->class_type_id)
                        ->required(),
                    Forms\Components\CheckboxList::make('days_of_week')
                        ->label('Días de la semana')
                        ->options([
                            1 => 'Lunes',
                            2 => 'Martes',
                            3 => 'Miércoles',
                            4 => 'Jueves',
                            5 => 'Viernes',
                            6 => 'Sábado',
                            0 => 'Domingo',
                        ])
                        // Por defecto marcamos el mismo día de la semana de la clase que estamos editando
                        ->default(fn () => [Carbon::parse($this->record->start_time)->dayOfWeek])
                        ->columns(3)
                        ->required(),
                    Forms\Components\DatePicker::make('repeat_until')
                        ->label('Propagar hasta la fecha (incluida)')
                        ->default(fn () => Carbon::parse($this->record->start_time)->addMonths(1)) // Por defecto 1 mes
                        ->minDate(fn () => Carbon::parse($this->record->start_time))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $daysOfWeek = $data['days_of_week'];
                    $repeatUntil = Carbon::parse($data['repeat_until'])->endOfDay();
                    $classTypeId = $data['class_type_id'];

                    // Buscamos las clases que encajen y las actualizamos
                    $updatedCount = GymSession::where('gym_id', $this->record->gym_id)
                        ->where('id', '!=', $this->record->id) // Excluir la actual
                        ->where('class_type_id', $classTypeId)
                        ->where('start_time', '>', $this->record->start_time)
                        ->where('start_time', '<=', $repeatUntil)
                        ->get()
                        ->filter(function ($session) use ($daysOfWeek) {
                            // Filtramos en PHP para que funcione en cualquier base de datos (MySQL, PostgreSQL, etc.)
                            return in_array(Carbon::parse($session->start_time)->dayOfWeek, $daysOfWeek);
                        })
                        ->each(function ($session) {
                            $session->update([
                                'workout_id' => $this->record->workout_id,
                                'is_workout_visible' => $this->record->is_workout_visible,
                            ]);
                        })
                        ->count();

                    \Filament\Notifications\Notification::make()
                        ->title("¡Éxito!")
                        ->body("WOD propagado correctamente a {$updatedCount} clases.")
                        ->success()
                        ->send();
                })
                // El botón solo sale si la clase actual tiene un entrenamiento guardado en la BD
                ->visible(fn () => !empty($this->record->workout_id)),

            Actions\DeleteAction::make(),
        ];
    }
}
