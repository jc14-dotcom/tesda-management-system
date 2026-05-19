<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountApprovedNotification extends Notification
{
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
