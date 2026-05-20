<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** Retry up to 3 times if the immediate send fails. */
    public int $tries = 3;

    /** Wait 2 min, then 5 min between retries. */
    public array $backoff = [120, 300];

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Alcatt Portal Account Has Been Approved')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('Great news! Your account registration for the **Alcatt Portal** has been reviewed and approved by an administrator.')
            ->line('You can now log in to the system using your registered email address and password.')
            ->action('Log In to Alcatt Portal', route('login'))
            ->line('If you did not register for this account, please contact the system administrator immediately.')
            ->salutation('Alcatt Portal');
    }
}
