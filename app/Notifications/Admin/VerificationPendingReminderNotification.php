<?php

namespace App\Notifications\Admin;

use App\Models\Certificate;
use Illuminate\Notifications\Notification;

class VerificationPendingReminderNotification extends Notification
{
    public function __construct(
        private readonly Certificate $certificate,
        private readonly int $pendingDays,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = Certificate::TYPE_LABELS[$this->certificate->certificate_type] ?? ucfirst($this->certificate->certificate_type);
        $userName  = $this->certificate->user?->name ?? 'Unknown User';

        return [
            'type'             => 'verification_reminder',
            'title'            => 'Certificate Awaiting Verification',
            'message'          => "{$userName}'s {$typeLabel} certificate has been pending verification for {$this->pendingDays} " . ($this->pendingDays === 1 ? 'day' : 'days') . '.',
            'url'              => route('admin.certificates.show', $this->certificate->id),
            'user_id'          => $this->certificate->user_id,
            'user_name'        => $userName,
            'certificate_id'   => $this->certificate->id,
            'certificate_name' => $this->certificate->certificate_name,
            'pending_days'     => $this->pendingDays,
        ];
    }
}
