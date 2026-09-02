<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Email hebdomadaire 3x30, le lundi matin (heure suisse).
Schedule::command('workshop:weekly-reminders')
    ->weeklyOn(1, '07:00')
    ->timezone('Europe/Zurich');
