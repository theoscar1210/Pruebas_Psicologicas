<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Backup automatizado (spatie/laravel-backup) ───────────────────────────────
// Requiere BACKUP_ARCHIVE_PASSWORD en .env para cifrado AES-256.
// Para S3: configurar BACKUP_S3_DISK=s3 y credenciales AWS_* en .env.
// El cron del servidor debe ejecutar: php artisan schedule:run cada minuto.
Schedule::command('backup:run')->dailyAt('02:00');
Schedule::command('backup:clean')->dailyAt('02:30');
Schedule::command('backup:monitor')->dailyAt('03:00');
