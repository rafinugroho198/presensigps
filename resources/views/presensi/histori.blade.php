@extends('layouts.presensi')

{{-- Struktur view di sini akan mengambil layout dari layouts.presensi --}}

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
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

    /* Input focus glow */
    .input-glow:focus {
        box-shadow: 0 0 0 2px rgba(0, 255, 255, 0.5);
    }
</style>

@section('header')
{{-- HEADER FIXED DAN TOMBOL KEMBALI --}}
<div class="fixed top-0 left-0 right-0 p-4 bg-gray-800 z-20 shadow-xl border-b border-gray-700">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        <ion-icon name="arrow-back-outline" class="text-3xl text-cyan-400"></ion-icon>
        <h2 class="text-xl font-bold text-cyan-400">Histori Absensi</h2>
    </a>
</div>
@endsection

@section('content')
{{-- Kontainer utama dijamin gelap dan teksnya putih. Menggunakan pt-20 untuk memberi ruang bagi fixed header. --}}
<div class="min-h-screen pb-24 px-4 bg-gray-900 text-white z-10">

    <div class="pt-20"> {{-- Memberi ruang untuk header di atas --}}
        
        {{-- FORM FILTER BULAN DAN TAHUN --}}
        <div class="mb-5 mt-2 p-4 bg-gray-800 rounded-xl shadow-lg border border-cyan-500/50">
            {{-- Sesuaikan action ke route yang benar --}}
            <form method="GET" action="{{ route('presensi.histori') }}" class="flex gap-3"> 
                
                {{-- Dropdown Bulan --}}
                <select name="bulan" id="bulan" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white text-sm focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition input-glow">
                    <option value="">Pilih Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ (isset($bulan) && $bulan == $i) ? 'selected' : '' }}>
                            {{ isset($namabulan) && isset($namabulan[$i]) ? $namabulan[$i] : "Bulan $i" }}
                        </option>
                    @endfor
                </select>
                
                {{-- Dropdown Tahun --}}
                <select name="tahun" id="tahun" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white text-sm focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition input-glow">
                    <option value="">Pilih Tahun</option>
                    @php
                        $startYear = 2022;
                        $currentYear = date('Y');
                    @endphp
                    @for ($i = $currentYear; $i >= $startYear; $i--)
                        <option value="{{ $i }}" {{ (isset($tahun) && $tahun == $i) ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                
                {{-- Tombol Search dengan efek glow cyan --}}
                <button type="submit" class="flex-shrink-0 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold px-4 py-3 rounded-lg shadow-md shadow-cyan-600/60 active:scale-[0.98] transition-all">
                    <ion-icon name="search-outline" class="text-xl"></ion-icon>
                </button>
            </form>
        </div>

        {{-- LIST DATA HISTORI --}}
        <div class="space-y-4">
            {{-- Pastikan variabel $histori, $namabulan, $bulan, dan $tahun dilempar dari controller --}}
            
            @forelse ($histori as $d)
                @php
                    $isPulang = !empty($d->jam_out);
                    
                    // Warna untuk jam masuk/pulang
                    $inColor = 'text-green-400';
                    $outColor = $isPulang ? 'text-red-500' : 'text-gray-500';

                    $statusText = $isPulang ? 'Absen Selesai' : 'Belum Pulang';
                    $badgeClass = $isPulang 
                        ? 'bg-green-700/30 text-green-300 border-green-600/50' 
                        : 'bg-orange-700/30 text-orange-300 border-orange-600/50';
                    $statusIcon = $isPulang ? 'checkmark-circle-outline' : 'time-outline';

                    // Memecah Tanggal untuk tampilan menonjol
                    $tanggal = date("d", strtotime($d->tgl_presensi));
                    $hari = date("l", strtotime($d->tgl_presensi));
                    $bulan_tahun = date("F Y", strtotime($d->tgl_presensi));
                @endphp
                
                {{-- Card Histori Individual --}}
                <div class="bg-gray-800 rounded-xl p-4 shadow-2xl shadow-black/30 border border-gray-700/50 hover:border-cyan-500/50 transition duration-300">
                    
                    {{-- Judul Tanggal dan Badge Status (NEW STRUCTURE) --}}
                    <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-700/50">
                        <div class="flex items-center gap-3">
                            {{-- Tanggal sebagai elemen besar --}}
                            <div class="flex flex-col items-center justify-center bg-gray-900 rounded-lg p-2 w-14 h-14 border border-cyan-600/50 shadow-lg shadow-cyan-900/30">
                                <b class="text-2xl font-extrabold text-cyan-400 leading-none">{{ $tanggal }}</b>
                                <span class="text-xs text-gray-400 leading-none">{{ substr($hari, 0, 3) }}</span>
                            </div>
                            
                            {{-- Hari, Bulan & Tahun, dan Status --}}
                            <div class="flex flex-col">
                                <b class="text-lg font-bold text-white tracking-wider">{{ $hari }}</b>
                                <span class="text-sm text-gray-300">{{ $bulan_tahun }}</span>
                                <span class="text-xs font-medium py-0.5 px-2 mt-2 rounded-full inline-block w-fit border {{ $badgeClass }}">
                                    <ion-icon name="{{ $statusIcon }}" class="mr-1 align-middle"></ion-icon>
                                    {{ $statusText }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- Tombol Lihat Bukti Foto --}}
                        <a href="#" class="flex-shrink-0 text-cyan-400 hover:text-cyan-300 transition text-sm font-semibold p-2 rounded-full bg-gray-900/50" 
                            onclick="alert('Tampilkan Foto Absensi - Perlu implementasi Modal atau lightbox.')">
                            <ion-icon name="camera-outline" class="align-middle text-2xl"></ion-icon>
                        </a>
                    </div>
                    
                    {{-- Detail Jam Masuk dan Pulang (Mengikuti konsep 2 card terpisah) --}}
                    <div class="grid grid-cols-2 gap-4">
                        
                        {{-- Card Check-In --}}
                        <div class="bg-gray-900 rounded-lg p-3 text-center border border-gray-700/50 shadow-inner shadow-cyan-900/10">
                            <small class="text-gray-400 block mb-1 font-semibold text-xs uppercase">Check-In</small>
                            <p class="text-4xl font-extrabold tracking-widest {{ $inColor }} leading-none">{{ $d->jam_in }}</p>
                            <small class="text-gray-500 text-xs mt-2 block italic truncate px-1">
                                <ion-icon name="location-outline" class="align-text-top mr-0.5"></ion-icon>
                                {{ substr($d->lokasi_in, 0, 15) }}...
                            </small>
                        </div>

                        {{-- Card Check-Out --}}
                        <div class="bg-gray-900 rounded-lg p-3 text-center border border-gray-700/50 shadow-inner shadow-red-900/10">
                            <small class="text-gray-400 block mb-1 font-semibold text-xs uppercase">Check-Out</small>
                            <p class="text-4xl font-extrabold tracking-widest {{ $outColor }} leading-none">
                                {{ $d->jam_out ? $d->jam_out : '00:00:00' }}
                            </p>
                            <small class="text-gray-500 text-xs mt-2 block italic truncate px-1">
                                @if($d->lokasi_out)
                                    <ion-icon name="location-outline" class="align-text-top mr-0.5"></ion-icon>
                                    {{ substr($d->lokasi_out, 0, 15) }}...
                                @else
                                    Belum Absen Pulang
                                @endif
                            </small>
                        </div>
                    </div>

                </div>
            @empty
                <div class="p-6 text-center bg-cyan-900/30 border border-cyan-500/40 text-cyan-200 rounded-xl font-medium mt-4">
                    <ion-icon name="alert-circle-outline" class="text-2xl align-middle mr-1"></ion-icon>
                    Tidak ada data presensi untuk bulan {{ $namabulan[$bulan] }} tahun {{ $tahun }}.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
