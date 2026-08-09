<?php

namespace App\Http\Controllers\Sopir;

use App\Http\Controllers\Controller;
use App\Models\RuteHarian;
use App\Models\Website;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $riwayat = RuteHarian::with(['angkutan', 'ruteTujuan'])
            ->where('user_id', $user->id)
            ->where('status', 'selesai')
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('sopir.riwayat.index', [
            'title' => 'Riwayat Pengangkutan',
            'active' => 'riwayat',
            'user' => $user,
            'riwayat' => $riwayat,
        ]);
    }

    public function show(RuteHarian $ruteHarian)
    {
        $user = Auth::user();

        if ($ruteHarian->user_id !== $user->id) {
            abort(403);
        }

        $ruteHarian->load([
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

        return view('sopir.riwayat.show', [
            'title' => 'Detail Riwayat Rute',
            'active' => 'riwayat',
            'user' => $user,
            'ruteHarian' => $ruteHarian,
            'kantorCoord' => $kantorCoord,
            'tpaCoord' => $tpaCoord,
        ]);
    }
}
