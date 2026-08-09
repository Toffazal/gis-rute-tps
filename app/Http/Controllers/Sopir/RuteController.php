<?php

namespace App\Http\Controllers\Sopir;

use App\Http\Controllers\Controller;
use App\Models\RuteHarian;
use App\Models\RuteLog;
use App\Models\RuteTujuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Website;
use App\Services\AcoService;

class RuteController extends Controller
{
    /**
     * Mulai rute harian (belum_mulai -> berlangsung).
     */
    public function mulaiRute(RuteHarian $ruteHarian)
    {
        // Pastikan rute milik sopir yang login
        if ($ruteHarian->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ruteHarian->status !== 'belum_mulai') {
            return back()->with('error', 'Rute sudah dimulai atau sudah selesai.');
        }

        $ruteHarian->update([
            'status' => 'berlangsung',
            'waktu_mulai' => Carbon::now(),
        ]);

        // Set tujuan pertama menjadi aktif
        $tujuanPertama = $ruteHarian->ruteTujuan()->orderBy('urutan', 'asc')->first();
        if ($tujuanPertama) {
            $tujuanPertama->update(['status' => 'aktif']);
        }

        // Log
        RuteLog::create([
            'rute_harian_id' => $ruteHarian->id,
            'rute_tujuan_id' => $tujuanPertama?->id,
            'aksi' => 'mulai_rute',
        ]);

        return back()->with('success', 'Rute berhasil dimulai! Silakan menuju tujuan pertama.');
    }

    /**
     * Tandai tiba di tujuan.
     */
    public function tiba(RuteTujuan $ruteTujuan)
    {
        $ruteHarian = $ruteTujuan->ruteHarian;

        if ($ruteHarian->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ruteTujuan->status !== 'aktif') {
            return back()->with('error', 'Tujuan ini belum aktif.');
        }

        $ruteTujuan->update([
            'waktu_tiba' => Carbon::now(),
        ]);

        // Log
        RuteLog::create([
            'rute_harian_id' => $ruteHarian->id,
            'rute_tujuan_id' => $ruteTujuan->id,
            'aksi' => 'tiba',
        ]);

        return back()->with('success', 'Anda telah tiba di ' . ($ruteTujuan->tps->nama ?? 'TPA') . '.');
    }

    /**
     * Tandai selesai pengangkutan di tujuan, lanjut ke tujuan berikutnya.
     */
    public function selesai(RuteTujuan $ruteTujuan)
    {
        $ruteHarian = $ruteTujuan->ruteHarian;

        if ($ruteHarian->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ruteTujuan->status !== 'aktif') {
            return back()->with('error', 'Tujuan ini belum aktif.');
        }

        $ruteTujuan->update([
            'status' => 'selesai',
            'waktu_selesai' => Carbon::now(),
        ]);

        // Log
        RuteLog::create([
            'rute_harian_id' => $ruteHarian->id,
            'rute_tujuan_id' => $ruteTujuan->id,
            'aksi' => 'selesai',
        ]);

        // Aktifkan tujuan berikutnya
        $this->activateNext($ruteHarian, $ruteTujuan->urutan);

        return back()->with('success', 'Pengangkutan selesai di ' . ($ruteTujuan->tps->nama ?? 'TPA') . '.');
    }

