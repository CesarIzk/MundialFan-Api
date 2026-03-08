<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedPlayer extends Model
{
    protected $table      = 'featured_players';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'name',
        'country',
        'achievements',
        'photo',
    ];

    // ─── Métodos de negocio ───────────────────────────────────

    /**
     * Jugadores ordenados por nombre
     */
    public static function allSorted(): \Illuminate\Database\Eloquent\Collection
    {
        return self::orderBy('name', 'asc')->get();
    }

    /**
     * Jugadores por país
     */
    public static function byCountry(string $country): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('country', $country)
            ->orderBy('name', 'asc')
            ->get();
    }
}
