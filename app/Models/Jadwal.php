<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $fillable = [
        'user_id',
        'tps_id',
        'angkutan_id',
        'hari',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tps()
    {
        return $this->belongsTo(Tps::class, 'tps_id');
    }

    public function angkutan()
    {
        return $this->belongsTo(Angkutan::class, 'angkutan_id');
    }
}
