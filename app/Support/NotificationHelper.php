<?php

namespace App\Support;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    /**
     * Try to send a notification immediately (synchronously).
     *
     * If the send fails — e.g. SMTP is unreachable, no internet connection — the
     * notification is queued to the database so the background worker can retry it
     * later using the notification's own $tries / $backoff settings.
     *
     * The notification MUST implement ShouldQueue for the fallback retry to work.
     *
     * Usage:
     *   NotificationHelper::sendNowOrQueue($user, new AccountApprovedNotification());
     */
    public static function sendNowOrQueue(mixed $notifiable, Notification $notification): void
    {
        try {
            $notifiable->notifyNow($notification);
        } catch (\Throwable $e) {
            Log::warning(
                '[NotificationHelper] Immediate send failed — queuing for retry. ' .
                get_class($notification) . ' for notifiable #' . ($notifiable->getKey() ?? '?') .
                ': ' . $e->getMessage()
            );

            // Dispatch to the queue so the worker retries with backoff.
            // Requires the notification to implement ShouldQueue.
            $notifiable->notify($notification);
        }
    }
}
