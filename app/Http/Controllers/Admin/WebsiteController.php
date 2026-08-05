<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Website;
use Illuminate\Support\Facades\Auth;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $website = Website::first();
        return view('admin.website.index', [
            'title' => 'Pengaturan Website',
            'active' => 'Index',
            'website' => $website,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $website = Website::findOrFail($id);

        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'alamat' => ['required', 'string'],
            'telepon' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'koordinat_tpa' => ['required', 'string'],
            'koordinat_kantor' => ['required', 'string'],
        ]);

        $data = [
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'koordinat_tpa' => $request->koordinat_tpa,
            'koordinat_kantor' => $request->koordinat_kantor,
        ];

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img'), $filename);

            // Hapus logo lama jika ada
            if ($website->logo && file_exists(public_path('img/' . $website->logo))) {
                @unlink(public_path('img/' . $website->logo));
            }

            $data['logo'] = $filename;
        }

        $website->update($data);

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Pengaturan website berhasil diperbarui.');
    }
}
