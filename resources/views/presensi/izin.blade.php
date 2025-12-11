@extends('layouts.presensi')

@section('header')
<div class="fixed top-0 left-0 right-0 p-4 bg-gray-800 z-20 shadow-xl border-b border-gray-700">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        <ion-icon name="arrow-back-outline" class="text-3xl text-cyan-400"></ion-icon>
        <h2 class="text-xl font-bold text-cyan-400">Riwayat Izin/Cuti</h2>
    </a>
</div>
@endsection

@section('content')
{{-- Load Tailwind CSS hanya jika tidak dimuat di layout utama --}}
@if (env('APP_ENV') === 'local')
    <script src="https://cdn.tailwindcss.com"></script>
@endif
<style>
    /* Custom Scrollbar for better dark mode aesthetics */
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #161b22;
    }
    ::-webkit-scrollbar-thumb {
        background: #00ffff; /* Cyan Glow */
        border-radius: 3px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #00dddd;
    }
</style>

<div class="min-h-screen pb-24 px-4 bg-gray-900 text-white z-10">

    <div class="pt-20"> {{-- Memberi ruang untuk header di atas --}}
        
        {{-- Pesan Success/Error dari Controller --}}
        @if (session('success'))
            <div class="bg-green-700/30 text-green-300 border border-green-600/50 p-3 rounded-xl mb-4 font-medium text-sm">
                <ion-icon name="checkmark-circle-outline" class="align-middle mr-1"></ion-icon>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-700/30 text-red-300 border border-red-600/50 p-3 rounded-xl mb-4 font-medium text-sm">
                <ion-icon name="close-circle-outline" class="align-middle mr-1"></ion-icon>
                {{ session('error') }}
            </div>
        @endif
        
        {{-- Tombol Pengajuan Izin --}}
        <div class="mb-5">
            <a href="{{ route('presensi.buatizin') }}" class="w-full flex items-center justify-center gap-2 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold py-3 rounded-xl shadow-xl shadow-cyan-600/60 active:scale-[0.98] transition-all">
                <ion-icon name="add-circle-outline" class="text-2xl"></ion-icon>
                Ajukan Izin Baru
            </a>
        </div>

        {{-- LIST DATA RIWAYAT IZIN --}}
        <div class="space-y-4">
            {{-- Variabel $izin dikirim dari PresensiController --}}
            
            @forelse ($izin as $d)
                @php
                    // Set Status Izin (i, s, c)
                    $statusIzin = '';
                    $statusTypeClass = '';
                    if ($d->status == 's') {
                        $statusIzin = 'Sakit';
                        $statusTypeClass = 'bg-red-700/30 text-red-300 border-red-600/50';
                    } elseif ($d->status == 'i') {
                        $statusIzin = 'Izin';
                        $statusTypeClass = 'bg-orange-700/30 text-orange-300 border-orange-600/50';
                    } elseif ($d->status == 'c') {
                        $statusIzin = 'Cuti';
                        $statusTypeClass = 'bg-cyan-700/30 text-cyan-300 border-cyan-600/50';
                    }
                    
                    // Set Status Approval (0, 1, 2)
                    if ($d->status_approved == 1) {
                        $statusApproval = 'Disetujui';
                        $approvalClass = 'bg-green-700/30 text-green-300 border-green-600/50';
                    } elseif ($d->status_approved == 2) {
                        $statusApproval = 'Ditolak';
                        $approvalClass = 'bg-red-700/30 text-red-300 border-red-600/50';
                    } else {
                        $statusApproval = 'Menunggu';
                        $approvalClass = 'bg-yellow-700/30 text-yellow-300 border-yellow-600/50';
                    }

                    // Menggunakan tgl_izin_dari dan tgl_izin_sampai (Perbaikan)
                    if (isset($d->tgl_izin_dari) && isset($d->tgl_izin_sampai)) {
                        if ($d->tgl_izin_dari == $d->tgl_izin_sampai) {
                            // Jika tanggalnya sama (hanya 1 hari)
                            $tanggalText = date("d F Y", strtotime($d->tgl_izin_dari));
                        } else {
                            // Jika rentang tanggal
                            $tanggalAwal = date("d F", strtotime($d->tgl_izin_dari));
                            $tanggalAkhir = date("d F Y", strtotime($d->tgl_izin_sampai));
                            $tanggalText = $tanggalAwal . " - " . $tanggalAkhir;
                        }
                    } else {
                        $tanggalText = 'Tanggal tidak tersedia';
                    }

                @endphp
                
                {{-- Card Histori Izin Individual --}}
                <div class="bg-gray-800 rounded-xl p-4 shadow-2xl shadow-black/30 border border-gray-700/50 hover:border-cyan-500/50 transition duration-300">
                    
                    <div class="flex justify-between items-start mb-3 pb-3 border-b border-gray-700/50">
                        <div class="flex flex-col">
                            <b class="text-lg font-bold text-white">{{ $tanggalText }}</b>
                            
                            {{-- Jenis Izin --}}
                            <span class="text-xs font-medium py-0.5 px-2 mt-2 rounded-full inline-block w-fit border {{ $statusTypeClass }}">
                                <ion-icon name="shield-half-outline" class="mr-1 align-middle"></ion-icon>
                                {{ $statusIzin }}
                            </span>
                        </div>
                        
                        {{-- Status Approval --}}
                        <div class="text-right">
                            <span class="text-sm font-semibold text-gray-400 block mb-1">Status Approval:</span>
                            <span class="text-xs font-medium py-1 px-3 rounded-full inline-block border {{ $approvalClass }}">
                                <ion-icon name="{{ $d->status_approved == 1 ? 'checkmark-circle-outline' : ($d->status_approved == 2 ? 'close-circle-outline' : 'time-outline') }}" class="mr-1 align-middle"></ion-icon>
                                {{ $statusApproval }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- Detail Keterangan --}}
                    <div>
                        <small class="text-gray-400 block mb-1 font-semibold text-xs uppercase">Alasan Keterangan</small>
                        <p class="text-sm text-gray-300 italic">{{ $d->keterangan }}</p>
                    </div>

                </div>
            @empty
                <div class="p-6 text-center bg-cyan-900/30 border border-cyan-500/40 text-cyan-200 rounded-xl font-medium mt-4">
                    <ion-icon name="alert-circle-outline" class="text-2xl align-middle mr-1"></ion-icon>
                    Anda belum mengajukan Izin/Cuti. Silakan ajukan izin baru.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
