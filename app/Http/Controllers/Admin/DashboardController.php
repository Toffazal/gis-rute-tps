<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tps;
use App\Models\User;
use App\Models\Angkutan;
use App\Models\RuteHarian;
use App\Models\RuteTujuan;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $website = Website::first();

        // 1. Stats Cards
        $tpsCount = Tps::count();
        $sopirCount = User::where('role', 'sopir')->count();
        $angkutanCount = Angkutan::count();
        
        $today = Carbon::today();
        $ruteAktifHariIni = RuteHarian::whereDate('tanggal', $today)->where('status', 'berlangsung')->count();
        $ruteSelesaiHariIni = RuteHarian::whereDate('tanggal', $today)->where('status', 'selesai')->count();

        // 2. Chart 1: Rute Harian 7 Hari Terakhir
        $chartHistoryLabels = [];
        $chartHistoryCount = [];
        $chartHistoryJarak = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartHistoryLabels[] = $date->translatedFormat('d M');
            
            $routes = RuteHarian::whereDate('tanggal', $date)->where('status', 'selesai')->get();
            $chartHistoryCount[] = $routes->count();
            
            $totalJarakKm = $routes->sum('total_jarak_meter') / 1000;
            $chartHistoryJarak[] = round($totalJarakKm, 1);
        }

        // 3. Chart 2: Status Pengangkutan TPS Hari Ini
        $todayRouteIds = RuteHarian::whereDate('tanggal', $today)->pluck('id');
        
        $tpsSelesai = RuteTujuan::whereIn('rute_harian_id', $todayRouteIds)
            ->where('tipe', 'tps')
            ->where('status', 'selesai')
            ->count();
            
        $tpsSkip = RuteTujuan::whereIn('rute_harian_id', $todayRouteIds)
            ->where('tipe', 'tps')
            ->whereIn('status', ['dilewati', 'tidak_dikunjungi'])
            ->count();
            
        $tpsMenunggu = RuteTujuan::whereIn('rute_harian_id', $todayRouteIds)
            ->where('tipe', 'tps')
            ->whereIn('status', ['menunggu', 'aktif'])
            ->count();

        // 4. Daftar Rute Aktif Hari Ini
        $activeRoutes = RuteHarian::with(['user', 'angkutan', 'ruteTujuan.tps'])
            ->whereDate('tanggal', $today)
            ->where('status', 'berlangsung')
            ->get();

        return view('admin.dashboard', [
            'title' => 'Dashboard Admin',
            'active' => 'dashboard',
            'user' => Auth::user(),
            'website' => $website,
            
            // Stats
            'tpsCount' => $tpsCount,
            'sopirCount' => $sopirCount,
            'angkutanCount' => $angkutanCount,
            'ruteAktifHariIni' => $ruteAktifHariIni,
            'ruteSelesaiHariIni' => $ruteSelesaiHariIni,
            
            // Chart 1
            'chartHistoryLabels' => $chartHistoryLabels,
            'chartHistoryCount' => $chartHistoryCount,
            'chartHistoryJarak' => $chartHistoryJarak,
            
            // Chart 2
            'tpsSelesai' => $tpsSelesai,
            'tpsSkip' => $tpsSkip,
            'tpsMenunggu' => $tpsMenunggu,
            
            // Active Routes
            'activeRoutes' => $activeRoutes,
        ]);
    }
}
