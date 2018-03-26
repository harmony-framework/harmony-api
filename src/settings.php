<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'db' => [
            'type' => 'mysql',
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'name' => 'harmony-api'
        ],
        'mail' => [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'secureType' => 'tls',
            'username' => '',
            'password' => '',
            'sendermail' => 'no-reply@harmony-api.com',
            'sendername' => 'Ofir Attia'
        ],
        'authentication' => [
            'options' => [
                'cost' => 12
            ]
        ]
    ],
];
