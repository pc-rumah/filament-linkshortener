<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('plans:downgrade-expired', function () {
    $this->call('app:downgrade-expired-plans');
})->describe('Downgrade expired pro plans to basic');

Schedule::command('plans:downgrade-expired')->daily();
