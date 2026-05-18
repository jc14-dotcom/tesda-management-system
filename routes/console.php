<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('certificates:send-expiry-notifications')
    ->dailyAt('08:00')
    ->withoutOverlapping();

Schedule::command('admin:send-notifications')
    ->dailyAt('08:05')
    ->withoutOverlapping();

Schedule::command('admin:send-notifications', ['--weekly-digest'])
    ->weeklyOn(1, '08:10') // Every Monday at 08:10
    ->withoutOverlapping();

// Dynamic backup schedule — frequency and time are configured by admins on the Backups page.
// Wrapped in try/catch so a missing settings table (fresh install) doesn't break the scheduler.
try {
    $backupFreq     = \App\Models\Setting::get('backup_schedule_frequency', 'disabled');
    $backupTime     = \App\Models\Setting::get('backup_schedule_time', '02:00') ?: '02:00';
    $backupWeekday  = (int) (\App\Models\Setting::get('backup_schedule_weekday', '1') ?: 1);
    $backupMonthday = (int) (\App\Models\Setting::get('backup_schedule_monthday', '1') ?: 1);

    if ($backupFreq !== 'disabled') {
        $backupCmd = Schedule::command('backup:run')->withoutOverlapping();
        if ($backupFreq === 'daily')          $backupCmd->dailyAt($backupTime);
        elseif ($backupFreq === 'weekly')     $backupCmd->weeklyOn($backupWeekday, $backupTime);
        elseif ($backupFreq === 'monthly')    $backupCmd->monthlyOn($backupMonthday, $backupTime);
        elseif ($backupFreq === 'quarterly') {
            [$bh, $bm] = array_map('intval', explode(':', $backupTime.':0'));
            $backupCmd->cron("{$bm} {$bh} 1 1,4,7,10 *");
        }
        elseif ($backupFreq === 'yearly')     $backupCmd->yearlyOn(1, 1, $backupTime);
    }
} catch (\Throwable) {
    // Settings table not yet available — skip dynamic schedule silently.
}
