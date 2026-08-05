<?php

namespace App\Http\Controllers\Sopir;

use App\Http\Controllers\Controller;
use App\Models\Tps;
use App\Models\Riwayat;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tps = Tps::count();
        $riwayat = Riwayat::where('user', Auth::user()->id)->count();
        return view('sopir.dashboard', [
            'title' => 'Dashboard Sopir',
            'active' => 'dashboard',
            'tps' => $tps,
            'riwayat' => $riwayat,
            'user' => Auth::user(),
        ]);
    }
}
