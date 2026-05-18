<?php

return [
    'notifications_enabled' => env('CERTIFICATES_NOTIFICATIONS_ENABLED', false),
    'expiry_notice_days' => [30, 14, 7, 3, 1],
    'status_expiring_days' => 30,
    // Certificates pending verification for this many days trigger a reminder to admins
    'verification_reminder_days' => env('CERTIFICATES_VERIFICATION_REMINDER_DAYS', 7),
];
