<?php

namespace App\Console\Commands;

use App\Services\HrLeaveBalanceService;
use Illuminate\Console\Command;

class HrProvisionLeaveBalances extends Command
{
    protected $signature = 'hr:provision-leave-balances
                            {--year= : Calendar year (default: current year)}
                            {--hospital_id= : Limit to one hospital id}';

    protected $description = 'Provision staff leave balance rows for the year (paid leave types) and sync used days from approved requests';

    public function handle(HrLeaveBalanceService $service): int
    {
        $year = (int) ($this->option('year') ?: now()->year);
        $hospitalId = $this->option('hospital_id');

        $service->provisionAnnualBalancesForYear($year, $hospitalId !== null && $hospitalId !== '' ? (int) $hospitalId : null);

        $this->info('Leave balances provisioned for year ' . $year . '.');

        return self::SUCCESS;
    }
}
