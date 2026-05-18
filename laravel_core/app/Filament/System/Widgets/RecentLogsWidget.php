<?php
namespace App\Filament\System\Widgets;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\File;

class RecentLogsWidget extends Widget
{
    protected static ?string $pollingInterval = '5s';
    protected static string $view = 'filament.system.widgets.recent-logs-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function getLogsProperty(): array
    {
        $logFile = storage_path('logs/laravel.log');
        if (!File::exists($logFile)) return [];
        $lines = file($logFile); $errors = [];
        foreach (array_reverse($lines) as $line) {
            if (str_contains($line, '.ERROR:')) {
                $errors[] = trim(preg_replace('/\{"exception".*/s', '', $line));
            }
            if (count($errors) >= 5) break; 
        }
        return $errors;
    }
}
