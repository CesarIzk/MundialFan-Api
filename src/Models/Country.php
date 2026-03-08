<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table      = 'countries';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'continent',
        'flag',
        'description',
        'history',
        'titles',
        'participations',
        'coach',
        'best_player',
        'featured_video',
    ];

    protected $casts = [
        'titles'         => 'integer',
        'participations' => 'integer',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function images()
    {
        return $this->hasMany(CountryImage::class, 'country_id');
    }

    public function videos()
    {
        return $this->hasMany(CountryVideo::class, 'country_id');
    }

    // ─── Métodos de negocio ───────────────────────────────────

    /**
     * Buscar país por código
     */
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    /**
     * Países ordenados por títulos (ranking)
     */
    public static function ranking(): \Illuminate\Database\Eloquent\Collection
    {
        return self::orderBy('titles', 'desc')
            ->orderBy('participations', 'desc')
            ->get();
    }

    /**
     * Países por continente
     */
    public static function byContinent(string $continent): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('continent', $continent)
            ->orderBy('name', 'asc')
            ->get();
    }
}
