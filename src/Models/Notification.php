<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'entity_id',
        'entity_type',
        'body',
        'is_read',
        'created_at',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id')
                    ->select(['id', 'name', 'username', 'profile_picture']);
    }

    // ─── Métodos de negocio ───────────────────────────────────────────────────

    /**
     * Crear una notificación. Evita duplicados recientes (mismo tipo + entidad, < 1 min).
     * Nota: se llama notify() porque Eloquent ya usa push() internamente.
     */
    public static function notify(
        int    $userId,
        string $type,
        ?int   $actorId     = null,
        ?int   $entityId    = null,
        ?string $entityType = null,
        ?string $body       = null
    ): void {
        // No notificarse a uno mismo
        if ($actorId && $actorId === $userId) return;

        // Evitar spam: si ya existe la misma notif en el último minuto, ignorar
        $recent = self::where('user_id', $userId)
            ->where('type', $type)
            ->where('actor_id', $actorId)
            ->where('entity_id', $entityId)
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 minute')))
            ->exists();

        if ($recent) return;

        self::create([
            'user_id'     => $userId,
            'actor_id'    => $actorId,
            'type'        => $type,
            'entity_id'   => $entityId,
            'entity_type' => $entityType,
            'body'        => $body,
            'is_read'     => false,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Últimas N notificaciones de un usuario con info del actor.
     */
    public static function forUser(int $userId, int $limit = 20): \Illuminate\Support\Collection
    {
        return self::where('user_id', $userId)
            ->with('actor')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Conteo de no leídas.
     */
    public static function unreadCount(int $userId): int
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Marcar todas como leídas.
     */
    public static function markAllRead(int $userId): void
    {
        self::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}