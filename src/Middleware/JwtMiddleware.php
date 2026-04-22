<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Factory\ResponseFactory;

class JwtMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthorized('Token no proporcionado.');
        }

        $token = substr($authHeader, 7);
        $payload = $this->validateJWT($token);

        if (!$payload) {
            return $this->unauthorized('Token inválido o expirado.');
        }

        // Inyectar datos del usuario autenticado en el request
        $request = $request->withAttribute('auth_user', $payload);

        return $handler->handle($request);
    }

 public static function validateJWT(string $token): array|false
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;

    [$header, $payload, $signature] = $parts;

    $secret   = $_ENV['JWT_SECRET'] ?? 'mundialfan_secret_key';
    $expected = rtrim(strtr(base64_encode(
        hash_hmac('sha256', "$header.$payload", $secret, true)
    ), '+/', '-_'), '=');

    if (!hash_equals($expected, $signature)) {
        return false;
    }

    $decoded = base64_decode(strtr($payload, '-_', '+/'));
    $data    = json_decode($decoded, true);

    if (!$data || (isset($data['exp']) && $data['exp'] < time())) {
        return false;
    }

    return $data;
}

    private function unauthorized(string $message): Response
    {
        $factory  = new ResponseFactory();
        $response = $factory->createResponse(401);
        $response->getBody()->write(json_encode(['message' => $message]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
