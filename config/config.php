<?php

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'italian_vat_app',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => '/italian-vat-app/public',
        'upload_dir' => __DIR__ . '/../uploads',
        'max_upload_size' => 5 * 1024 * 1024,
    ],
];
