<?php

return [
    'notifications_enabled' => env('CERTIFICATES_NOTIFICATIONS_ENABLED', false),
    'expiry_notice_days' => [30, 14, 7, 3, 1],
    'status_expiring_days' => 30,
];
