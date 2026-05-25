<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ViewCompetition extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'view-competition/{record}';
    protected static string $view = 'filament.pages.view-competition';

    public function getTitle(): string
    {
        return 'Ver Competición';
    }
}
