<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQLHOST']     ?? '127.0.0.1',
    'port'      => $_ENV['MYSQLPORT']     ?? '3306',
    'database'  => $_ENV['MYSQLDATABASE'] ?? 'mundialfan',
    'username'  => $_ENV['MYSQLUSER']     ?? 'root',
    'password'  => $_ENV['MYSQLPASSWORD'] ?? 'root',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();
