<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Backup automatizado ───────────────────────────────────────────────────────
// Requiere: composer require spatie/laravel-backup
// Config:   php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
// Ajustar config/backup.php con discos, notificaciones y retención.
//
// Schedule::command('backup:run')->dailyAt('02:00');
// Schedule::command('backup:clean')->dailyAt('02:30');
// Schedule::command('backup:monitor')->dailyAt('03:00');
