<?php

use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// ── Cargar variables de entorno ───────────────────────────────────────────────
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// ── Bootstrap de Eloquent ─────────────────────────────────────────────────────
require __DIR__ . '/../bootstrap/database.php';

$app = AppFactory::create();

// ① Ruta OPTIONS global — debe registrarse ANTES que cualquier middleware
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

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
// Así los headers CORS se añaden incluso en respuestas de error (404, 405, 500)
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin',  $_ENV['FRONTEND_URL'] ?? '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
});

$app->run();