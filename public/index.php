<?php

use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// ── Cargar variables de entorno ───────────────────────────────────────────────
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// ── Bootstrap de Eloquent ─────────────────────────────────────────────────────
require __DIR__ . '/../bootstrap/database.php';

// ── Crear app Slim ────────────────────────────────────────────────────────────
$app = AppFactory::create();

// ── CORS: permitir peticiones desde el frontend ───────────────────────────────
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);

    return $response
        ->withHeader('Access-Control-Allow-Origin',  $_ENV['FRONTEND_URL'] ?? '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
});

// Responder preflight OPTIONS sin pasar por el middleware de JWT
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

// ── Middleware de errores ─────────────────────────────────────────────────────
$app->addErrorMiddleware(
    displayErrorDetails: (bool)($_ENV['APP_DEBUG'] ?? false),
    logErrors: true,
    logErrorDetails: true
);

$app->addBodyParsingMiddleware();
$app->add(new ContentLengthMiddleware());

// ── Rutas ─────────────────────────────────────────────────────────────────────
$routes = require __DIR__ . '/../routes.php';
$routes($app);

$app->run();