<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuccessfulTeam extends Model
{
    protected $table      = 'successful_teams';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'name',
        'flag',
        'titles',
    ];

    protected $casts = [
        'titles' => 'integer',
    ];

    // ─── Métodos de negocio ───────────────────────────────────

    /**
     * Ranking de equipos por títulos
     */
    public static function ranking(): \Illuminate\Database\Eloquent\Collection
    {
        return self::orderBy('titles', 'desc')->get();
    }
}
