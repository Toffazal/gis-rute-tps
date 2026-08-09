<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Angkutan extends Model
{
    protected $table = 'angkutan';

    protected $fillable = [
        'nama',
        'nomor',
    ];

    public function ruteHarian()
    {
        return $this->hasMany(RuteHarian::class, 'angkutan_id');
    }
}
