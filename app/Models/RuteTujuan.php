<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuteTujuan extends Model
{
    protected $table = 'rute_tujuan';

    protected $fillable = [
        'rute_harian_id',
        'tps_id',
        'tipe',
        'urutan',
        'status',
        'alasan',
        'jarak_dari_sebelumnya_meter',
        'durasi_dari_sebelumnya_detik',
        'waktu_tiba',
        'waktu_selesai',
    ];

    protected function casts(): array
    {
        return [
            'waktu_tiba' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function ruteHarian()
    {
        return $this->belongsTo(RuteHarian::class, 'rute_harian_id');
    }

    public function tps()
    {
        return $this->belongsTo(Tps::class, 'tps_id');
    }

    public function ruteLog()
    {
        return $this->hasMany(RuteLog::class, 'rute_tujuan_id');
    }
}
