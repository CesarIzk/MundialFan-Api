<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table      = 'likes';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    // ─── Métodos de negocio ───────────────────────────────────

    /**
     * Verificar si un usuario ya dio like a un post
     */
    public static function exists(int $postId, int $userId): bool
    {
        return self::where('post_id', $postId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Dar like e incrementar contador del post
     */
    public static function give(int $postId, int $userId): void
    {
        self::create([
            'post_id' => $postId,
            'user_id' => $userId,
        ]);

        Post::where('id', $postId)->increment('likes');
    }

    /**
     * Quitar like y decrementar contador del post
     */
    public static function remove(int $postId, int $userId): void
    {
        self::where('post_id', $postId)
            ->where('user_id', $userId)
            ->delete();

        Post::where('id', $postId)
            ->where('likes', '>', 0)
            ->decrement('likes');
    }

    /**
     * Toggle: si ya tiene like lo quita, si no lo da.
     * Devuelve true si ahora tiene like, false si se quitó.
     */
    public static function toggle(int $postId, int $userId): bool
    {
        if (self::exists($postId, $userId)) {
            self::remove($postId, $userId);
            return false;
        }

        self::give($postId, $userId);
        return true;
    }
}
