<?php
namespace App\Filament\System\Pages;

use Filament\Pages\Page;

class LogsAsincronos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationGroup = 'Auditoría e Historial';
    protected static ?string $title = 'Logs en Tiempo Real';
    protected static string $view = 'filament.pages.log-redirect';
    
    public function mount()
    {
        return redirect('/log-viewer');
    }
}
