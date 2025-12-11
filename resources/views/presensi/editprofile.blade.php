@extends('layouts.presensi')

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

@php
    // Deteksi user login
    $user = Auth::guard('web')->check() ? Auth::guard('web')->user() : Auth::guard('karyawan')->user();
    $routeUpdate = Auth::guard('web')->check() ? route('admin.profile.update') : route('profile.update');
    
    // Gunakan logika Storage::url() yang sudah kita sepakati sebagai solusi path yang paling aman
    $path_foto = $user->foto ? Storage::url('uploads/profile/' . $user->foto) : null; 
    $foto_url = $path_foto 
        ? url($path_foto) . '?' . time()
        : 'https://placehold.co/128x128/0d1117/00ffff?text=PF';
@endphp

<div class="min-h-screen flex justify-center items-center py-10 px-4 bg-[#0d1117] text-white">
    <div class="w-full max-w-lg bg-[#161b22] border border-cyan-500/30 rounded-2xl shadow-2xl shadow-cyan-900/50 p-8 transition-all duration-500 hover:shadow-cyan-900/70">
        
        <h2 class="text-3xl font-extrabold mb-8 text-center text-cyan-400">
            <ion-icon name="person-circle-outline" class="align-middle text-4xl mr-1"></ion-icon>
            EDIT PROFIL
        </h2>

        {{-- ✅ Pesan sukses/error --}}
        @if (session('success'))
            <div class="p-4 rounded-xl mb-6 text-center bg-green-700/30 border border-green-500/50 text-green-200 animate__animated animate__fadeInDown">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="p-4 rounded-xl mb-6 bg-red-700/30 border border-red-500/50 text-red-200 animate__animated animate__fadeInDown">
                <p class="font-semibold mb-2">Terjadi Kesalahan:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM EDIT PROFIL --}}
        <form action="{{ $routeUpdate }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="space-y-8">

                {{-- **FOTO PROFIL & INFO DASAR** --}}
                <div class="p-6 bg-[#0d1117] rounded-xl border border-gray-700/50">
                    <h3 class="text-xl font-semibold mb-4 text-cyan-400 border-b border-gray-700 pb-2">Data Pengguna</h3>

                    <div class="flex flex-col items-center mb-6">
                        <div class="relative group cursor-pointer" onclick="document.getElementById('file-input').click()">
                            <img 
                                src="{{ $foto_url }}" 
                                alt="Foto Profil"
                                id="profile-image"
                                class="w-32 h-32 rounded-full border-4 border-cyan-500 object-cover shadow-lg shadow-cyan-900/50 transition-transform duration-300 group-hover:scale-105">
                            
                            {{-- Overlay untuk ganti foto --}}
                            <div class="absolute inset-0 rounded-full bg-cyan-500/10 opacity-0 group-hover:opacity-100 transition duration-300"></div>
                            <div class="absolute inset-0 flex items-center justify-center rounded-full opacity-0 group-hover:opacity-100 transition duration-300">
                                <ion-icon name="camera-outline" class="text-white text-4xl"></ion-icon>
                            </div>
                        </div>
                        <label class="mt-3 text-sm font-medium text-gray-400 cursor-pointer hover:text-cyan-400 transition-colors" onclick="document.getElementById('file-input').click()">
                            Klik untuk Ganti Foto
                        </label>
                        <input type="file" name="foto" id="file-input" accept="image/*" class="hidden" onchange="previewImage(event)">
                    </div>

                    {{-- NAMA LENGKAP --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                            placeholder="Nama Lengkap Anda"
                            class="input-glow w-full bg-[#0d1117] border border-gray-700 rounded-xl p-3 text-white transition placeholder-gray-500 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400">
                    </div>

                    {{-- JABATAN --}}
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}"
                            placeholder="Jabatan Anda"
                            class="input-glow w-full bg-[#0d1117] border border-gray-700 rounded-xl p-3 text-white transition placeholder-gray-500 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400">
                    </div>
                </div>

                {{-- **PASSWORD BARU SECTION** --}}
                <div class="p-6 bg-[#0d1117] rounded-xl border border-gray-700/50">
                    <h3 class="text-xl font-semibold mb-4 text-orange-400 border-b border-gray-700 pb-2">Keamanan</h3>

                    {{-- PASSWORD BARU --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Password Baru 
                            <span class="text-orange-500">(Kosongkan jika tidak ada perubahan)</span>
                        </label>
                        <input type="password" name="password" placeholder="Masukkan password baru (min 8 karakter)"
                            class="input-glow w-full bg-[#0d1117] border border-gray-700 rounded-xl p-3 text-white transition placeholder-gray-500 focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                    </div>
                </div>

            </div>

            {{-- TOMBOL SIMPAN --}}
            <div class="flex justify-center pt-4">
                <button type="submit"
                        class="bg-cyan-500 hover:bg-cyan-400 text-black font-extrabold py-3 px-10 rounded-xl 
                                shadow-lg shadow-cyan-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center gap-2 uppercase tracking-wider">
                    <ion-icon name="save-outline" class="text-xl"></ion-icon>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Preview Foto Otomatis --}}
<script>
    function previewImage(event) {
        const image = document.getElementById('profile-image');
        const file = event.target.files[0];
        if (file) {
            image.src = URL.createObjectURL(file);
        }
    }
</script>
@endsection