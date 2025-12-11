<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfileController;


// ---------------------------------------------------------------------
// RUTE GUEST (Tidak memerlukan login: Login & Register)
// ---------------------------------------------------------------------
Route::middleware(['guest:karyawan'])->group(function () {

    // --- Login Karyawan ---
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/proseslogin', [AuthController::class, 'proseslogin'])->name('proseslogin');

    // --- REGISTER Karyawan ---
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});


// ---------------------------------------------------------------------
// RUTE KHUSUS KARYAWAN (Harus Login dengan guard "karyawan")
// ---------------------------------------------------------------------
Route::middleware(['auth:karyawan'])->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Logout ---
    Route::get('/proseslogout', [AuthController::class, 'proseslogout'])->name('proseslogout');

    // --- Presensi ---
    Route::prefix('presensi')->name('presensi.')->group(function () {

        // Presensi Harian
        Route::get('/create', [PresensiController::class, 'create'])->name('create');
        Route::post('/store', [PresensiController::class, 'store'])->name('store');

        // Histori Presensi
        Route::get('/histori', [PresensiController::class, 'histori'])->name('histori');

        // --- FITUR IZIN (Izin, Sakit, Cuti) ---
        Route::get('/izin', [PresensiController::class, 'izin'])->name('izin');       // halaman histori izin
        Route::get('/izin/buat', [PresensiController::class, 'buatizin'])->name('buatizin');  // form buat izin
        Route::post('/izin/store', [PresensiController::class, 'storeizin'])->name('storeizin'); // simpan izin
    });

    // --- Profile (edit & update) ---
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});


// ---------------------------------------------------------------------
// RUTE KHUSUS ADMIN (Guard web)
// ---------------------------------------------------------------------
Route::middleware(['auth:web'])->group(function () {

    // --- Dashboard Admin ---
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // --- Profile Admin ---
    Route::get('/admin/profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/admin/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');

    // --- Logout Admin ---
    Route::get('/admin/logout', [AuthController::class, 'proseslogout'])->name('admin.logout');
});
