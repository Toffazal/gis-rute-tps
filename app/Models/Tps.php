<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tps extends Model
{
    protected $table = 'tps';

    protected $fillable = [
        'user_id',
        'nama',
        'alamat',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ruteTujuan()
    {
        return $this->hasMany(RuteTujuan::class, 'tps_id');
    }

    public function matriksAsal()
    {
        return $this->hasMany(MatriksJarak::class, 'asal_id');
    }

    public function matriksTujuan()
    {
        return $this->hasMany(MatriksJarak::class, 'tujuan_id');
    }
}
