<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule event reminders to be sent daily at 8:00 AM
Schedule::command('events:send-reminders')
    ->dailyAt('08:00')
    ->timezone(config('app.timezone'))
    ->description('Send reminder notifications for events happening today');
