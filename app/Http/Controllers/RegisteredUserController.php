<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Karyawan; // MODEL YANG BENAR UNTUK GUARD karyawan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman register
     */
    public function create(): View
    {
        return view('auth.register.page'); 
    }

    /**
     * Tangani proses register
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:50', 'unique:karyawan,nip'],
            'email' => ['required', 'email', 'max:255', 'unique:karyawan,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Buat akun baru
        $karyawan = Karyawan::create([
            'nama_lengkap' => $request->nama,
            'nip' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Login otomatis
        Auth::guard('karyawan')->login($karyawan);

        // 4. Redirect ke dashboard karyawan
        return redirect()->route('dashboard');
    }
}
