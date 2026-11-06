<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('site:check')->everyTenMinutes()->withoutOverlapping();
