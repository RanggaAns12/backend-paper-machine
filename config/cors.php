<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // Pastikan jalur API dan Sanctum cookie diizinkan lewat
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'],

    // Izinkan semua metode request (GET, POST, PUT, DELETE, dll)
    'allowed_methods' => ['*'],

    // ✅ Izinkan akses dari Localhost dan IP .216 (Frontend Angular)
    'allowed_origins' => [
        'http://localhost:4200',
        'http://127.0.0.1:4200',
        'http://192.168.1.30:4200',
    ],

    'allowed_origins_patterns' => [],

    // Izinkan semua jenis header
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // ✅ WAJIB TRUE UNTUK SANCTUM (Agar token dan cookie session bisa dibaca browser)
    'supports_credentials' => true,

];