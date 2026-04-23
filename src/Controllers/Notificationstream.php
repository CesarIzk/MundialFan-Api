<?php

namespace App\Controllers;

use App\Models\Notification;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotificationStream
{
    /**
     * GET /api/users/me/notifications/stream
     *
     * Endpoint Server-Sent Events (SSE).
     * El cliente conecta una vez y recibe eventos cada vez que cambia
     * el conteo de notificaciones no leídas, sin hacer polling.
     *
     * Requiere el JWT como query param ?token=... porque EventSource
     * no soporta headers personalizados.
     */
    public function stream(Request $request, Response $response): Response
    {
        // ── Autenticar via query param (EventSource no soporta headers) ───────
        $token  = $request->getQueryParams()['token'] ?? '';
        $userId = $this->resolveUserId($token);

        if (!$userId) {
            return $response->withStatus(401);
        }

        // ── Deshabilitar límites de tiempo y buffering ─────────────────────────
        @set_time_limit(0);
        @ini_set('output_buffering', 'off');
        @ini_set('zlib.output_compression', false);

        // ── Headers SSE ───────────────────────────────────────────────────────
        // Escribimos los headers directamente porque Slim bufferiza la respuesta
        // y SSE necesita streaming inmediato.
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');   // Para Nginx
        header('Connection: keep-alive');
        // CORS si el front corre en origen diferente
        header('Access-Control-Allow-Origin: *');

        // Limpiar cualquier buffer previo
        while (ob_get_level()) ob_end_clean();
        ob_implicit_flush(true);

        $lastCount = -1;
        $ticks     = 0;

        while (true) {
            // Cada 30 ticks (~30s) enviar heartbeat para mantener conexión viva
            $ticks++;
            if ($ticks % 30 === 0) {
                echo ": heartbeat\n\n";
                flush();
            }

            $count = Notification::unreadCount($userId);

            // Solo emitir evento si el conteo cambió
            if ($count !== $lastCount) {
                $lastCount = $count;
                echo "event: notif-count\n";
                echo "data: " . json_encode(['count' => $count]) . "\n\n";
                flush();
            }

            // Verificar que el cliente sigue conectado
            if (connection_aborted()) break;

            sleep(1); // Revisar BD cada segundo
        }

        // SSE no usa la respuesta de Slim, pero necesitamos retornar algo
        exit(0);
    }

    private function resolveUserId(string $token): ?int
    {
        if (!$token) return null;
        try {
            $secret  = $_ENV['JWT_SECRET'] ?? 'secret';
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return (int) ($decoded->sub ?? 0) ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}