<?php

namespace App\Notifications\Admin;

use App\Models\Certificate;
use Illuminate\Notifications\Notification;

class CertificateExpiryAdminNotification extends Notification
{
    public function __construct(
        private readonly Certificate $certificate,
        private readonly int $daysUntilExpiry,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = Certificate::TYPE_LABELS[$this->certificate->certificate_type] ?? ucfirst($this->certificate->certificate_type);
        $userName  = $this->certificate->user?->name ?? 'Unknown User';

        if ($this->daysUntilExpiry < 0) {
            $title   = 'Certificate Expired';
            $message = "{$userName}'s {$typeLabel} certificate ({$this->certificate->certificate_name}) has expired.";
            $type    = 'certificate_expired';
        } else {
            $days    = $this->daysUntilExpiry;
            $title   = "Certificate Expiring in {$days} " . ($days === 1 ? 'Day' : 'Days');
            $message = "{$userName}'s {$typeLabel} certificate ({$this->certificate->certificate_name}) expires in {$days} " . ($days === 1 ? 'day' : 'days') . '.';
            $type    = 'certificate_expiry';
        }

        return [
            'type'             => $type,
            'title'            => $title,
            'message'          => $message,
            'url'              => route('admin.certificates.show', $this->certificate->id),
            'user_id'          => $this->certificate->user_id,
            'user_name'        => $userName,
            'certificate_id'   => $this->certificate->id,
            'certificate_name' => $this->certificate->certificate_name,
            'days_until_expiry' => $this->daysUntilExpiry,
        ];
    }
}
