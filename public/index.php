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

// ── index.php corregido ───────────────────────────────────────────────────────

$app->addBodyParsingMiddleware();
$app->add(new ContentLengthMiddleware());

// CORS va antes del Error Middleware para que envuelva los errores también
$app->add(function ($request, $handler) {
    $origin = $request->getHeaderLine('Origin');
    $allowedOrigins = [
        'https://mundialmcu.netlify.app',
        'http://localhost:3000',
        'http://localhost:8080',
        'http://0.0.0.0:8080',
    ];
    
    // Si el origen está en la lista, usarlo; si no, usar wildcard como fallback
    $allowedOrigin = in_array($origin, $allowedOrigins) ? $origin : '*';

    // Manejo de solicitudes preflight OPTIONS
    if ($request->getMethod() === 'OPTIONS') {
        $response = new \Slim\Psr7\Response();
        $corsResponse = $response
            ->withStatus(200)
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Max-Age', '86400');
        
        // Agregar el header Origin si no es wildcard
        if ($allowedOrigin !== '*') {
            $corsResponse = $corsResponse
                ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
                ->withHeader('Access-Control-Allow-Credentials', 'true');
        } else {
            $corsResponse = $corsResponse->withHeader('Access-Control-Allow-Origin', '*');
        }
        
        return $corsResponse;
    }

    $response = $handler->handle($request);
    
    // Agregar headers CORS a todas las respuestas (incluyendo errores)
    if ($allowedOrigin !== '*') {
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    } else {
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
    }
    
    return $response
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
});

$app->addErrorMiddleware(
    displayErrorDetails: true,
    logErrors: true,
    logErrorDetails: true
);

$app->run(); // ← falta esta línea