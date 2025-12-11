<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * 🔹 Menampilkan halaman edit profil
     */
    public function edit()
    {
        $user = Auth::guard('karyawan')->user() ?? Auth::guard('web')->user();

        if (!$user) {
            return redirect('/')->withErrors('Anda harus login terlebih dahulu.');
        }

        return view('presensi.editprofile', compact('user'));
    }

    /**
     * 🔹 Proses update profil
     */
    public function update(Request $request)
    {
        $user = Auth::guard('karyawan')->user() ?? Auth::guard('web')->user();

        if (!$user) {
            return redirect('/')->withErrors('Anda harus login terlebih dahulu.');
        }

        // Validasi input
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jabatan'      => 'nullable|string|max:255',
            'password'     => 'nullable|min:6',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update data dasar
        $user->nama_lengkap = $request->nama_lengkap;
        $user->jabatan = $request->jabatan ?? $user->jabatan;

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Pastikan folder penyimpanan ada
        Storage::makeDirectory('public/uploads/profile');

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists('public/uploads/profile/' . $user->foto)) {
                Storage::delete('public/uploads/profile/' . $user->foto);
            }

            // Simpan file baru
            $file->storeAs('public/uploads/profile', $filename);

            // Simpan nama file ke DB
            $user->foto = $filename;
        }

        $user->save();

        // ✅ Setelah update, arahkan ke dashboard karyawan biasa
        return redirect()->route('dashboard')->with('success', 'Profil berhasil diperbarui!');
    }
}
