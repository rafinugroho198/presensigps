@extends('layouts.presensi')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    
    <style>
        :root {
            --color-bg-dark: #0a0e14;
            --color-card-bg: #1a202c;
            --color-accent-primary: #00ffff;
            --color-accent-secondary: #ff00ff;
            --color-text-light: #e0e6ed;
            --color-text-dim: #9aa6b8;
            --color-border-subtle: #2d3748;
        }

        body, #appCapsule {
            background-color: var(--color-bg-dark);
            color: var(--color-text-light);
            font-family: 'Inter', sans-serif;
        }

        .neon-glow-card {
            background-color: var(--color-card-bg);
            box-shadow: 0 0 10px rgba(0,255,255,0.2), 0 0 20px rgba(255,0,255,0.1);
            transition: all 0.3s ease-in-out;
        }
        .neon-glow-card:hover {
            transform: scale(1.02) translateY(-3px);
            box-shadow: 0 0 20px rgba(0,255,255,0.5), 0 0 30px rgba(255,0,255,0.3);
        }

        .section-panel {
            background-color: var(--color-card-bg);
            border: 1px solid var(--color-border-subtle);
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.3);
        }

        .fade-in-section {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .fade-in-section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .profile-photo {
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
        }
        .profile-photo.loaded {
            opacity: 1;
        }

        .tab-link.active {
            color: var(--color-accent-primary) !important;
            border-bottom-color: var(--color-accent-primary) !important;
            font-weight: 600;
        }
    </style>

    <div id="appCapsule" class="p-4 md:p-8 min-h-screen">
        {{-- HEADER --}}
        <div class="section-panel p-6 rounded-xl flex items-center mb-6 fade-in-section">
            @php
                $user = Auth::guard('karyawan')->user() ?? Auth::guard('web')->user();
    
             // Gunakan Storage::url() untuk path yang lebih terjamin
             $path_foto = $user->foto ? Storage::url('uploads/profile/' . $user->foto) : null; 
    
             // Gunakan url() untuk memastikan https://presensigs.test/storage/... terbentuk dengan benar
             $foto = $path_foto 
              ? url($path_foto) . '?' . time()
              : 'https://placehold.co/64x64/00ffff/0d1117?text=PF';
            @endphp

            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <img id="profile-photo" src="{{ $foto }}" alt="Foto Profil"
                        class="profile-photo imaged w-16 h-16 rounded-full border-2 border-cyan-400 p-0.5 shadow-lg object-cover transition-transform duration-300 group-hover:scale-105">
                    <span class="absolute bottom-0 right-0 h-3 w-3 bg-green-500 rounded-full ring-2 ring-gray-800 animate-pulse"></span>
                </div>
                <div id="user-info">
                    <h2 class="text-xl font-bold text-cyan-400">
                        {{ $user->nama_lengkap ?? 'Karyawan Digital' }}
                    </h2>
                    <span class="text-sm text-gray-400">
                        {{ $user->jabatan ?? 'Digital Analyst' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- PRESENSI HARI INI --}}
        <div class="section mb-6 fade-in-section">
            <div class="text-xl font-semibold mb-4 border-b pb-2">PRESENSI HARI INI</div>
            <div class="grid grid-cols-2 gap-4">
                {{-- MASUK --}}
                <div class="neon-glow-card rounded-xl p-4 flex flex-col items-center text-center">
                    @if ($presensihariini?->jam_in)
                        @php $path_in = Storage::url('uploads/absensi/' . $presensihariini->foto_in); @endphp
                        <img src="{{ url($path_in) }}" class="w-16 h-16 rounded-full border-2 border-green-500 mb-2 object-cover shadow-md">
                    @else
                        <ion-icon name="camera-outline" class="text-4xl animate-bounce"></ion-icon>
                    @endif
                    <h4 class="text-sm text-gray-400">JAM MASUK</h4>
                    <span class="text-2xl font-extrabold text-green-400">{{ $presensihariini->jam_in ?? '00:00' }}</span>
                </div>

                {{-- PULANG --}}
                <div class="neon-glow-card rounded-xl p-4 flex flex-col items-center text-center">
                    @if ($presensihariini?->jam_out)
                        @php $path_out = Storage::url('uploads/absensi/' . $presensihariini->foto_out); @endphp
                        <img src="{{ url($path_out) }}" class="w-16 h-16 rounded-full border-2 border-red-500 mb-2 object-cover shadow-md">
                    @else
                        <ion-icon name="exit-outline" class="text-4xl animate-bounce"></ion-icon>
                    @endif
                    <h4 class="text-sm text-gray-400">JAM PULANG</h4>
                    <span class="text-2xl font-extrabold text-red-400">{{ $presensihariini?->jam_out ?? '00:00' }}</span>
                </div>
            </div>

            @if (!$presensihariini || !$presensihariini->jam_out)
                <a href="{{ url('/presensi/create') }}"
                   class="mt-6 block w-full bg-cyan-400 hover:bg-opacity-80 text-black font-bold py-4 px-4 rounded-lg text-center 
                          transition duration-300 transform hover:scale-105 shadow-lg shadow-cyan-500/40 flex items-center justify-center text-lg uppercase">
                    <ion-icon name="camera-outline" class="mr-3 text-2xl"></ion-icon> ABSEN SEKARANG
                </a>
            @endif
        </div>

       {{-- MENU --}}
<div class="section mb-6 fade-in-section">
    <div class="section-panel rounded-xl p-4">
        <div class="grid grid-cols-3 justify-items-center text-center">
            
            {{-- Profil --}}
            <a href="{{ route('profile.edit') }}" 
               class="flex flex-col items-center p-2 rounded-lg hover:bg-gray-700 transition">
                <ion-icon name="person-sharp" class="text-cyan-400 text-3xl mb-1"></ion-icon>
                <span class="text-xs text-gray-400">Profil</span>
            </a>

            {{-- Cuti --}}
            <a href="{{ route('presensi.izin') }}" 
               class="flex flex-col items-center p-2 rounded-lg hover:bg-gray-700 transition">
                <ion-icon name="calendar-number" class="text-red-400 text-3xl mb-1"></ion-icon>
                <span class="text-xs text-gray-400">Cuti</span>
            </a>

            {{-- Histori --}}
            <a href="{{ route('presensi.histori') }}" 
               class="flex flex-col items-center p-2 rounded-lg hover:bg-gray-700 transition">
                <ion-icon name="document-text" class="text-yellow-400 text-3xl mb-1"></ion-icon>
                <span class="text-xs text-gray-400">Histori</span>
            </a>

        </div>
    </div>
</div>


        {{-- HISTORY & LEADERBOARD TAB --}}
        <div class="section mb-10 fade-in-section">
            <div class="section-panel rounded-xl">
                {{-- Tab Navigation --}}
                <div class="flex border-b p-2 border-color-border-subtle">
                    <a href="#home" data-tab-id="home" class="tab-link w-1/2 text-center py-3 border-b-2 border-transparent hover:text-cyan-400">Bulan Ini</a>
                    <a href="#leaderboard" data-tab-id="leaderboard" class="tab-link w-1/2 text-center py-3 border-b-2 border-transparent hover:text-cyan-400">Leaderboard</a>
                </div>

                <div id="tab-content" class="p-4">
                    {{-- TAB BULAN INI --}}
                    <div id="home" class="tab-pane active">
                        <ul class="divide-y divide-gray-700/50">
                            @forelse ($historibulanini as $d)
                                <li class="flex justify-between py-3">
                                    <div class="flex items-center space-x-3">
                                        <ion-icon name="calendar-outline" class="text-cyan-400"></ion-icon>
                                        <div class="text-sm">
                                            <div class="font-medium">{{ date('d F Y', strtotime($d->tgl_presensi)) }}</div>
                                            <span class="text-xs text-gray-400">Masuk: {{ $d->jam_in }} | Pulang: {{ $d->jam_out ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <span class="font-bold text-sm {{ $d->jam_in > '08:00' ? 'text-red-400' : 'text-green-400' }}">
                                        {{ $d->jam_in > '08:00' ? 'Terlambat' : 'Tepat Waktu' }}
                                    </span>
                                </li>
                            @empty
                                <li class="text-center py-4 text-gray-400">Belum ada data presensi bulan ini.</li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- TAB LEADERBOARD --}}
                    <div id="leaderboard" class="tab-pane hidden">
                        <ul class="divide-y divide-gray-800/50">
                            @forelse ($leaderboard as $index => $user)
                                @php
                                    $percentage = $user->attendance_percentage ?? 0;
                                    if ($percentage >= 90) $color = 'text-green-400';
                                    elseif ($percentage >= 75) $color = 'text-yellow-400';
                                    else $color = 'text-red-400';
                                    $rankColor = match($index) {
                                        0 => 'text-yellow-400',
                                        1 => 'text-gray-300',
                                        2 => 'text-amber-600',
                                        default => 'text-gray-500'
                                    };
                                @endphp

                                <li class="flex justify-between items-center py-3 px-2 hover:bg-cyan-950/20 rounded-lg transition duration-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-cyan-700/30 text-cyan-400 font-bold shadow-lg shadow-cyan-500/30">
                                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div class="text-sm">
                                            <div class="font-medium text-white">{{ $user->nama_lengkap }}</div>
                                            <div class="text-xs text-gray-400">{{ $user->nip }}</div>
                                            <div class="text-xs {{ $color }} font-semibold mt-1">{{ $percentage }}% Kehadiran</div>
                                        </div>
                                    </div>
                                    <div class="font-bold text-lg {{ $rankColor }}">#{{ $index + 1 }}</div>
                                </li>
                            @empty
                                <li class="text-center py-4 text-gray-400">Belum ada data leaderboard bulan ini.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fade-in animasi tiap section
            document.querySelectorAll('.fade-in-section').forEach((section, i) => {
                setTimeout(() => section.classList.add('visible'), 100 * i);
            });

            // Fade-in foto profil
            const photo = document.getElementById('profile-photo');
            if (photo.complete) photo.classList.add('loaded');
            else photo.addEventListener('load', () => photo.classList.add('loaded'));

            // Tab handler
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContainer = document.getElementById('tab-content');
            tabLinks.forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    tabLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    tabContainer.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
                    const target = document.getElementById(link.getAttribute('data-tab-id'));
                    if (target) target.classList.remove('hidden');
                });
            });
            document.querySelector('.tab-link[data-tab-id="home"]').click();
        });
    </script>
@endsection
