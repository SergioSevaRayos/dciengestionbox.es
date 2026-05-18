<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentLog;
use Carbon\Carbon;

class ClearOldPayments extends Command
{
    protected $signature = 'payments:clear-old';
    protected $description = 'Borra registros de pagos de más de 6 meses';

    public function handle()
    {
        $count = PaymentLog::where('created_at', '<', Carbon::now()->subMonths(6))->delete();
        $this->info("Limpieza completada: $count registros eliminados.");
    }
}
