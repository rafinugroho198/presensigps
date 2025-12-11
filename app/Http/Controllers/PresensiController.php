<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    // === Konstanta Lokasi dan Radius (Tetap) ===
    private const LATITUDE_KANTOR = -6.523987146602277;
    private const LONGITUDE_KANTOR = 106.71530996185996;
    private const MAX_RADIUS_METER = 50000; 

    public function create()
    {
        $hariini = date("Y-m-d");
        $nip = Auth::guard('karyawan')->user()->nip; 
        
        $cek = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('nip', $nip)
            ->count();

        // Mengirim data 'cek' (jumlah presensi hari ini) ke view
        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        // ... (Fungsi store tetap sama) ...
        $nip = Auth::guard('karyawan')->user()->nip;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");

        // === Validasi lokasi user ===
        $lokasi = $request->lokasi;
        if (!$lokasi) {
            return response("error|Lokasi tidak terdeteksi. Aktifkan GPS Anda.|gps");
        }

        [$latitudeuser, $longitudeuser] = array_pad(explode(",", $lokasi), 2, null);

        if (!$latitudeuser || !$longitudeuser) {
            return response("error|Koordinat tidak valid.|gps");
        }

        // === Hitung jarak user ke kantor ===
        $jarak = $this->distance(self::LATITUDE_KANTOR, self::LONGITUDE_KANTOR, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        // === Cek status absen hari ini ===
        $cek = DB::table('presensi')
            ->where('tgl_presensi', $tgl_presensi)
            ->where('nip', $nip)
            ->count();

        $ket = $cek > 0 ? 'out' : 'in';

        // === Ambil foto dari request (Base64 String) ===
        $image = $request->image;
        if (!$image) {
            return response("error|Gagal mengambil foto. Pastikan kamera aktif.|foto");
        }
    
        // Cek radius
        if ($radius > self::MAX_RADIUS_METER) { 
            return response("error|Anda berada di luar radius kantor. Jarak: {$radius} meter.|radius");
        }
        
        // =========================================================
        // === PROSES & SIMPAN FOTO ===
        // =========================================================

        // Hapus prefix data URI dan Decode Base64
        if (str_contains($image, 'base64,')) {
            $image = explode('base64,', $image)[1];
        }
        $image_base64 = base64_decode($image);

        // Siapkan path file & folder
        $folder = "uploads/absensi";
        $fileName = "{$nip}-{$tgl_presensi}-{$ket}.png"; 

        // 1. Simpan foto ke Storage
        try {
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
            }
            Storage::disk('public')->put("{$folder}/{$fileName}", $image_base64);
        } catch (\Exception $e) {
            return response("error|Gagal menyimpan foto ke server. Cek Izin Folder Storage. | storage");
        }

        // 2. Simpan data ke database 
        if ($cek > 0) {
            // ABSEN PULANG (Update data)
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
                return response("success|Terimakasih dan hati-hati di jalan :)|out");
            } 
            
            // Gagal Update DB: Hapus foto
            Storage::disk('public')->delete("{$folder}/{$fileName}"); 
            return response("error|Gagal menyimpan data absen pulang. Foto telah dihapus.|out");
            
        } else {
            // ABSEN MASUK (Insert data baru)
            $data_masuk = [
                'nip' => $nip,
                'tgl_presensi' => $tgl_presensi,
                'jam_in' => $jam,
                'foto_in' => $fileName,
                'lokasi_in' => $lokasi
            ];

            $simpan = DB::table('presensi')->insert($data_masuk);

            if ($simpan) {
                return response("success|Terimakasih dan selamat bekerja!|in");
            } 
            
            // Gagal Insert DB: Hapus foto
            Storage::disk('public')->delete("{$folder}/{$fileName}");
            return response("error|Gagal menyimpan data absen masuk. Foto telah dihapus.|in");
        }
    }

    // =========================================================
    // === FUNGSI IZIN / ABSENSI NON-KEHADIRAN (BARU) ===
    // =========================================================
    
    /**
     * 🔹 Menampilkan daftar pengajuan Izin/Sakit/Cuti.
     * @return \Illuminate\View\View
     */
    public function izin()
    {
        $nip = Auth::guard('karyawan')->user()->nip; 
        
        // Ambil data pengajuan izin untuk user yang sedang login
        $izin = DB::table('pengajuan_izin')
            ->where('nip', $nip)
            ->orderBy('tgl_izin_dari', 'desc')
            ->get();
            
        return view('presensi.izin', compact('izin')); 
    }


    /**
     * 🔹 Menampilkan form untuk pengajuan Izin/Cuti/Sakit.
     * @return \Illuminate\View\View
     */
    public function buatizin()
    {
        return view('presensi.buatizin'); 
    }

    /**
     * 🔹 Memproses dan menyimpan pengajuan Izin ke tabel 'pengajuan_izin'.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeizin(Request $request)
    {
        $nip = Auth::guard('karyawan')->user()->nip;
        
        // 1. Validasi Input (FIXED: tgl_izin_sampai nullable, max:1000, dan validasi file)
        $request->validate([
            'tgl_izin_dari' => 'required|date',
            'tgl_izin_sampai' => 'nullable|date|after_or_equal:tgl_izin_dari',
            'status' => 'required|in:i,s,c', // 'i': Izin, 's': Sakit, 'c': Cuti
            'keterangan' => 'required|string|max:1000',
            'file_pendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' // Validasi file
        ]);

        $tgl_izin_dari = $request->tgl_izin_dari;
        $tgl_izin_sampai = $request->tgl_izin_sampai;
        $status = $request->status;
        $keterangan = $request->keterangan;
        
        // LOGIKA PENANGANAN TANGGAL 1 HARI (jika tgl_izin_sampai kosong)
        if (empty($tgl_izin_sampai)) {
            $tgl_izin_sampai = $tgl_izin_dari;
        }

        // PENANGANAN FILE UPLOAD (Surat Dokter, dll)
        $nama_file = null;
        if ($request->hasFile('file_pendukung')) {
            $file = $request->file('file_pendukung');
            // Buat nama unik: nip-status-tanggal.ekstensi
            $nama_file_unik = $nip . '-' . $status . '-' . date('Ymd', strtotime($tgl_izin_dari)) . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke storage 'public/uploads/izin'
            $path = 'public/uploads/izin';
            try {
                // Gunakan putFileAs untuk menyimpan file
                Storage::putFileAs($path, $file, $nama_file_unik);
                $nama_file = $nama_file_unik;
            } catch (\Exception $e) {
                // Jika gagal simpan file, kasih error
                return redirect()->back()->with('error', 'Gagal menyimpan file pendukung. Error: ' . $e->getMessage());
            }
        }
        
        $data = [
            'nip' => $nip,
            'tgl_izin_dari' => $tgl_izin_dari,
            'tgl_izin_sampai' => $tgl_izin_sampai,
            'status' => $status,
            'keterangan' => $keterangan,
            'status_approved' => 0, // 0: Menunggu Persetujuan (default)
            'file_pendukung' => $nama_file, // Simpan nama file
            'created_at' => now(), // Tambahkan timestamp
            'updated_at' => now()  // Tambahkan timestamp
        ];


        // 2. Simpan Data ke Database
        try {
            DB::table('pengajuan_izin')->insert($data);
            
            // Redirect ke halaman daftar izin (presensi.izin)
            return redirect()->route('presensi.izin')->with('success', 'Pengajuan Izin berhasil dikirim dan menunggu persetujuan.');

        } catch (\Exception $e) {
            // Jika gagal simpan DB, hapus file yang sudah terupload (jika ada) dan kasih error
            if ($nama_file && Storage::disk('public')->exists("uploads/izin/{$nama_file}")) {
                Storage::disk('public')->delete("uploads/izin/{$nama_file}");
            }
            return redirect()->back()->with('error', 'Gagal menyimpan pengajuan izin. Pastikan tabel `pengajuan_izin` Anda memiliki semua kolom yang diperlukan, termasuk `file_pendukung`. Error: ' . $e->getMessage())->withInput();
        }
    }


    // =========================================================
    // === METODE BARU: HISTORI PRESENSI (FIXED FOR PGSQL) ===
    // =========================================================

    /**
     * 🔹 Menampilkan dan memfilter Histori Presensi
     */
    public function histori(Request $request)
    {
        $nip = Auth::guard('karyawan')->user()->nip; 
        
        // Ambil input filter (bulan dan tahun)
        $bulan = $request->input('bulan', date('m')); 
        $tahun = $request->input('tahun', date('Y')); 

        // Query data presensi
        $histori = DB::table('presensi')
            ->where('nip', $nip)
            // 💡 PERBAIKAN UNTUK POSTGRESQL: Gunakan fungsi EXTRACT(MONTH/YEAR FROM kolom_tanggal)
            ->whereRaw('EXTRACT(MONTH FROM tgl_presensi) = ?', [$bulan]) 
            ->whereRaw('EXTRACT(YEAR FROM tgl_presensi) = ?', [$tahun])
            ->orderBy('tgl_presensi', 'desc')
            ->get();
        
        // Data untuk dropdown bulan
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        
        // Mengirim data histori, dropdown, dan filter ke view
        return view('presensi.histori', compact('histori', 'namabulan', 'bulan', 'tahun'));
    }


    // === Fungsi menghitung jarak antar koordinat (Haversine Formula) ===
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
