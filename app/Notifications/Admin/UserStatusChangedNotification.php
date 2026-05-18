<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Notifications\Notification;

class UserStatusChangedNotification extends Notification
{
    public function __construct(
        private readonly User $user,
        private readonly string $newStatus,
        private readonly string $changedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $action  = $this->newStatus === 'active' ? 'activated' : 'deactivated';
        $message = "{$this->user->name}'s account was {$action} by {$this->changedBy}.";

        return [
            'type'       => 'user_status_changed',
            'title'      => 'User Account ' . ucfirst($action),
            'message'    => $message,
            'url'        => route('admin.users.show', $this->user->id),
            'user_id'    => $this->user->id,
            'user_name'  => $this->user->name,
            'new_status' => $this->newStatus,
            'changed_by' => $this->changedBy,
        ];
    }
}
