<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function index()
    {
        // Jika user sudah login, arahkan ke dashboard.
        // Ini adalah fallback jika middleware gagal.
        if (Auth::guard('karyawan')->check()) {
            return redirect()->route('dashboard]');
        }
        
        // Tampilkan view login (auth.login)
        return view('auth.login');
    }

    // Memproses data login (POST request)
    public function proseslogin(Request $request)
    {
        // Validasi input NIP dan Password
        $credentials = $request->validate([
            'nip' => 'required',
            'password' => 'required',
        ]);

        // Coba melakukan autentikasi menggunakan guard 'karyawan'
        // Ingat, kita menggunakan 'nip' sebagai username
        if (Auth::guard('karyawan')->attempt(['nip' => $request->nip, 'password' => $request->password])) {
            // Regenerasi session untuk mencegah session fixation
            $request->session()->regenerate();

            // Jika berhasil, arahkan ke dashboard
            return redirect()->route('dashboard');
        }

        // Jika gagal, kembali ke halaman login dengan pesan error
        return redirect()->back()->withErrors([
            'loginError' => 'NIP atau Password salah!',
        ])->withInput();
    }

    // Memproses logout
    public function proseslogout(Request $request)
    {
        // Logout guard karyawan
        Auth::guard('karyawan')->logout();

        // Invalidasi session
        $request->session()->invalidate();
        
        // Regenerasi token CSRF
        $request->session()->regenerateToken();

        // Arahkan kembali ke halaman login
        return redirect()->route('login');
    }
}
