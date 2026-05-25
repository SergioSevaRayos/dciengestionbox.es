<?php
namespace App\Filament\App\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
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
            ])->columns(1),

            Section::make('Datos personales')->schema([
                TextInput::make('name')
                    ->label('Nombre completo')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel(),
                DatePicker::make('birth_date')
                    ->label('Fecha de nacimiento')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
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
