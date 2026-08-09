<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatriksJarak extends Model
{
    protected $table = 'matriks_jarak';

    protected $fillable = [
        'asal_tipe',
        'asal_id',
        'tujuan_tipe',
        'tujuan_id',
        'jarak_meter',
        'durasi_detik',
    ];

    public function asalTps()
    {
        return $this->belongsTo(Tps::class, 'asal_id');
    }

    public function tujuanTps()
    {
        return $this->belongsTo(Tps::class, 'tujuan_id');
    }
}
