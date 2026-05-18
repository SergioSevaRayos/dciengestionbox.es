<?php

namespace App\Filament\Resources\PackageRenewalResource\Pages;

use App\Filament\Resources\PackageRenewalResource;
use Filament\Resources\Pages\ListRecords;

class ListPackageRenewals extends ListRecords
{
    protected static string $resource = PackageRenewalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No dejamos que el admin cree peticiones manuales desde aquí, solo alumnos
        ];
    }
}
