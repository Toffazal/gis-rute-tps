<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RuteHarian;
use App\Models\User;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = RuteHarian::with(['user', 'angkutan', 'ruteTujuan']);

        // Filter: Sopir
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter: Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Tanggal (Daterange)
        if ($request->filled('daterange')) {
            try {
                $dates = explode(' - ', $request->daterange);
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
            } catch (\Exception $e) {
                // If parsing fails, ignore filter
            }
        }

        $riwayat = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $sopirList = User::where('role', 'sopir')->orderBy('name', 'asc')->get();

        return view('admin.riwayat.index', [
            'title' => 'Riwayat & Monitoring Rute',
            'active' => 'riwayat',
            'riwayat' => $riwayat,
            'sopirList' => $sopirList,
            'user' => Auth::user(),
            'website' => Website::first(),
        ]);
    }

    public function cetak(Request $request)
    {
        $query = RuteHarian::with(['user', 'angkutan', 'ruteTujuan']);

        // Filter: Sopir
        $selectedSopir = null;
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
            $selectedSopir = User::find($request->user_id);
        }

        // Filter: Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Tanggal (Daterange)
        $periodeText = 'Semua Periode';
        if ($request->filled('daterange')) {
            try {
                $dates = explode(' - ', $request->daterange);
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                    $periodeText = $startDate->translatedFormat('d F Y') . ' s/d ' . $endDate->translatedFormat('d F Y');
                }
            } catch (\Exception $e) {
                // If parsing fails, ignore filter
            }
        }

        $riwayat = $query->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $website = Website::first();

        // Hitung statistik untuk summary di laporan
        $totalPerjalanan = $riwayat->count();
        $totalJarakMeter = $riwayat->sum('total_jarak_meter');
        $totalDurasiDetik = $riwayat->sum(function ($item) {
            return abs($item->total_durasi_detik ?? 0);
        });
        $totalTps = $riwayat->sum(function ($item) {
            return $item->ruteTujuan->where('tipe', 'tps')->count();
        });

        return view('admin.riwayat.cetak', [
            'title' => 'Laporan Riwayat Pengangkutan Sampah',
            'riwayat' => $riwayat,
            'website' => $website,
            'periodeText' => $periodeText,
            'selectedSopir' => $selectedSopir,
            'statusFilter' => $request->status,
            'totalPerjalanan' => $totalPerjalanan,
            'totalJarakMeter' => $totalJarakMeter,
            'totalDurasiDetik' => $totalDurasiDetik,
            'totalTps' => $totalTps,
            'user' => Auth::user(),
        ]);
    }

    public function cetakShow(RuteHarian $ruteHarian)
    {
        $ruteHarian->load([
            'user',
            'angkutan',
            'ruteTujuan' => fn($q) => $q->orderBy('urutan', 'asc'),
            'ruteTujuan.tps',
            'ruteLog' => fn($q) => $q->orderBy('created_at', 'asc'),
            'ruteLog.ruteTujuan',
            'ruteLog.ruteTujuan.tps',
        ]);

        $website = Website::first();

        return view('admin.riwayat.cetak-show', [
            'title' => 'Cetak Detail Aktivitas Rute - ' . ($ruteHarian->user->name ?? 'Sopir'),
            'ruteHarian' => $ruteHarian,
            'website' => $website,
            'user' => Auth::user(),
        ]);
    }

    public function show(RuteHarian $ruteHarian)
    {
        $ruteHarian->load([
            'user',
            'angkutan',
            'ruteTujuan' => fn($q) => $q->orderBy('urutan', 'asc'),
            'ruteTujuan.tps',
            'ruteLog' => fn($q) => $q->orderBy('created_at', 'asc'),
            'ruteLog.ruteTujuan',
            'ruteLog.ruteTujuan.tps',
        ]);

        $website = Website::first();
        $kantorCoord = json_decode($website->koordinat_kantor, true) ?? [3.593147, 98.4879093];
        $tpaCoord = json_decode($website->koordinat_tpa, true) ?? [3.593147, 98.4879093];

        return view('admin.riwayat.show', [
            'title' => 'Detail Monitoring Rute - ' . ($ruteHarian->user->name ?? 'Sopir'),
            'active' => 'riwayat',
            'ruteHarian' => $ruteHarian,
            'kantorCoord' => $kantorCoord,
            'tpaCoord' => $tpaCoord,
            'user' => Auth::user(),
            'website' => $website,
        ]);
    }
}
