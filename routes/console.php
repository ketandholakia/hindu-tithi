<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('telegram:send-daily-panchang')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('telegram:send-reminders')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('panchang:send-web-reminders')
    ->everyMinute()
    ->withoutOverlapping();
