<?php
return [


    'settings' => [

        'mode' => env('mode'),
        'displayErrorDetails' => env('show_errors'),
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => env('LOGS_NAME'),
            'path' => env('LOGS_PATH'),
            /** @see \Monolog\Logger */
            'level' => env('LOGS_LEVEL'),
        ],
    ],
];
