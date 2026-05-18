<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification
{
    public function __construct(private readonly User $newUser) {}

    public function via(object $notifiable): array
    {
        return ['database'];
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
