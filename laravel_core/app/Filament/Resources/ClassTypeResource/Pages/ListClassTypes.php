<?php

namespace App\Filament\Resources\ClassTypeResource\Pages;

use App\Filament\Resources\ClassTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassTypes extends ListRecords
{
    protected static string $resource = ClassTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
