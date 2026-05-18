<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\Admin\NewUserRegisteredNotification;

class UserObserver
{
    public function created(User $user): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        User::role('admin')->each(
            fn (User $admin) => $admin->notify(new NewUserRegisteredNotification($user))
        );
    }
}
