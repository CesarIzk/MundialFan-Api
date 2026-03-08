<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table      = 'categories';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }

    // ─── Métodos de negocio ───────────────────────────────────

    /**
     * Todas las categorías ordenadas por nombre
     */
    public static function allSorted(): \Illuminate\Database\Eloquent\Collection
    {
        return self::orderBy('name', 'asc')->get();
    }
}
