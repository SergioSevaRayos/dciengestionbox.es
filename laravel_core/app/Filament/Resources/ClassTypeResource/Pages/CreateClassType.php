<?php

namespace App\Filament\Resources\ClassTypeResource\Pages;

use App\Filament\Resources\ClassTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClassType extends CreateRecord
{
    protected static string $resource = ClassTypeResource::class;

    // 1. Personalizamos el título principal de esta pantalla
    protected static ?string $title = 'Añadir Nueva Disciplina';
    // 2. Truco UX: Al guardar, devolvemos al usuario a la tabla principal
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
