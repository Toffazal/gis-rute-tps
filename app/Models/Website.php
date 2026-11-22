<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $table = 'website';

    protected $fillable = [
        'nama',
        'deskripsi',
        'alamat',
        'telepon',
        'email',
        'logo',
        'kopSurat',
        'koordinat_tpa', //[latitude, longitude]
        'koordinat_kantor', //[latitude, longitude]
    ];
}
