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
        
        $handle = fopen($logFile, 'r');
        if (!$handle) return [];

        $errors = [];
        $lineBuffer = '';
        
        fseek($handle, 0, SEEK_END);
        $pos = ftell($handle);

        $chunkSize = 4096;
        while ($pos > 0 && count($errors) < 5) {
            $readSize = min($pos, $chunkSize);
            $pos -= $readSize;
            fseek($handle, $pos, SEEK_SET);
            $chunk = fread($handle, $readSize);
            
            $chunk .= $lineBuffer;
            $lines = explode("\n", $chunk);
            
            if ($pos > 0) {
                $lineBuffer = array_shift($lines);
            } else {
                $lineBuffer = '';
            }

            for ($i = count($lines) - 1; $i >= 0; $i--) {
                $line = $lines[$i];
                if (str_contains($line, '.ERROR:')) {
                    $errors[] = trim(preg_replace('/\{"exception".*/s', '', $line));
                    if (count($errors) >= 5) {
                        break 2;
                    }
                }
            }
        }
        fclose($handle);
        return $errors;
    }
}
