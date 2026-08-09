<?php

namespace App\Http\Controllers\Sopir;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Ambil jadwal sopir ini, grouped by hari
        $jadwalAll = Jadwal::with(['tps', 'angkutan'])
            ->where('user_id', $user->id)
            ->get()
            ->groupBy('hari');

        return view('sopir.jadwal.index', [
            'title' => 'Jadwal Pengangkutan',
            'active' => 'jadwal',
            'user' => $user,
            'jadwalAll' => $jadwalAll,
            'hariList' => $hariList,
        ]);
    }
}