    /**
     * Skip tujuan dengan alasan.
     */
    public function skip(Request $request, RuteTujuan $ruteTujuan)
    {
        $ruteHarian = $ruteTujuan->ruteHarian;

        if ($ruteHarian->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($ruteTujuan->status, ['aktif', 'menunggu'])) {
            return back()->with('error', 'Tujuan ini tidak dapat dilewati.');
        }

        $request->validate([
            'alasan' => 'required|string|max:500',
            'status_skip' => 'required|in:dilewati,tidak_dikunjungi',
        ]);

        $statusSkip = $request->status_skip;

        $ruteTujuan->update([
            'status' => $statusSkip,
            'alasan' => $request->alasan,
        ]);

        // Log aksi
        $aksiLog = $statusSkip === 'tidak_dikunjungi' ? 'tidak_dikunjungi' : 'skip';
        RuteLog::create([
            'rute_harian_id' => $ruteHarian->id,
            'rute_tujuan_id' => $ruteTujuan->id,
            'aksi' => $aksiLog,
            'alasan' => $request->alasan,
        ]);

        if ($statusSkip === 'dilewati') {
            // Duplikat tujuan ini sebagai menunggu untuk dikunjungi nanti
            $newTujuan = RuteTujuan::create([
                'rute_harian_id' => $ruteHarian->id,
                'tps_id' => $ruteTujuan->tps_id,
                'tipe' => $ruteTujuan->tipe,
                'urutan' => 999, // Sementara, akan diatur ulang oleh reoptimizeRute
                'status' => 'menunggu',
            ]);

            // Hitung ulang rute paling ideal, tempatkan newTujuan di paling akhir
            $this->reoptimizeRute($ruteHarian, null, $newTujuan);
        } else {
            // Jika tidak_dikunjungi, langsung aktifkan tujuan berikutnya tanpa re-optimasi
            $this->activateNext($ruteHarian, $ruteTujuan->urutan);
        }

        return back()->with('success', 'Tujuan berhasil di-skip dengan status: ' . str_replace('_', ' ', $statusSkip));
    }

    /**
     * Selesaikan seluruh rute harian.
     */
    public function selesaiRute(RuteHarian $ruteHarian)
    {
        if ($ruteHarian->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ruteHarian->status !== 'berlangsung') {
            return back()->with('error', 'Rute belum dimulai atau sudah selesai.');
        }

        // Tandai semua tujuan yang masih menunggu sebagai tidak_dikunjungi
        $ruteHarian->ruteTujuan()
            ->whereIn('status', ['menunggu', 'aktif'])
            ->update(['status' => 'tidak_dikunjungi']);

        // Hitung total jarak dan durasi
        $totalJarak = $ruteHarian->ruteTujuan()
            ->whereIn('status', ['selesai'])
            ->sum('jarak_dari_sebelumnya_meter');

        $ruteHarian->update([
            'status' => 'selesai',
            'waktu_selesai' => Carbon::now(),
            'total_jarak_meter' => $totalJarak,
            'total_durasi_detik' => $ruteHarian->waktu_mulai
                ? (int) abs(Carbon::now()->diffInSeconds($ruteHarian->waktu_mulai))
                : 0,
        ]);

        // Log
        RuteLog::create([
            'rute_harian_id' => $ruteHarian->id,
            'aksi' => 'selesai',
        ]);

        return back()->with('success', 'Rute harian telah selesai.');
    }

    /**
     * Kunjungi tujuan secara manual (Reorder / Prioritas Mendekat).
     */
    public function mulaiManual(RuteTujuan $ruteTujuan)
    {
        $ruteHarian = $ruteTujuan->ruteHarian;

        if ($ruteHarian->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ruteHarian->status !== 'berlangsung') {
            return back()->with('error', 'Rute harian harus sedang berlangsung.');
        }

        $targetRecord = $ruteTujuan;

        if (in_array($ruteTujuan->status, ['dilewati', 'tidak_dikunjungi'])) {
            $existingMenunggu = $ruteHarian->ruteTujuan()
                ->where('tps_id', $ruteTujuan->tps_id)
                ->where('status', 'menunggu')
                ->first();

            if ($existingMenunggu) {
                $targetRecord = $existingMenunggu;
            } else {
                $targetRecord = RuteTujuan::create([
                    'rute_harian_id' => $ruteHarian->id,
                    'tps_id' => $ruteTujuan->tps_id,
                    'tipe' => $ruteTujuan->tipe,
                    'urutan' => 999,
                    'status' => 'menunggu',
                ]);
            }
        } elseif ($ruteTujuan->status !== 'menunggu') {
            return back()->with('error', 'Tujuan ini tidak dapat dikunjungi secara manual.');
        }

        // 1. Cari tujuan yang saat ini 'aktif' (jika ada), kembalikan ke 'menunggu'
        $currentActive = $ruteHarian->ruteTujuan()->where('status', 'aktif')->first();
        if ($currentActive) {
            $currentActive->update(['status' => 'menunggu']);
        }

        // 2. Jalankan reoptimizeRute dengan $startTujuan = $targetRecord
        $this->reoptimizeRute($ruteHarian, $targetRecord);

        // 3. Log aksi reorder_manual
        RuteLog::create([
            'rute_harian_id' => $ruteHarian->id,
            'rute_tujuan_id' => $targetRecord->id,
            'aksi' => 'reorder_manual',
            'alasan' => 'Dikunjungi terlebih dahulu secara manual (prioritas mendesak)',
        ]);

        return back()->with('success', 'Rute berhasil diatur ulang. Sekarang Anda diarahkan ke ' . ($targetRecord->tps->nama ?? 'TPA') . ' terlebih dahulu.');
    }

