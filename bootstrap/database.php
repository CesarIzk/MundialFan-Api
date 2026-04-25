<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();

$url = $_ENV['MYSQL_URL'] ?? getenv('MYSQL_URL');

if ($url) {
    $parts = parse_url($url);
    $capsule->addConnection([
        'driver'    => 'mysql',
        'host'      => $parts['host'],
        'port'      => $parts['port'] ?? 3306,
        'database'  => ltrim($parts['path'], '/'),
        'username'  => $parts['user'],
        'password'  => $parts['pass'],
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ]);
} else {
    // fallback local
    $capsule->addConnection([
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'port'      => 3306,
        'database'  => 'mundialfan',
        'username'  => 'root',
        'password'  => 'root',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ]);
}

$capsule->setAsGlobal();
$capsule->bootEloquent();