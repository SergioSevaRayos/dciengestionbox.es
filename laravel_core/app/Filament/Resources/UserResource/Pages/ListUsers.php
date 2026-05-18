<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\TextInput;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // BOTÓN PARA VINCULAR ALUMNOS DE OTROS CENTROS
            Actions\Action::make('vincular_existente')
                ->label('Vincular Alumno Existente')
                ->color('warning')
                ->icon('heroicon-o-link')
                ->modalHeading('Vincular Alumno de otro Centro')
                ->modalDescription('Si el alumno ya está registrado en nuestra app por otro gimnasio, introduce su email para vincularlo al tuyo.')
                ->form([
                    TextInput::make('email')
                        ->label('Email del alumno')
                        ->email()
                        ->required()
                        ->placeholder('ejemplo@gmail.com')
                ])
                ->action(function (array $data) {
                    $user = User::where('email', $data['email'])->first();
                    $gym = Filament::getTenant();

                    // 1. Si el email no existe en toda la base de datos
                    if (!$user) {
                        Notification::make()
                            ->title('Usuario no encontrado')
                            ->body('No hay nadie registrado con este email. Usa el botón "Nuevo Alumno" para crearle una cuenta.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // 2. Si el usuario ya está en este gimnasio
                    if ($user->gyms()->where('gym_id', $gym->id)->exists()) {
                        Notification::make()
                            ->title('Ya está en tu gimnasio')
                            ->body('Este alumno ya forma parte de tu lista de usuarios.')
                            ->warning()
                            ->send();
                        return;
                    }

                    // 3. Vincular al gimnasio actual
                    $user->gyms()->attach($gym->id);

                    Notification::make()
                        ->title('¡Alumno vinculado!')
                        ->body('El usuario ha sido añadido a tu centro correctamente.')
                        ->success()
                        ->send();
                }),
                
            // EL BOTÓN NORMAL DE CREAR (Lo dejamos como estaba)
            Actions\CreateAction::make()->label('Nuevo Alumno'),
        ];
    }
}
