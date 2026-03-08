<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminController
{
    // ── GET /api/admin/posts ──────────────────────────────────────────────────
    public function posts(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);
        return $this->json($response, Post::adminFeed());
    }

    // ── PUT /api/admin/posts/{id}/status ─────────────────────────────────────
    public function changePostStatus(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $post = Post::find($args['id']);
        if (!$post) {
            return $this->json($response, ['message' => 'Publicación no encontrada.'], 404);
        }

        $body   = (array) $request->getParsedBody();
        $status = $body['status'] ?? '';

        if (!in_array($status, ['public', 'hidden'])) {
            return $this->json($response, ['message' => 'Estado inválido. Use: public | hidden'], 422);
        }

        $post->changeStatus($status);

        return $this->json($response, ['message' => "Publicación marcada como '$status'."]);
    }

    // ── DELETE /api/admin/posts/{id} ──────────────────────────────────────────
    public function deletePost(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $post = Post::find($args['id']);
        if (!$post) {
            return $this->json($response, ['message' => 'Publicación no encontrada.'], 404);
        }

        $post->delete();

        return $this->json($response, ['message' => 'Publicación eliminada.']);
    }

    // ── GET /api/admin/users ──────────────────────────────────────────────────
    public function users(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $users = User::select('id', 'name', 'username', 'email', 'role', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->json($response, $users);
    }

    // ── PUT /api/admin/users/{id}/status ─────────────────────────────────────
    public function changeUserStatus(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $user = User::find($args['id']);
        if (!$user) {
            return $this->json($response, ['message' => 'Usuario no encontrado.'], 404);
        }

        $body   = (array) $request->getParsedBody();
        $status = $body['status'] ?? '';

        if (!in_array($status, ['active', 'inactive'])) {
            return $this->json($response, ['message' => 'Estado inválido. Use: active | inactive'], 422);
        }

        $user->status = $status;
        $user->save();

        return $this->json($response, ['message' => "Usuario marcado como '$status'."]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function requireAdmin(Request $request): void
    {
        $authUser = $request->getAttribute('auth_user');
        if (!$authUser || $authUser['role'] !== 'admin') {
            throw new \Slim\Exception\HttpForbiddenException($request, 'Acceso restringido a administradores.');
        }
    }

    private function json(Response $response, mixed $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
