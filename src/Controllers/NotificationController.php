<?php

namespace App\Controllers;

use App\Models\Notification;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotificationController
{
    /**
     * GET /api/users/me/notifications
     * Devuelve las últimas notificaciones del usuario autenticado.
     * Query param: ?limit=20
     */
    public function index(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $userId   = (int) $authUser['sub'];

        $limit = min((int) ($request->getQueryParams()['limit'] ?? 20), 50);

        $notifications = Notification::forUser($userId, $limit)
            ->map(fn($n) => [
                'id'          => $n->id,
                'type'        => $n->type,
                'entity_id'   => $n->entity_id,
                'entity_type' => $n->entity_type,
                'body'        => $n->body,
                'is_read'     => $n->is_read,
                'created_at'  => $n->created_at,
                'actor'       => $n->actor ? [
                    'id'              => $n->actor->id,
                    'name'            => $n->actor->name,
                    'username'        => $n->actor->username,
                    'profile_picture' => $n->actor->profile_picture,
                ] : null,
            ]);

        return $this->json($response, ['notifications' => $notifications]);
    }

    /**
     * GET /api/users/me/notifications/unread-count
     * Devuelve solo el conteo de no leídas (para el badge de la campana).
     */
    public function unreadCount(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $userId   = (int) $authUser['sub'];

        return $this->json($response, [
            'count' => Notification::unreadCount($userId),
        ]);
    }

    /**
     * POST /api/users/me/notifications/read
     * Marca todas las notificaciones del usuario como leídas.
     */
    public function markAllRead(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $userId   = (int) $authUser['sub'];

        Notification::markAllRead($userId);

        return $this->json($response, ['message' => 'Notificaciones marcadas como leídas.']);
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function json(Response $response, mixed $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}