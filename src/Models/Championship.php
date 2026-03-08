<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Championship extends Model
{
    protected $table      = 'championships';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'year',
        'host_country',
        'flag',
        'champion',
        'runner_up',
        'total_goals',
        'participating_teams',
        'description',
    ];

    protected $casts = [
        'year'                => 'integer',
        'total_goals'         => 'integer',
        'participating_teams' => 'integer',
    ];

    // ─── Métodos de negocio ───────────────────────────────────

    /**
     * Todos los campeonatos ordenados por año
     */
    public static function allSorted(string $direction = 'desc'): \Illuminate\Database\Eloquent\Collection
    {
        return self::orderBy('year', $direction)->get();
    }

    /**
     * Campeonatos ganados por un país
     */
    public static function byChampion(string $champion): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('champion', $champion)
            ->orderBy('year', 'asc')
            ->get();
    }
}
