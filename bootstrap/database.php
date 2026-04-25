<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['DB_HOST']     ?? getenv('DB_HOST')     ?: 'mysql.railway.internal',
    'port'      => $_ENV['DB_PORT']     ?? getenv('DB_PORT')     ?: 3306,
    'database'  => $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'railway',
    'username'  => $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root',
    'password'  => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();