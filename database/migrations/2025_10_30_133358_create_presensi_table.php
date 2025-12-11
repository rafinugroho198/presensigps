<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $nip = Auth::guard('karyawan')->user()->nip;
        $cek = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('nip', $nip)
            ->count();

        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $nip = Auth::guard('karyawan')->user()->nip;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");

        // === Titik lokasi kantor ===
        $latitudekantor = -6.523987146602277;
        $longitudekantor = 106.71530996185996;

        // === Validasi lokasi user ===
        $lokasi = $request->lokasi;
        if (!$lokasi) {
            return response("error|Lokasi tidak terdeteksi. Aktifkan GPS Anda.|gps");
        }

        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0] ?? null;
        $longitudeuser = $lokasiuser[1] ?? null;

        if (!$latitudeuser || !$longitudeuser) {
            return response("error|Koordinat tidak valid.|gps");
        }

        // === Hitung jarak user ke kantor ===
        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        // === Cek apakah sudah absen hari ini ===
        $cek = DB::table('presensi')
            ->where('tgl_presensi', $tgl_presensi)
            ->where('nip', $nip)
            ->count();

        $ket = $cek > 0 ? 'out' : 'in';

        // === Ambil foto dari request ===
        $image = $request->image;
        if (!$image) {
            return response("error|Gagal mengambil foto. Pastikan kamera aktif.|foto");
        }

        // === Hapus prefix base64 jika ada ===
        if (str_contains($image, 'base64,')) {
            $image = explode('base64,', $image)[1];
        }

        // === Siapkan nama file & folder ===
        $folder = "uploads/absensi";
        $fileName = "{$nip}-{$tgl_presensi}-{$ket}.jpg";

        // Pastikan folder ada
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        // Decode base64 menjadi binary
        $image_base64 = base64_decode($image);

        // === Validasi radius (maks 100 meter) ===
        if ($radius > 100) {
            return response("error|Anda berada di luar radius kantor. Jarak: {$radius} meter.|radius");
        }

        // === Simpan ke database dan storage ===
        if ($cek > 0) {
            // Absen pulang
            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $fileName,
                'lokasi_out' => $lokasi
            ];

            $update = DB::table('presensi')
                ->where('tgl_presensi', $tgl_presensi)
                ->where('nip', $nip)
                ->update($data_pulang);

            if ($update) {
                Storage::disk('public')->put("{$folder}/{$fileName}", $image_base64);
                return response("success|Terimakasih dan hati-hati di jalan :)|out");
            } else {
                return response("error|Gagal menyimpan data absen pulang.|out");
            }
        } else {
            // Absen masuk
            $data_masuk = [
                'nip' => $nip,
                'tgl_presensi' => $tgl_presensi,
                'jam_in' => $jam,
                'foto_in' => $fileName,
                'lokasi_in' => $lokasi
            ];

            $simpan = DB::table('presensi')->insert($data_masuk);

            if ($simpan) {
                Storage::disk('public')->put("{$folder}/{$fileName}", $image_base64);
                return response("success|Terimakasih dan selamat bekerja!|in");
            } else {
                return response("error|Gagal menyimpan data absen masuk.|in");
            }
        }
    }

    // === Fungsi menghitung jarak antar koordinat ===
    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2)))
            + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
}
