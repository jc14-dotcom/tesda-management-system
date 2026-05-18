<?php

namespace App\Notifications\Admin;

use App\Models\Certificate;
use Illuminate\Notifications\Notification;

class CertificateSubmittedNotification extends Notification
{
    public function __construct(private readonly Certificate $certificate) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = Certificate::TYPE_LABELS[$this->certificate->certificate_type] ?? ucfirst($this->certificate->certificate_type);
        $userName  = $this->certificate->user?->name ?? 'Unknown User';

        return [
            'type'             => 'certificate_submitted',
            'title'            => 'New Certificate Submitted',
            'message'          => "{$userName} submitted a {$typeLabel} certificate for verification.",
            'url'              => route('admin.certificates.show', $this->certificate->id),
            'user_id'          => $this->certificate->user_id,
            'user_name'        => $userName,
            'certificate_id'   => $this->certificate->id,
            'certificate_name' => $this->certificate->certificate_name,
            'certificate_type' => $this->certificate->certificate_type,
        ];
    }
}
