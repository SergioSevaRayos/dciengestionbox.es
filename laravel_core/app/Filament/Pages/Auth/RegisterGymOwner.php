<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use App\Models\Gym;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterGymOwner extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        TextInput::make('gym_name')
                            ->label('Nombre de tu Gimnasio')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function handleRegistration(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // 1. Crear Usuario
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'gestor',
            ]);

            // 2. Crear Gimnasio
            $gym = Gym::create([
                'name' => $data['gym_name'],
                'slug' => Str::slug($data['gym_name'] . '-' . Str::random(5)),
                'is_subscribed' => false,
            ]);

            // 3. Vincular
            $user->gyms()->attach($gym->id);
            
            return $user;
        });
    }

    protected function getRedirectUrl(): string
    {
        return "/admin"; 
    }
}
