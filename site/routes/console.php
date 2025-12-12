<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// for debugg only 
Schedule::command('articles:auto')->everyMinute();

// for prod
// Schedule::command('articles:auto')->hourly();