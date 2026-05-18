<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;

class WorkoutTimer extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Temporizador';
    protected static ?string $title = 'Smart Timer';
    protected static ?string $navigationGroup = 'Entrenamiento';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.app.pages.workout-timer';
}
