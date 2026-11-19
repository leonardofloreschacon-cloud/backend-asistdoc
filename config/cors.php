<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'https://frontend-asistdoc.vercel.app',
        'https://frontend-asistdoc-q59nfua26-leonardo-c1a1.vercel.app',
    ],
    'allowed_origins_patterns' => [
        '/^https:\/\/frontend-asistdoc.*\.vercel\.app$/',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];