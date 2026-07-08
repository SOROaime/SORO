<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Annule les commandes GeniusPay en attente depuis plus de 35 min et restaure le stock
Schedule::command('payments:cancel-pending')->everyFiveMinutes();

// Regénère le sitemap.xml chaque semaine (SEO)
Schedule::command('sitemap:generate')->weekly();
