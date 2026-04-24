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

// CORS Middleware - se ejecuta primero (last added, first executed)
$app->add(function ($request, $handler) {
    $origin = $request->getHeaderLine('Origin');
    $allowedOrigins = [
        'https://mundialmcu.netlify.app',
        'http://localhost:3000',
        'http://localhost:8080',
        'http://0.0.0.0:8080'
    ];

    $allowedOrigin = in_array($origin, $allowedOrigins) ? $origin : 'https://mundialmcu.netlify.app';

    // Responder OPTIONS directamente
    if ($request->getMethod() === 'OPTIONS') {
        return (new \Slim\Psr7\Response())
            ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Max-Age', '86400');
    }

    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

$app->run();