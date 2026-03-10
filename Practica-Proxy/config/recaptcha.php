<?php

return [
    'enabled' => env('RECAPTCHA_ENABLED', false),
    'site_key' => env('RECAPTCHA_SITE_KEY', ''),
    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
    'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5),
    'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
];
