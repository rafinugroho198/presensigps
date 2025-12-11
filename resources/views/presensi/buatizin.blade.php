@extends('layouts.presensi')

@section('header')
<div class="fixed top-0 left-0 right-0 p-4 bg-gray-800 z-20 shadow-xl border-b border-gray-700">
    <a href="{{ route('presensi.izin') }}" class="flex items-center gap-3">
        <ion-icon name="arrow-back-outline" class="text-3xl text-cyan-400"></ion-icon>
        <h2 class="text-xl font-bold text-cyan-400">Ajukan Izin/Cuti</h2>
    </a>
</div>
@endsection

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .input-glow:focus {
        box-shadow: 0 0 0 2px rgba(0, 255, 255, 0.5);
    }
</style>

<div class="min-h-screen pt-20 pb-24 px-4 bg-gray-900 text-white z-10">
    <div class="mt-4 p-5 bg-gray-800 rounded-xl shadow-2xl border border-cyan-500/50">
        
        {{-- Tampilkan pesan error umum non-validasi dari Controller --}}
        @if (session('error'))
            <div class="bg-red-700/30 text-red-300 border border-red-600/50 p-3 rounded-xl mb-4 font-medium text-sm">
                <ion-icon name="close-circle-outline" class="align-middle mr-1"></ion-icon>
                {{ session('error') }}
            </div>
        @endif
        
        <form action="{{ route('presensi.storeizin') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 💡 PERBAIKAN: Input Tanggal Mulai Izin/Cuti --}}
            <div class="mb-4">
                <label for="tgl_izin_dari" class="block text-sm font-medium text-gray-400 mb-2">Mulai Tanggal Izin/Cuti</label>
                {{-- Nama input diubah dari tgl_izin menjadi tgl_izin_dari --}}
                <input type="date" id="tgl_izin_dari" name="tgl_izin_dari" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white text-sm focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition input-glow" value="{{ old('tgl_izin_dari') }}" required>
                {{-- Tampilkan error validasi jika ada --}}
                @error('tgl_izin_dari')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 💡 PERBAIKAN: Input Tanggal Sampai Izin/Cuti (Opsional) --}}
            <div class="mb-4">
                <label for="tgl_izin_sampai" class="block text-sm font-medium text-gray-400 mb-2">Sampai Tanggal (Kosongkan jika hanya 1 hari)</label>
                {{-- Input baru untuk rentang tanggal. Controller akan mengisi otomatis jika ini kosong. --}}
                <input type="date" id="tgl_izin_sampai" name="tgl_izin_sampai" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white text-sm focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition input-glow" value="{{ old('tgl_izin_sampai') }}">
                @error('tgl_izin_sampai')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pilih Jenis Izin --}}
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-400 mb-2">Jenis Izin</label>
                <select id="status" name="status" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white text-sm focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition input-glow" required>
                    <option value="">Pilih Jenis Izin</option>
                    <option value="i" {{ old('status') == 'i' ? 'selected' : '' }}>Izin (Keperluan Pribadi/Lainnya)</option>
                    <option value="s" {{ old('status') == 's' ? 'selected' : '' }}>Sakit</option>
                    <option value="c" {{ old('status') == 'c' ? 'selected' : '' }}>Cuti (Liburan/Jangka Panjang)</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Keterangan / Alasan --}}
            <div class="mb-6">
                <label for="keterangan" class="block text-sm font-medium text-gray-400 mb-2">Keterangan / Alasan</label>
                <textarea id="keterangan" name="keterangan" rows="4" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white text-sm focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition input-glow" placeholder="Jelaskan alasan pengajuan izin Anda..." required>{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- File Pendukung (Opsional untuk Sakit) --}}
            <div class="mb-6">
                <label for="file_pendukung" class="block text-sm font-medium text-gray-400 mb-2">File Pendukung (Max 2MB, jpg/png/pdf)</label>
                <input type="file" id="file_pendukung" name="file_pendukung" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-500 file:text-white hover:file:bg-cyan-600 transition">
                @error('file_pendukung')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Submit --}}
            <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-semibold py-3 rounded-lg shadow-xl shadow-cyan-600/60 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <ion-icon name="send-outline" class="text-xl"></ion-icon>
                Kirim Pengajuan Izin
            </button>
        </form>

    </div>
</div>
@endsection
