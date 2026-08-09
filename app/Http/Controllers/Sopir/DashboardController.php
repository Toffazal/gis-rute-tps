<?php

namespace App\Http\Controllers\Sopir;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\RuteHarian;
use App\Models\RuteTujuan;
use App\Models\Website;
use App\Services\AcoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $hariIni = $this->getNamaHari($today->dayOfWeek);

        $website = Website::first();
        $kantorCoord = json_decode($website->koordinat_kantor, true) ?? [3.593147, 98.4879093];
        $tpaCoord = json_decode($website->koordinat_tpa, true) ?? [3.593147, 98.4879093];

        // Cek apakah sudah ada rute_harian untuk hari ini
        $ruteHarian = RuteHarian::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$ruteHarian) {
            // Ambil jadwal hari ini untuk sopir ini
            $jadwalHariIni = Jadwal::with('tps', 'angkutan')
                ->where('user_id', $user->id)
                ->where('hari', $hariIni)
                ->get();

            if ($jadwalHariIni->isNotEmpty()) {
                // Ambil angkutan dari jadwal pertama (asumsi 1 angkutan per hari)
                $angkutan = $jadwalHariIni->first()->angkutan;

                // Kumpulkan TPS unik dari jadwal
                $tpsList = $jadwalHariIni->map(fn($j) => $j->tps)->unique('id')->values()->all();

                if (count($tpsList) > 0) {
                    // Jalankan ACO
                    $acoService = new AcoService();
                    $result = $acoService->optimize($kantorCoord, $tpaCoord, $tpsList);

                    // Buat rute_harian
                    $ruteHarian = RuteHarian::create([
                        'user_id' => $user->id,
                        'angkutan_id' => $angkutan->id ?? null,
                        'tanggal' => $today,
                        'status' => 'belum_mulai',
                    ]);

                    // Buat rute_tujuan berdasarkan urutan ACO
                    $urutan = 1;
                    $prevCoord = $kantorCoord;

                    foreach ($result['urutan'] as $tps) {
                        // Cari jarak dari titik sebelumnya
                        $detailIdx = $urutan - 1;
                        $jarakDariSebelumnya = $result['detail_jarak'][$detailIdx]['jarak'] ?? 0;

                        RuteTujuan::create([
                            'rute_harian_id' => $ruteHarian->id,
                            'tps_id' => $tps->id,
                            'tipe' => 'tps',
                            'urutan' => $urutan,
                            'status' => 'menunggu',
                            'jarak_dari_sebelumnya_meter' => $jarakDariSebelumnya,
                        ]);

                        $urutan++;
                    }

                    // Tambahkan TPA sebagai tujuan terakhir
                    $jarakTerakhir = !empty($result['detail_jarak']) ? end($result['detail_jarak'])['jarak'] : 0;
                    RuteTujuan::create([
                        'rute_harian_id' => $ruteHarian->id,
                        'tps_id' => null,
                        'tipe' => 'tpa',
                        'urutan' => $urutan,
                        'status' => 'menunggu',
                        'jarak_dari_sebelumnya_meter' => $jarakTerakhir,
                    ]);

                    // Update total jarak
                    $ruteHarian->update([
                        'total_jarak_meter' => $result['total_jarak'],
                    ]);
                }
            }
        }

        // Reload rute harian with relations
        $ruteHarian = RuteHarian::with(['ruteTujuan' => function ($q) {
                $q->orderBy('urutan', 'asc');
            }, 'ruteTujuan.tps', 'angkutan'])
            ->where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Determine active/next destination
        $tujuanAktif = null;
        if ($ruteHarian) {
            $tujuanAktif = $ruteHarian->ruteTujuan
                ->whereIn('status', ['aktif'])
                ->first();

            if (!$tujuanAktif) {
                $tujuanAktif = $ruteHarian->ruteTujuan
                    ->where('status', 'menunggu')
                    ->first();
            }
        }

        return view('sopir.dashboard', [
            'title' => 'Dashboard Sopir',
            'active' => 'dashboard',
            'user' => $user,
            'ruteHarian' => $ruteHarian,
            'tujuanAktif' => $tujuanAktif,
            'kantorCoord' => $kantorCoord,
            'tpaCoord' => $tpaCoord,
            'hariIni' => $hariIni,
        ]);
    }

    /**
     * Convert day-of-week number to Indonesian day name.
     */
    private function getNamaHari(int $dayOfWeek): string
    {
        $hari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        return $hari[$dayOfWeek] ?? 'Senin';
    }
}
