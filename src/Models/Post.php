<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table      = 'posts';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'user_id',
        'category_id',
        'content',
        'content_type',
        'media_path',
        'likes',
        'comments_count',
        'status',
    ];

    protected $casts = [
        'likes'          => 'integer',
        'comments_count' => 'integer',
        'created_at'     => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id');
    }

    // ─── Métodos de negocio ───────────────────────────────────

    /**
     * Feed público ordenado por fecha
     */
    public static function feed(): \Illuminate\Database\Eloquent\Collection
    {
        return self::with(['user:id,name,username,profile_picture', 'category:id,name'])
            ->where('status', 'public')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Todas las publicaciones para el panel admin (incluye ocultas)
     */
    public static function adminFeed(): \Illuminate\Database\Eloquent\Collection
    {
        return self::with(['user:id,name,username', 'category:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Buscar posts por contenido o nombre de autor
     */
    public static function search(string $q): \Illuminate\Database\Eloquent\Collection
    {
        if (empty(trim($q))) {
            return self::feed();
        }

        $term = '%' . trim($q) . '%';

        return self::with(['user:id,name,username,profile_picture', 'category:id,name'])
            ->where('status', 'public')
            ->where(function ($query) use ($term) {
                $query->where('content', 'LIKE', $term)
                      ->orWhereHas('user', fn($q) => $q->where('name', 'LIKE', $term));
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Posts de un usuario específico
     */
    public static function byUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Cambiar estado (public / hidden)
     */
    public function changeStatus(string $status): void
    {
        $this->update(['status' => $status]);
    }
}
