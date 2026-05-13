<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('document:expiry')->dailyAt("01:00");
Schedule::command('annual:declaration')->dailyAt("03:00");