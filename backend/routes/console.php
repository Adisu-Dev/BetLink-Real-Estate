<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: expire old listings daily
Schedule::command('properties:expire')->daily();

// Schedule: send appointment reminders every hour
Schedule::command('appointments:reminders')->hourly();
