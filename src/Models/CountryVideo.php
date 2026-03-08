<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryVideo extends Model
{
    protected $table      = 'country_videos';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'country_id',
        'url',
        'title',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
