<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\User;
use App\Notifications\Admin\DocumentUploadedNotification;

class DocumentObserver
{
    public function created(Document $document): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        // Skip documents auto-created as certificate file attachments
        if ($document->type === 'certificate') {
            return;
        }

        $document->loadMissing('user');

        User::role('admin')->each(
            fn (User $admin) => $admin->notify(new DocumentUploadedNotification($document))
        );
    }
}
