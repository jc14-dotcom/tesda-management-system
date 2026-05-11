<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Notifications\CertificateExpiryNotification;
use Illuminate\Support\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('certificates:send-expiry-notifications')]
#[Description('Send expiry notifications for certificates nearing expiration')]
class SendCertificateExpiryNotifications extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = collect(config('certificates.expiry_notice_days', [30, 14, 7, 3, 1]))
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value >= 0)
            ->unique()
            ->sort()
            ->values();

        if ($days->isEmpty()) {
            $this->warn('No expiry notice days configured.');
            return Command::SUCCESS;
        }

        $maxDays = (int) $days->max();
        $statusExpiringDays = (int) config('certificates.status_expiring_days', 30);
        $today = now()->startOfDay();

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Certificate> $certificates */
        $certificates = Certificate::with('user')
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays($maxDays))
            ->get();

        $notifiedCount = 0;

        foreach ($certificates as $certificate) {
            /** @var \App\Models\Certificate $certificate */
            if (! $certificate->user) {
                continue;
            }

            $expiration = Carbon::parse($certificate->expiration_date)->startOfDay();
            $daysUntil = $today->diffInDays($expiration, false);

            if ($daysUntil < 0) {
                $certificate->status = 'expired';
            } elseif ($daysUntil <= $statusExpiringDays) {
                $certificate->status = 'expiring';
            } else {
                $certificate->status = 'valid';
            }

            if ($daysUntil >= 0 && $days->contains($daysUntil)) {
                $notifiedDays = $certificate->notified_days ?? [];
                if (! in_array($daysUntil, $notifiedDays, true)) {
                    $certificate->user->notify(new CertificateExpiryNotification($certificate, $daysUntil));
                    $notifiedDays[] = $daysUntil;
                    $certificate->notified_days = $notifiedDays;
                    $certificate->last_notified_at = now()->toDateTimeString();
                    $certificate->notification_count = (int) $certificate->notification_count + 1;
                    $notifiedCount++;
                }
            }

            $certificate->save();
        }

        $this->info("Expiry notifications sent: {$notifiedCount}");

        return Command::SUCCESS;
    }
}