    /**
     * Re-optimize remaining route destinations.
     */
    private function reoptimizeRute(RuteHarian $ruteHarian, ?RuteTujuan $startTujuan = null, ?RuteTujuan $skippedTujuan = null)
    {
        $website = Website::first();
        $kantorCoord = json_decode($website->koordinat_kantor, true) ?? [3.593147, 98.4879093];
        $tpaCoord = json_decode($website->koordinat_tpa, true) ?? [3.593147, 98.4879093];

        // 1. Dapatkan semua completed / skipped / tidak_dikunjungi tujuan
        $completedTujuan = $ruteHarian->ruteTujuan()
            ->whereIn('status', ['selesai', 'dilewati', 'tidak_dikunjungi'])
            ->orderBy('urutan', 'asc')
            ->get();

        $maxCompletedUrutan = $completedTujuan->max('urutan') ?? 0;

        // 2. Dapatkan sisa TPS yang akan dikunjungi (status = menunggu)
        $remainingTpsQuery = $ruteHarian->ruteTujuan()
            ->where('tipe', 'tps')
            ->where('status', 'menunggu');

        if ($startTujuan) {
            $remainingTpsQuery->where('id', '!=', $startTujuan->id);
        }

        if ($skippedTujuan) {
            $remainingTpsQuery->where('id', '!=', $skippedTujuan->id);
        }

        $remainingTpsRecords = $remainingTpsQuery->get();

        // Kumpulkan data objek TPS asli untuk ACO
        $tpsList = $remainingTpsRecords->map(fn($r) => $r->tps)->filter()->values()->all();

        // 3. Tentukan starting node info
        $startNodeInfo = null;
        if ($startTujuan && $startTujuan->tipe === 'tps' && $startTujuan->tps) {
            $startNodeInfo = [
                'tipe' => 'tps',
                'id' => $startTujuan->tps->id,
                'nama' => $startTujuan->tps->nama,
                'coords' => [$startTujuan->tps->latitude, $startTujuan->tps->longitude]
            ];
        } else {
            // Cari tujuan 'selesai' terakhir
            $lastSelesai = $completedTujuan->where('status', 'selesai')->last();
            if ($lastSelesai && $lastSelesai->tps) {
                $startNodeInfo = [
                    'tipe' => 'tps',
                    'id' => $lastSelesai->tps->id,
                    'nama' => $lastSelesai->tps->nama,
                    'coords' => [$lastSelesai->tps->latitude, $lastSelesai->tps->longitude]
                ];
            } else {
                // Mulai dari kantor
                $startNodeInfo = [
                    'tipe' => 'kantor',
                    'id' => null,
                    'nama' => 'Kantor',
                    'coords' => $kantorCoord
                ];
            }
        }

        // 4. Jalankan ACO untuk sisa TPS
        $aco = new AcoService();
        $result = $aco->optimize($kantorCoord, $tpaCoord, $tpsList, $startNodeInfo);

        // 5. Update urutan di database
        $currentUrutan = $maxCompletedUrutan + 1;

        // Jika ada startTujuan, posisikan dia pertama setelah yang selesai
        if ($startTujuan) {
            // Cari jarak dari starting node sebelumnya
            $jarakKeStart = 0;
            if ($startNodeInfo) {
                $driverPosition = null;
                $lastSelesai = $completedTujuan->where('status', 'selesai')->last();
                if ($lastSelesai && $lastSelesai->tps) {
                    $driverPosition = [
                        'tipe' => 'tps',
                        'id' => $lastSelesai->tps->id,
                        'coords' => [$lastSelesai->tps->latitude, $lastSelesai->tps->longitude]
                    ];
                } else {
                    $driverPosition = [
                        'tipe' => 'kantor',
                        'id' => null,
                        'coords' => $kantorCoord
                    ];
                }
                
                $jarakKeStart = $this->getJarakDirect($driverPosition, [
                    'tipe' => 'tps',
                    'id' => $startTujuan->tps->id,
                    'coords' => [$startTujuan->tps->latitude, $startTujuan->tps->longitude]
                ]);
            }

            $startTujuan->update([
                'urutan' => $currentUrutan,
                'status' => 'aktif',
                'jarak_dari_sebelumnya_meter' => $jarakKeStart,
            ]);
            $currentUrutan++;
        }

        // Simpan urutan TPS hasil ACO
        $remainingRecordsByTpsId = $remainingTpsRecords->keyBy('tps_id');
        $lastTpsRecord = null;
        
        foreach ($result['urutan'] as $idx => $tps) {
            $tRecord = $remainingRecordsByTpsId->get($tps->id);
            if ($tRecord) {
                $detailIdx = $idx;
                $jarak = $result['detail_jarak'][$detailIdx]['jarak'] ?? 0;
                
                // Set the first one to 'aktif'
                $status = ($idx === 0 && !$startTujuan) ? 'aktif' : 'menunggu';
                
                $tRecord->update([
                    'urutan' => $currentUrutan,
                    'status' => $status,
                    'jarak_dari_sebelumnya_meter' => $jarak,
                ]);
                $currentUrutan++;
                $lastTpsRecord = $tRecord;
            }
        }

        // Jika ada skippedTujuan, tempatkan dia di paling akhir sebelum TPA
        if ($skippedTujuan) {
            // Tentukan jarak dari TPS terakhir hasil ACO ke skippedTujuan
            $jarakKeSkipped = 0;
            
            // Kita cari node sebelumnya
            $nodePrev = null;
            if ($lastTpsRecord && $lastTpsRecord->tps) {
                $nodePrev = [
                    'tipe' => 'tps',
                    'id' => $lastTpsRecord->tps->id,
                    'coords' => [$lastTpsRecord->tps->latitude, $lastTpsRecord->tps->longitude]
                ];
            } elseif ($startTujuan && $startTujuan->tps) {
                $nodePrev = [
                    'tipe' => 'tps',
                    'id' => $startTujuan->tps->id,
                    'coords' => [$startTujuan->tps->latitude, $startTujuan->tps->longitude]
                ];
            } else {
                $lastSelesai = $completedTujuan->where('status', 'selesai')->last();
                if ($lastSelesai && $lastSelesai->tps) {
                    $nodePrev = [
                        'tipe' => 'tps',
                        'id' => $lastSelesai->tps->id,
                        'coords' => [$lastSelesai->tps->latitude, $lastSelesai->tps->longitude]
                    ];
                } else {
                    $nodePrev = [
                        'tipe' => 'kantor',
                        'id' => null,
                        'coords' => $kantorCoord
                    ];
                }
            }

            if ($nodePrev && $skippedTujuan->tps) {
                $jarakKeSkipped = $this->getJarakDirect($nodePrev, [
                    'tipe' => 'tps',
                    'id' => $skippedTujuan->tps->id,
                    'coords' => [$skippedTujuan->tps->latitude, $skippedTujuan->tps->longitude]
                ]);
            }

            $skippedStatus = ($currentUrutan === ($maxCompletedUrutan + 1) && !$startTujuan) ? 'aktif' : 'menunggu';

            $skippedTujuan->update([
                'urutan' => $currentUrutan,
                'status' => $skippedStatus,
                'jarak_dari_sebelumnya_meter' => $jarakKeSkipped,
            ]);
            
            $lastTpsRecord = $skippedTujuan;
            $currentUrutan++;
        }

        // TPA selalu di akhir
        $tpaRecord = $ruteHarian->ruteTujuan()->where('tipe', 'tpa')->first();
        if ($tpaRecord) {
            $jarakTerakhir = 0;
            
            $nodePrev = null;
            if ($lastTpsRecord && $lastTpsRecord->tps) {
                $nodePrev = [
                    'tipe' => 'tps',
                    'id' => $lastTpsRecord->tps->id,
                    'coords' => [$lastTpsRecord->tps->latitude, $lastTpsRecord->tps->longitude]
                ];
            } elseif ($startTujuan && $startTujuan->tps) {
                $nodePrev = [
                    'tipe' => 'tps',
                    'id' => $startTujuan->tps->id,
                    'coords' => [$startTujuan->tps->latitude, $startTujuan->tps->longitude]
                ];
            } else {
                $lastSelesai = $completedTujuan->where('status', 'selesai')->last();
                if ($lastSelesai && $lastSelesai->tps) {
                    $nodePrev = [
                        'tipe' => 'tps',
                        'id' => $lastSelesai->tps->id,
                        'coords' => [$lastSelesai->tps->latitude, $lastSelesai->tps->longitude]
                    ];
                } else {
                    $nodePrev = [
                        'tipe' => 'kantor',
                        'id' => null,
                        'coords' => $kantorCoord
                    ];
                }
            }

            if ($nodePrev) {
                $jarakTerakhir = $this->getJarakDirect($nodePrev, [
                    'tipe' => 'tpa',
                    'id' => null,
                    'coords' => $tpaCoord
                ]);
            }

            $tpaStatus = ($currentUrutan === ($maxCompletedUrutan + 1) && !$startTujuan) ? 'aktif' : 'menunggu';
            
            $tpaRecord->update([
                'urutan' => $currentUrutan,
                'status' => $tpaStatus,
                'jarak_dari_sebelumnya_meter' => $jarakTerakhir,
            ]);
        }

        // Update total jarak rute harian
        $totalJarakAll = $ruteHarian->ruteTujuan()->sum('jarak_dari_sebelumnya_meter');
        $ruteHarian->update([
            'total_jarak_meter' => $totalJarakAll,
        ]);
    }



