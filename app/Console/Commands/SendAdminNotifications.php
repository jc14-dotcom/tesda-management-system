<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Models\User;
use App\Notifications\Admin\VerificationPendingReminderNotification;
use App\Notifications\Admin\WeeklyDigestNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

#[Signature('admin:send-notifications {--weekly-digest : Send the weekly activity digest}')]
#[Description('Send scheduled admin notifications: verification reminders and optional weekly digest')]
class SendAdminNotifications extends Command
{
    public function handle(): int
    {
        if ($this->option('weekly-digest')) {
            $this->sendWeeklyDigest();
        } else {
            $this->sendVerificationReminders();
        }

        return Command::SUCCESS;
    }

    private function sendVerificationReminders(): void
    {
        $reminderDays = (int) config('certificates.verification_reminder_days', 7);
        $threshold    = now()->subDays($reminderDays);

        $pending = Certificate::with('user')
            ->where('verification_status', 'pending')
            ->where('created_at', '<=', $threshold)
            ->get();

        if ($pending->isEmpty()) {
            $this->info('No pending certificates require reminders.');
            return;
        }

        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            $this->warn('No admin users found.');
            return;
        }

        $sent = 0;
        foreach ($pending as $certificate) {
            $pendingDays = (int) now()->startOfDay()->diffInDays(
                Carbon::parse($certificate->created_at)->startOfDay()
            );

            foreach ($admins as $admin) {
                $admin->notify(new VerificationPendingReminderNotification($certificate, $pendingDays));
            }
            $sent++;
        }

        $this->info("Sent verification reminders for {$sent} certificate(s).");
    }

    private function sendWeeklyDigest(): void
    {
        $since = now()->startOfWeek();

        $stats = [
            'new_users'            => User::where('created_at', '>=', $since)->count(),
            'new_certificates'     => Certificate::where('created_at', '>=', $since)->count(),
            'expiring_certificates' => Certificate::where('status', 'expiring')->count(),
            'pending_verification' => Certificate::where('verification_status', 'pending')->count(),
            'expired_certificates' => Certificate::where('status', 'expired')->count(),
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
