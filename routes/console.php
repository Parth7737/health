<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('document:expiry')->dailyAt("01:00");
Schedule::command('annual:declaration')->dailyAt("03:00");
Schedule::command('hr:provision-leave-balances')->yearlyOn(1, 1, '0:30');