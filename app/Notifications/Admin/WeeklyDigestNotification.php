<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Notification;

class WeeklyDigestNotification extends Notification
{
    public function __construct(private readonly array $stats) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $parts = [];
        if ($this->stats['new_users'] > 0) {
            $parts[] = $this->stats['new_users'] . ' new ' . ($this->stats['new_users'] === 1 ? 'user' : 'users');
        }
        if ($this->stats['new_certificates'] > 0) {
            $parts[] = $this->stats['new_certificates'] . ' new ' . ($this->stats['new_certificates'] === 1 ? 'certificate' : 'certificates');
        }
        if ($this->stats['expiring_certificates'] > 0) {
            $parts[] = $this->stats['expiring_certificates'] . ' expiring';
        }
        if ($this->stats['pending_verification'] > 0) {
            $parts[] = $this->stats['pending_verification'] . ' pending verification';
        }

        $summary = empty($parts) ? 'No significant activity this week.' : 'This week: ' . implode(', ', $parts) . '.';

        return [
            'type'    => 'weekly_digest',
            'title'   => 'Weekly Activity Summary',
            'message' => $summary,
            'url'     => route('admin.dashboard'),
            'stats'   => $this->stats,
        ];
    }
}
