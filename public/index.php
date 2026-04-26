<?php

use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// ── Cargar variables de entorno ───────────────────────────────────────────────
// createUnsafeImmutable permite que getenv() funcione con variables del sistema
// (Railway las inyecta como variables de sistema, no como archivo .env)
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// ── Bootstrap de Eloquent ─────────────────────────────────────────────────────
require __DIR__ . '/../bootstrap/database.php';

$app = AppFactory::create();
$app->add(function ($request, $handler) {
    $origin = $request->getHeaderLine('Origin');
    $allowedOrigins = [
        'https://mundialmcu.netlify.app',
        'http://localhost:3000',
        'http://localhost:8080',
        'http://127.0.0.1:5500',
        'http://0.0.0.0:8080',
    ];
    $allowedOrigin = in_array($origin, $allowedOrigins) ? $origin : 'https://mundialmcu.netlify.app';

    if ($request->getMethod() === 'OPTIONS') {
        $response = new \Slim\Psr7\Response();
        return $response
            ->withStatus(204)
            ->withHeader('Access-Control-Allow-Origin',      $allowedOrigin)
            ->withHeader('Access-Control-Allow-Methods',     'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers',     'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Max-Age',           '86400');
    }

    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin',      $allowedOrigin)
        ->withHeader('Access-Control-Allow-Methods',     'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers',     'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

$app->addBodyParsingMiddleware();
$app->add(new ContentLengthMiddleware());

// ── Mostrar errores detallados para depuración ────────────────────────────────
// TODO: cambiar displayErrorDetails a false cuando todo funcione
$app->addErrorMiddleware(
    displayErrorDetails: true,
    logErrors:           true,
    logErrorDetails:     true
);

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ── Rutas ─────────────────────────────────────────────────────────────────────
$routes = require __DIR__ . '/../routes.php';
$routes($app);

$app->run();