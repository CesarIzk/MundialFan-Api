<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    public    $timestamps = false;   // la tabla maneja created_at/last_activity manualmente

    // Columnas asignables en masa
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'birth_date',
        'gender',
        'city',
        'country',
        'profile_picture',
        'bio',
        'status',
        'total_posts',
    ];

    // Ocultar password en respuestas JSON
    protected $hidden = ['password'];

    // Cast de tipos
    protected $casts = [
        'birth_date'    => 'date',
        'created_at'    => 'datetime',
        'last_activity' => 'datetime',
        'total_posts'   => 'integer',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id');
    }

    // ─── Métodos de negocio ───────────────────────────────────

    /**
     * Buscar usuario por email
     */
    public static function findByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    /**
     * Buscar usuario por username
     */
    public static function findByUsername(string $username): ?self
    {
        return self::where('username', $username)->first();
    }

    /**
     * Autenticación (texto plano según requerimiento del proyecto)
     */
    public static function attempt(string $email, string $password): ?self
    {
        $user = self::findByEmail($email);

        if ($user && $user->password === $password) {
            return $user;
        }

        return null;
    }

    /**
     * Buscar usuarios por nombre o email (excluyendo al usuario actual)
     */
    public static function search(string $q, int $excludeId): \Illuminate\Database\Eloquent\Collection
    {
        if (empty(trim($q))) {
            return collect();
        }

        $term = '%' . trim($q) . '%';

        return self::where(function ($query) use ($term) {
            $query->where('name', 'LIKE', $term)
                  ->orWhere('email', 'LIKE', $term);
        })
        ->where('id', '!=', $excludeId)
        ->select('id', 'name', 'username', 'email', 'profile_picture')
        ->limit(20)
        ->get();
    }
}
