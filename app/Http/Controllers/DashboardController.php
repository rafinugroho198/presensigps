<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ==============================
        // 🗓️ Setup tanggal
        // ==============================
        $hariini   = date('Y-m-d');
        $bulanini  = date('m');
        $tahunini  = date('Y');

        // ==============================
        // 🔐 Validasi login user
        // ==============================
        if (!Auth::guard('karyawan')->check()) {
            return redirect('/login')->withErrors('Anda harus login terlebih dahulu.');
        }

        $nip = Auth::guard('karyawan')->user()->nip;

        // ==============================
        // 📋 Data Presensi Hari Ini
        // ==============================
        $presensihariini = DB::table('presensi')
            ->where('nip', $nip)
            ->where('tgl_presensi', $hariini)
            ->first();

        // ==============================
        // 📅 Histori Presensi Bulan Ini
        // ==============================
        $historibulanini = DB::table('presensi')
            ->where('nip', $nip)
            ->whereMonth('tgl_presensi', $bulanini)
            ->whereYear('tgl_presensi', $tahunini)
            ->orderBy('tgl_presensi')
            ->get();

        // ==============================
        // 🏆 Leaderboard Kehadiran
        // ==============================

        // 🚫 Data dummy yang ingin dikecualikan
        $excludedNames = ['Edward Lindgren', 'Amelia Khan'];

        // Hitung total hari kerja bulan ini (jumlah hari unik dengan presensi)
        $totalHariKerja = DB::table('presensi')
            ->whereMonth('tgl_presensi', $bulanini)
            ->whereYear('tgl_presensi', $tahunini)
            ->distinct()
            ->count('tgl_presensi');

        $denominator = max($totalHariKerja, 1); // hindari pembagian nol

        // ==============================
        // 🔍 Query Leaderboard
        // ==============================
        $leaderboardQuery = DB::table('karyawan')
            ->select('karyawan.nip', 'karyawan.nama_lengkap')
            ->whereNotIn('nama_lengkap', $excludedNames)
            ->whereIn('nip', function ($q) use ($bulanini, $tahunini) {
                $q->select('nip')
                    ->from('presensi')
                    ->whereMonth('tgl_presensi', $bulanini)
                    ->whereYear('tgl_presensi', $tahunini)
                    ->distinct();
            })
            ->selectRaw("
                (
                    SELECT COUNT(p2.tgl_presensi)
                    FROM presensi p2
                    WHERE p2.nip = karyawan.nip
                    AND EXTRACT(MONTH FROM p2.tgl_presensi) = ?
                    AND EXTRACT(YEAR FROM p2.tgl_presensi) = ?
                ) AS total_hadir
            ", [$bulanini, $tahunini])
            ->selectRaw("
                ROUND((
                    (
                        SELECT COUNT(p3.tgl_presensi)
                        FROM presensi p3
                        WHERE p3.nip = karyawan.nip
                        AND EXTRACT(MONTH FROM p3.tgl_presensi) = ?
                        AND EXTRACT(YEAR FROM p3.tgl_presensi) = ?
                    ) / {$denominator} * 100
                ), 0) AS attendance_percentage
            ", [$bulanini, $tahunini]);

        // Urutkan & ambil 10 besar
        $leaderboard = DB::query()
            ->fromSub($leaderboardQuery, 'lb')
            ->orderByDesc('attendance_percentage')
            ->limit(10)
            ->get();

        // ==============================
        // 📤 Kirim data ke view
        // ==============================
        return view('dashboard.dashboard', compact(
            'presensihariini',
            'historibulanini',
            'leaderboard'
        ));
    }
}
