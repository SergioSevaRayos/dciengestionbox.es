<?php

namespace App\Filament\Resources\GymScheduleResource\Pages;

use App\Filament\Resources\GymScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGymSchedules extends ListRecords
{
    protected static string $resource = GymScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}