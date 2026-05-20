<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification
{
    public function __construct(private readonly User $newUser) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New User Registration — Alcatt Portal')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('A new user has registered and is awaiting account approval.')
            ->line('**Name:** ' . $this->newUser->name)
            ->line('**Email:** ' . $this->newUser->email)
            ->line('**Registered:** ' . $this->newUser->created_at?->format('F j, Y \a\t g:i A'))
            ->action('Review User Account', route('admin.users.show', $this->newUser->id))
            ->line('Please review and approve or reject this account from the administration panel.')
            ->salutation('Alcatt Portal Admin');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'      => 'user_registered',
            'title'     => 'New User Registered',
            'message'   => "{$this->newUser->name} ({$this->newUser->email}) has created an account.",
            'url'       => route('admin.users.show', $this->newUser->id),
            'user_id'   => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'email'     => $this->newUser->email,
        ];
    }
}
