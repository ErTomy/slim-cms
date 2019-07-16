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

        // jwt settings
        'jwt' => [
            'secret' => 'supersecretkeyyoushouldnotcommittogithub'
        ],

        // acceso administrador
        'admin' => [
            'username' => 'Admin',
            'password' => 'pass'
        ],

        // url base de la web
        'basepath' => 'http://192.168.99.101/'
    ],
];
