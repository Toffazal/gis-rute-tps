<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuteLog extends Model
{
    protected $table = 'rute_log';

    protected $fillable = [
        'rute_harian_id',
        'rute_tujuan_id',
        'aksi',
        'alasan',
        'urutan_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'urutan_snapshot' => 'array',
        ];
    }

    public function ruteHarian()
    {
        return $this->belongsTo(RuteHarian::class, 'rute_harian_id');
    }

    public function ruteTujuan()
    {
        return $this->belongsTo(RuteTujuan::class, 'rute_tujuan_id');
    }
}
