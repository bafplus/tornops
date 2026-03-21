<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('torn:sync-faction')->everyFiveMinutes();
Schedule::command('torn:sync-wars')->everyFiveMinutes();
Schedule::command('torn:sync-members')->everyFifteenMinutes();
