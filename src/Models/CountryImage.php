<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryImage extends Model
{
    protected $table      = 'country_images';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'country_id',
        'url',
        'description',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
