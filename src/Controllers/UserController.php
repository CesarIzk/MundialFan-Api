<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Post;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    // ── GET /api/users?q=... ──────────────────────────────────────────────────
    public function search(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $q        = trim($request->getQueryParams()['q'] ?? '');

        $users = User::search($q, $authUser['sub']);

        return $this->json($response, $users);
    }

    // ── GET /api/users/{id} ───────────────────────────────────────────────────
    public function show(Request $request, Response $response, array $args): Response
    {
        $user = User::find($args['id']);

        if (!$user) {
            return $this->json($response, ['message' => 'Usuario no encontrado.'], 404);
        }

        $posts = Post::byUser($user->id);

        return $this->json($response, [
            'user'  => $user->makeHidden(['password']),
            'posts' => $posts,
        ]);
    }

    // ── PUT /api/users/me ─────────────────────────────────────────────────────
    public function updateProfile(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $user     = User::find($authUser['sub']);
        $body     = (array) $request->getParsedBody();

        $allowed = ['name', 'username', 'bio', 'birth_date', 'gender', 'country', 'city'];

        foreach ($allowed as $field) {
            if (isset($body[$field])) {
                $user->$field = trim($body[$field]);
            }
        }

        // Validar que el username no esté tomado por otro usuario
        if (isset($body['username'])) {
            $existing = User::findByUsername($body['username']);
            if ($existing && $existing->id !== $user->id) {
                return $this->json($response, ['message' => 'El nombre de usuario ya está en uso.'], 409);
            }
        }

        $user->save();

        return $this->json($response, $user->makeHidden(['password']));
    }

    // ── POST /api/users/me/avatar ─────────────────────────────────────────────
    public function updateAvatar(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $user     = User::find($authUser['sub']);
        $files    = $request->getUploadedFiles();

        if (empty($files['avatar']) || $files['avatar']->getError() !== UPLOAD_ERR_OK) {
            return $this->json($response, ['message' => 'No se recibió ningún archivo.'], 422);
        }

        $file    = $files['avatar'];
        $ext     = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed)) {
            return $this->json($response, ['message' => 'Formato de imagen no permitido.'], 422);
        }

        $filename  = 'avatar_' . $user->id . '_' . time() . '.' . $ext;
        $uploadDir = __DIR__ . '/../../public/uploads/avatars/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file->moveTo($uploadDir . $filename);
        $user->profile_picture = 'uploads/avatars/' . $filename;
        $user->save();

        return $this->json($response, [
            'message'         => 'Avatar actualizado.',
            'profile_picture' => $user->profile_picture,
        ]);
    }

    // ── PUT /api/users/me/password ────────────────────────────────────────────
    public function updatePassword(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $user     = User::find($authUser['sub']);
        $body     = (array) $request->getParsedBody();

        $current = $body['current_password'] ?? '';
        $new     = $body['new_password']     ?? '';

        if ($user->password !== $current) {
            return $this->json($response, ['message' => 'La contraseña actual es incorrecta.'], 422);
        }

        if (strlen($new) < 6) {
            return $this->json($response, ['message' => 'La nueva contraseña debe tener al menos 6 caracteres.'], 422);
        }

        $user->password = $new;
        $user->save();

        return $this->json($response, ['message' => 'Contraseña actualizada.']);
    }

    // ── DELETE /api/users/me ──────────────────────────────────────────────────
    public function deactivate(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $user     = User::find($authUser['sub']);

        $user->status = 'inactive';
        $user->save();

        return $this->json($response, ['message' => 'Cuenta desactivada.']);
    }

    private function json(Response $response, mixed $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
