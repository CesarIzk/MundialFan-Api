<?php

namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    // ── POST /api/auth/login ──────────────────────────────────────────────────
    public function login(Request $request, Response $response): Response
    {
        $body     = (array) $request->getParsedBody();
        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');

        // Validación básica
        if (empty($email) || empty($password)) {
            return $this->json($response, ['message' => 'Email y contraseña son requeridos.'], 422);
        }

        $user = User::attempt($email, $password);

        if (!$user) {
            return $this->json($response, ['message' => 'Credenciales inválidas.'], 401);
        }

        if ($user->status !== 'active') {
            return $this->json($response, ['message' => 'Tu cuenta está desactivada.'], 403);
        }

        $token = $this->generateJWT($user);

        return $this->json($response, [
            'token' => $token,
            'user'  => $user->only(['id', 'name', 'username', 'email', 'role', 'profile_picture']),
        ]);
    }

    // ── POST /api/auth/register ───────────────────────────────────────────────
    public function register(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();

        $name      = trim($body['name']       ?? '');
        $username  = trim($body['username']   ?? '');
        $email     = trim($body['email']      ?? '');
        $password  = trim($body['password']   ?? '');
        $confirm   = trim($body['password_confirmation'] ?? '');

        // Validaciones
        $errors = [];

        if (empty($name))     $errors[] = 'El nombre es requerido.';
        if (empty($username)) $errors[] = 'El nombre de usuario es requerido.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
                              $errors[] = 'Email inválido.';
        if (strlen($password) < 6)
                              $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        if ($password !== $confirm)
                              $errors[] = 'Las contraseñas no coinciden.';

        if (!empty($errors)) {
            return $this->json($response, ['message' => implode(' ', $errors)], 422);
        }

        // Verificar duplicados
        if (User::findByEmail($email)) {
            return $this->json($response, ['message' => 'El email ya está registrado.'], 409);
        }
        if (User::findByUsername($username)) {
            return $this->json($response, ['message' => 'El nombre de usuario ya existe.'], 409);
        }

        $user = User::create([
            'name'       => $name,
            'username'   => $username,
            'email'      => $email,
            'password'   => $password,   // plain text según requerimiento del proyecto
            'role'       => 'user',
            'status'     => 'active',
            'birth_date' => $body['birth_date'] ?? null,
            'gender'     => $body['gender']     ?? null,
            'country'    => $body['country']    ?? null,
            'city'       => $body['city']       ?? null,
        ]);

        return $this->json($response, [
            'message' => 'Cuenta creada exitosamente.',
            'user'    => $user->only(['id', 'name', 'username', 'email']),
        ], 201);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function generateJWT(User $user): string
{
    $secret = $_ENV['JWT_SECRET'] ?? 'mundialfan_secret_key';

    $header  = $this->base64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = $this->base64url(json_encode([
        'sub'  => $user->id,
        'name' => $user->name,
        'email'=> $user->email,
        'role' => $user->role,
        'iat'  => time(),
        'exp'  => time() + 86400 * 7,
    ]));

    $signature = $this->base64url(hash_hmac('sha256', "$header.$payload", $secret, true));

    return "$header.$payload.$signature";
}

private function base64url(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

    private function json(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
