<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('site:check')->everyTenMinutes()->withoutOverlapping();

// Keep the visits table small: summarize per day, then delete raw rows past the retention period.
Schedule::command('visits:rollup')->dailyAt('00:20');
Schedule::command('model:prune', ['--model' => [\App\Models\Visit::class]])->dailyAt('00:40');

// Fresh IP-to-country data (DB-IP publishes monthly).
Schedule::command('geoip:update')->monthlyOn(3, '04:00');