    /**
     * Activate the next destination in sequence.
     */
    private function activateNext(RuteHarian $ruteHarian, int $currentUrutan): void
    {
        $next = $ruteHarian->ruteTujuan()
            ->where('urutan', '>', $currentUrutan)
            ->where('status', 'menunggu')
            ->orderBy('urutan', 'asc')
            ->first();

        if ($next) {
            $next->update(['status' => 'aktif']);
        } else {
            // Cek apakah semua tujuan sudah selesai / dilewati
            $remaining = $ruteHarian->ruteTujuan()
                ->whereIn('status', ['menunggu', 'aktif'])
                ->count();

            if ($remaining === 0) {
                // Auto-selesaikan rute
                $totalJarak = $ruteHarian->ruteTujuan()
                    ->where('status', 'selesai')
                    ->sum('jarak_dari_sebelumnya_meter');

                $ruteHarian->update([
                    'status' => 'selesai',
                    'waktu_selesai' => Carbon::now(),
                    'total_jarak_meter' => $totalJarak,
                    'total_durasi_detik' => $ruteHarian->waktu_mulai
                        ? (int) abs(Carbon::now()->diffInSeconds($ruteHarian->waktu_mulai))
                        : 0,
                ]);

                RuteLog::create([
                    'rute_harian_id' => $ruteHarian->id,
                    'aksi' => 'selesai',
                ]);
            }
        }
    }

    /**
     * Get direct distance between two nodes using AcoService.
     */
    private function getJarakDirect(array $nodeFrom, array $nodeTo): int
    {
        $aco = new AcoService();
        return $aco->getJarakDirect($nodeFrom, $nodeTo);
    }
}
