<?php

return [
    'reset_url' => env('FRONTEND_RESET_URL', 'http://localhost:5173/reset-password'),
    'reset_minutes' => 60,
    'payments' => ['mode' => env('PAYMENTS_MODE', 'test')],
    // OpenAI-compatible chat completions endpoint, configured only on the server.
    'ai' => [
        'url' => env('AI_CHAT_URL'),
        'key' => env('AI_API_KEY'),
        'model' => env('AI_MODEL'),
        'daily_limit' => (int) env('AI_DAILY_LIMIT', 20),
        'timeout' => (int) env('AI_TIMEOUT_SECONDS', 15),
    ],
];
