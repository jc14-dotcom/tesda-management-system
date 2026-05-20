<?php

namespace App\Notifications\Admin;

use App\Models\Certificate;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateSubmittedNotification extends Notification
{
    public function __construct(private readonly Certificate $certificate) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $typeLabel = Certificate::TYPE_LABELS[$this->certificate->certificate_type] ?? ucfirst($this->certificate->certificate_type);
        $userName  = $this->certificate->user?->name ?? 'Unknown User';

        return (new MailMessage)
            ->subject('New Certificate Submitted — Alcatt Portal')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line("{$userName} has submitted a new certificate for review.")
            ->line('**Certificate:** ' . ($this->certificate->certificate_name ?: '—'))
            ->line('**Type:** ' . $typeLabel)
            ->line('**Issued By:** ' . ($this->certificate->issued_by ?: '—'))
            ->action('View Certificate', route('admin.certificates.show', $this->certificate->id))
            ->line('You can review this certificate from the administration panel.')
            ->salutation('Alcatt Portal Admin');
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = Certificate::TYPE_LABELS[$this->certificate->certificate_type] ?? ucfirst($this->certificate->certificate_type);
        $userName  = $this->certificate->user?->name ?? 'Unknown User';

        return [
            'type'             => 'certificate_submitted',
            'title'            => 'New Certificate Added',
            'message'          => "{$userName} added a new {$typeLabel} certificate.",
            'url'              => route('admin.certificates.show', $this->certificate->id),
            'user_id'          => $this->certificate->user_id,
            'user_name'        => $userName,
            'certificate_id'   => $this->certificate->id,
            'certificate_name' => $this->certificate->certificate_name,
            'certificate_type' => $this->certificate->certificate_type,
        ];
    }
}
