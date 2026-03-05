<?php

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    'default' => env('LOG_CHANNEL', 'daily'),

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace'   => false,
    ],

    'channels' => [

        'stack' => [
            'driver'            => 'stack',
            'channels'          => ['daily'],
            'ignore_exceptions' => true,
        ],

        'daily' => [
            'driver'               => 'daily',
            'path'                 => storage_path('logs/laravel.log'),
            'level'                => env('LOG_LEVEL', 'error'),
            'days'                 => 7,
            'replace_placeholders' => true,
            'formatter'            => LineFormatter::class,
            'formatter_with'       => [
                'format'                     => "[%datetime%] %channel%.%level_name%: %message%\n",
                'allowInlineLineBreaks'      => false,
                'ignoreEmptyContextAndExtra' => true,
                'includeStacktraces'         => false, // ← INI KUNCI UTAMA
            ],
        ],

        'null' => [
            'driver'  => 'monolog',
            'handler' => \Monolog\Handler\NullHandler::class,
        ],

    ],

];
