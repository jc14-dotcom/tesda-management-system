<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyDigestNotification extends Notification
{
    public function __construct(private readonly array $stats) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $week = now()->subWeek()->format('M j') . ' – ' . now()->format('M j, Y');

        $message = (new MailMessage)
            ->subject('Weekly Activity Summary — ' . now()->format('M j, Y'))
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line("Here is your weekly activity summary for **{$week}**.");

        if ($this->stats['new_users'] > 0) {
            $label = $this->stats['new_users'] === 1 ? 'new user registered' : 'new users registered';
            $message->line('👥 **' . $this->stats['new_users'] . '** ' . $label);
        }
        if ($this->stats['new_certificates'] > 0) {
            $label = $this->stats['new_certificates'] === 1 ? 'new certificate added' : 'new certificates added';
            $message->line('📜 **' . $this->stats['new_certificates'] . '** ' . $label);
        }
        if ($this->stats['expiring_certificates'] > 0) {
            $label = $this->stats['expiring_certificates'] === 1 ? 'certificate expiring soon' : 'certificates expiring soon';
            $message->line('⚠️ **' . $this->stats['expiring_certificates'] . '** ' . $label);
        }
        if ($this->stats['pending_verification'] > 0) {
            $label = $this->stats['pending_verification'] === 1 ? 'certificate pending verification' : 'certificates pending verification';
            $message->line('🔍 **' . $this->stats['pending_verification'] . '** ' . $label);
        }

        return $message
            ->action('Go to Dashboard', route('admin.dashboard'))
            ->salutation('Alcatt Portal Admin');
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
