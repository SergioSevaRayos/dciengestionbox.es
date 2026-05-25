<?php
namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Foto de perfil')->schema([
                FileUpload::make('avatar')
                    ->label('')
                    ->image()
                    ->avatar()
                    ->directory('avatars')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->columnSpanFull(),
            ]),

            Section::make('Datos personales')->schema([
                TextInput::make('name')
                    ->label('Nombre completo')->required(),
                TextInput::make('email')
                    ->label('Email')->email()->required()->unique(ignoreRecord: true),
            ])->columns(2),

            Section::make('Cambiar contraseña')
                ->description('Déjalo vacío si no quieres cambiarla.')
                ->schema([
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                ])->columns(2),
        ]);
    }
}
