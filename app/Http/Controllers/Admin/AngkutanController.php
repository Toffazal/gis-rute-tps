<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Angkutan;
use Illuminate\Support\Facades\Auth;

class AngkutanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $angkutan = Angkutan::orderBy('id', 'desc')->get();
        return view('admin.angkutan.index', [
            'title' => 'Angkutan',
            'active' => 'Index',
            'angkutan' => $angkutan,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('admin.angkutan.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nomor' => ['required', 'string', 'max:100'],
        ]);

        Angkutan::create([
            'nama' => $request->nama,
            'nomor' => $request->nomor,
        ]);

        return redirect()
            ->route('admin.angkutan.index')
            ->with('success', 'Angkutan berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('admin.angkutan.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return redirect()->route('admin.angkutan.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nomor' => ['required', 'string', 'max:100'],
        ]);

        $angkutan = Angkutan::findOrFail($id);
        $angkutan->update([
            'nama' => $request->nama,
            'nomor' => $request->nomor,
        ]);

        return redirect()
            ->route('admin.angkutan.index')
            ->with('success', 'Angkutan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $angkutan = Angkutan::findOrFail($id);
        $angkutan->delete();

        return redirect()
            ->route('admin.angkutan.index')
            ->with('success', 'Angkutan berhasil dihapus.');
    }
}
