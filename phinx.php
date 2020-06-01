<?php


// load our environment files - used to store credentials & configuration
$dotenv = Dotenv\Dotenv::createImmutable(getcwd());
$dotenv->load();

return
    [
        'paths' => [
            'migrations' => 'database/migrations',
        ],
        'environments' =>
            [
                'default_database' => $_ENV['DB_DEFAULT'],
                'default_migration_table' => 'phinxlog',
                'development'      =>
                    [
                        'adapter' => $_ENV['DB_ADAPTER'],
                        'host' => $_ENV['DB_HOST'],
                        'name' => $_ENV['DB_DATABASE'],
                        'user' => $_ENV['DB_USERNAME'],
                        'pass' => $_ENV['DB_PASSWORD'],
                        'port' => $_ENV['DB_PORT'],
                        'charset' => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                    ],
                'staging' =>
                    [
                        'adapter' => $_ENV['DB_ADAPTER'],
                        'host' => $_ENV['DB_HOST'],
                        'name' => $_ENV['DB_DATABASE'],
                        'user' => $_ENV['DB_USERNAME'],
                        'pass' => $_ENV['DB_PASSWORD'],
                        'port' => $_ENV['DB_PORT'],
                        'charset' => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                    ],
                'production' =>
                    [
                        'adapter' => $_ENV['DB_ADAPTER'],
                        'host' => $_ENV['DB_HOST'],
                        'name' => $_ENV['DB_DATABASE'],
                        'user' => $_ENV['DB_USERNAME'],
                        'pass' => $_ENV['DB_PASSWORD'],
                        'port' => $_ENV['DB_PORT'],
                        'charset' => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                    ],
            ],
    ];
