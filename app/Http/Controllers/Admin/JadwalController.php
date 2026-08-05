<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\User;
use App\Models\Tps;
use App\Models\Angkutan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $hari = $request->get('hari');
        
        $query = Jadwal::with(['user', 'tps', 'angkutan']);
        
        if ($hari) {
            $query->where('hari', $hari);
        }

        $jadwal = $query->orderBy('id', 'desc')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('admin.jadwal.index', [
            'title' => 'Jadwal Pengangkutan',
            'active' => 'Index',
            'jadwal' => $jadwal,
            'hariList' => $hariList,
            'filterHari' => $hari,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Export jadwal to Excel.
     */
    public function export(Request $request, \App\Services\JadwalExcelService $service)
    {
        $hari = $request->get('hari');
        $spreadsheet = $service->export($hari);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = 'Jadwal_Pengangkutan_' . ($hari ?: 'Semua_Hari') . '_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($filename) . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Download template import jadwal.
     */
    public function downloadTemplate(\App\Services\JadwalExcelService $service)
    {
        $spreadsheet = $service->downloadTemplate();
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = 'Template_Import_Jadwal.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($filename) . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import jadwal from Excel.
     */
    public function import(Request $request, \App\Services\JadwalExcelService $service)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            $importedCount = $service->import($request->file('file'));
            return redirect()
                ->route('admin.jadwal.index')
                ->with('success', "Berhasil meng-import {$importedCount} data jadwal pengangkutan.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.jadwal.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sopir = User::where('role', 'sopir')->orderBy('name', 'asc')->get();
        $tps = Tps::orderBy('nama', 'asc')->get();
        $angkutan = Angkutan::orderBy('nama', 'asc')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('admin.jadwal.create', [
            'title' => 'Jadwal',
            'active' => 'Index',
            'sopir' => $sopir,
            'tps' => $tps,
            'angkutan' => $angkutan,
            'hariList' => $hariList,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tps_id' => ['required', 'exists:tps,id'],
            'angkutan_id' => ['required', 'exists:angkutan,id'],
            'hari' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
        ]);

        Jadwal::create([
            'user_id' => $request->user_id,
            'tps_id' => $request->tps_id,
            'angkutan_id' => $request->angkutan_id,
            'hari' => $request->hari,
        ]);

        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('admin.jadwal.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $sopir = User::where('role', 'sopir')->orderBy('name', 'asc')->get();
        $tps = Tps::orderBy('nama', 'asc')->get();
        $angkutan = Angkutan::orderBy('nama', 'asc')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('admin.jadwal.edit', [
            'title' => 'Jadwal',
            'active' => 'Edit',
            'jadwal' => $jadwal,
            'sopir' => $sopir,
            'tps' => $tps,
            'angkutan' => $angkutan,
            'hariList' => $hariList,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tps_id' => ['required', 'exists:tps,id'],
            'angkutan_id' => ['required', 'exists:angkutan,id'],
            'hari' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
        ]);

        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update([
            'user_id' => $request->user_id,
            'tps_id' => $request->tps_id,
            'angkutan_id' => $request->angkutan_id,
            'hari' => $request->hari,
        ]);

        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}
