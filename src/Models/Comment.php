<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table      = 'comments';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Métodos de negocio ───────────────────────────────────

    /**
     * Comentarios de un post con datos del autor
     */
    public static function byPost(int $postId): \Illuminate\Database\Eloquent\Collection
    {
        return self::with('user:id,name,username,profile_picture')
            ->where('post_id', $postId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Crear comentario e incrementar el contador del post
     */
    public static function createAndCount(int $postId, int $userId, string $content): self
    {
        $comment = self::create([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content,
        ]);

        // Incrementar contador en posts
        Post::where('id', $postId)->increment('comments_count');

        return $comment;
    }

    /**
     * Eliminar comentario y decrementar el contador del post
     */
    public function deleteAndCount(): void
    {
        $postId = $this->post_id;
        $this->delete();
        Post::where('id', $postId)->decrement('comments_count');
    }
}
