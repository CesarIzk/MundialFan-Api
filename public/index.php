<?php

use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// ── Cargar variables de entorno ───────────────────────────────────────────────
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// ── Bootstrap de Eloquent ─────────────────────────────────────────────────────
require __DIR__ . '/../bootstrap/database.php';

$app = AppFactory::create();;

// ② Rutas de la aplicación
$routes = require __DIR__ . '/../routes.php';
$routes($app);

// ③ Middlewares (orden LIFO: el último registrado se ejecuta primero)

$app->addBodyParsingMiddleware();
$app->add(new ContentLengthMiddleware());

// ErrorMiddleware envuelve todo lo de abajo → se ejecuta antes que CORS
$app->addErrorMiddleware(
    displayErrorDetails: (bool)($_ENV['APP_DEBUG'] ?? false),
    logErrors: true,
    logErrorDetails: true
);

// CORS al final del código = primero en ejecutarse (LIFO)
$app->add(function ($request, $handler) {
    // ← NUEVO: responder preflight aquí mismo, sin pasar al handler
    if ($request->getMethod() === 'OPTIONS') {
        $response = new \Slim\Psr7\Response();
        return $response
            ->withHeader('Access-Control-Allow-Origin',  $_ENV['FRONTEND_URL'] ?? '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withStatus(200);
    }

    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin',  $_ENV['FRONTEND_URL'] ?? '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
});

$app->run();