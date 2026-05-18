<?php

namespace App\Observers;

use App\Models\Certificate;
use App\Models\User;
use App\Notifications\Admin\CertificateSubmittedNotification;

class CertificateObserver
{
    public function created(Certificate $certificate): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $certificate->loadMissing('user');

        User::role('admin')->each(
            fn (User $admin) => $admin->notify(new CertificateSubmittedNotification($certificate))
        );
    }
}
