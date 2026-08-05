<?php

namespace App\Http\Controllers\Sopir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tps;
use App\Models\Riwayat;
use Illuminate\Support\Facades\Auth;

class TpsController extends Controller
{
    public function index()
    {
        $tps = Tps::join('users', 'users.id', '=', 'tps.user_id')
            ->orderBy('tps.id', 'desc')
            ->select('tps.*', 'users.name as namauser')
            ->get();

        return view('sopir.tps.index', [
            'title' => 'TPS',
            'active' => 'Index',
            'tps' => $tps,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return view('sopir.tps.create', [
            'title' => 'TPS',
            'active' => 'Index',
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'             => ['required', 'string', 'max:255'],
            'alamat'           => ['required', 'string'],
            'koordinat_marker' => ['required'],
        ]);

        $raw = $request->input('koordinat_marker');
        $coords = is_string($raw) ? json_decode($raw, true) : $raw;

        if (is_array($coords) && array_values($coords) === $coords) {
            $lat = $coords[0] ?? null;
            $lng = $coords[1] ?? null;
        } else {
            $lat = $coords['lat'] ?? $coords['latitude'] ?? null;
            $lng = $coords['lng'] ?? $coords['long'] ?? $coords['longitude'] ?? null;
        }

        if (!is_numeric($lat) || !is_numeric($lng)) {
            return back()
                ->withErrors(['koordinat_marker' => 'Format koordinat tidak valid. Gunakan [lat, lng].'])
                ->withInput();
        }

        Tps::create([
            'user_id'   => Auth::id(),
            'nama'      => $request->nama,
            'alamat'    => $request->alamat,
            'latitude'  => (float) $lat,
            'longitude' => (float) $lng,
        ]);

        return redirect()
            ->route('sopir.tps.index')
            ->with('success', 'TPS berhasil disimpan.');
    }

    public function edit($id)
    {
        $tps = Tps::findOrFail($id);

        return view('sopir.tps.edit', [
            'title' => 'TPS',
            'active' => 'Edit',
            'tps' => $tps,
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'             => ['required', 'string', 'max:255'],
            'alamat'           => ['required', 'string'],
            'koordinat_marker' => ['required'],
        ]);

        $tps = Tps::findOrFail($id);

        $raw = $request->input('koordinat_marker');
        $coords = is_string($raw) ? json_decode($raw, true) : $raw;

        if (is_array($coords) && array_values($coords) === $coords) {
            $lat = $coords[0] ?? null;
            $lng = $coords[1] ?? null;
        } else {
            $lat = $coords['lat'] ?? $coords['latitude'] ?? null;
            $lng = $coords['lng'] ?? $coords['long'] ?? $coords['longitude'] ?? null;
        }

        if (!is_numeric($lat) || !is_numeric($lng)) {
            return back()
                ->withErrors(['koordinat_marker' => 'Format koordinat tidak valid. Gunakan [lat, lng].'])
                ->withInput();
        }

        $tps->update([
            'nama'      => $request->nama,
            'alamat'    => $request->alamat,
            'latitude'  => (float) $lat,
            'longitude' => (float) $lng,
        ]);

        return redirect()
            ->route('sopir.tps.index')
            ->with('success', 'Data TPS berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tps = Tps::findOrFail($id);
        $tps->delete();

        return redirect()
            ->route('sopir.tps.index')
            ->with('success', 'Data TPS berhasil dihapus.');
    }
}
