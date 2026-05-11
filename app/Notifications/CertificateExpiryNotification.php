<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateExpiryNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Certificate $certificate,
        protected int $daysUntilExpiry
    ) {
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $expirationDate = optional($this->certificate->expiration_date)->format('F j, Y');
        $daysLabel = $this->daysUntilExpiry === 1 ? 'day' : 'days';

        return (new MailMessage)
            ->subject('Certificate Expiry Notice')
            ->line("Your {$this->certificate->certificate_type_label} certificate '{$this->certificate->certificate_name}' is expiring soon.")
            ->line("Expires in {$this->daysUntilExpiry} {$daysLabel} (" . ($expirationDate ?? 'date not set') . ").")
            ->action('View your profile', route('profile.edit'))
            ->line('Please renew or update your certificate details.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $expirationDate = $this->certificate->expiration_date;
        $expirationValue = $expirationDate instanceof \DateTimeInterface
            ? $expirationDate->format('Y-m-d')
            : null;

        return [
            'certificate_id' => $this->certificate->id,
            'certificate_name' => $this->certificate->certificate_name,
            'certificate_type' => $this->certificate->certificate_type,
            'certificate_type_label' => $this->certificate->certificate_type_label,
            'expiration_date' => $expirationValue,
            'days_until_expiry' => $this->daysUntilExpiry,
        ];
    }
}
