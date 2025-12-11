<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon; // Gunakan Illuminate\Support\Carbon

class PresensiOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $totalUsers = User::count();
        
        // Hitung yang sudah check-in hari ini
        $checkedInCount = Attendance::whereDate('check_in_time', $today)
                                    ->distinct('user_id')
                                    ->count();
        
        $notCheckedInCount = $totalUsers - $checkedInCount;

        return [
            Stat::make('Total Karyawan', $totalUsers)
                ->description('Jumlah Karyawan Terdaftar'),
            
            Stat::make('Sudah Presensi Hari Ini', $checkedInCount)
                ->color('success')
                ->description('Pegawai yang sudah Check-In'),
            
            Stat::make('Belum Presensi Hari Ini', $notCheckedInCount)
                ->color('danger')
                ->description('Pegawai yang belum Check-In'),
        ];
    }
}