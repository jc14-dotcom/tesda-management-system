<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Models\User;
use App\Notifications\Admin\WeeklyDigestNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('admin:send-notifications {--weekly-digest : Send the weekly activity digest}')]
#[Description('Send scheduled admin notifications: optional weekly digest')]
class SendAdminNotifications extends Command
{
    public function handle(): int
    {
        if ($this->option('weekly-digest')) {
            $this->sendWeeklyDigest();
        }

        return Command::SUCCESS;
    }

    private function sendWeeklyDigest(): void
    {
        $since = now()->startOfWeek();

        $stats = [
            'new_users'             => User::where('created_at', '>=', $since)->count(),
            'new_certificates'      => Certificate::where('created_at', '>=', $since)->count(),
            'expiring_certificates' => Certificate::where('status', 'expiring')->count(),
            'expired_certificates'  => Certificate::where('status', 'expired')->count(),
        ];

        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            $this->warn('No admin users found.');
            return;
        }

        foreach ($admins as $admin) {
            $admin->notify(new WeeklyDigestNotification($stats));
        }

        $this->info('Weekly digest sent to ' . $admins->count() . ' admin(s).');
    }
}
