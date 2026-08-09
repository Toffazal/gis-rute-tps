<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuteHarian extends Model
{
    protected $table = 'rute_harian';

    protected $fillable = [
        'user_id',
        'angkutan_id',
        'tanggal',
        'status',
        'waktu_mulai',
        'waktu_selesai',
        'total_jarak_meter',
        'total_durasi_detik',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function angkutan()
    {
        return $this->belongsTo(Angkutan::class, 'angkutan_id');
    }

    public function ruteTujuan()
    {
        return $this->hasMany(RuteTujuan::class, 'rute_harian_id');
    }

    public function ruteLog()
    {
        return $this->hasMany(RuteLog::class, 'rute_harian_id');
    }
}
