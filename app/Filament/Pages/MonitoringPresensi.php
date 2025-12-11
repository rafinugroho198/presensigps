<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\PresensiOverview;

class MonitoringPresensi extends Page
{
    // [PERBAIKAN ERROR PERTAMA] Mengikuti Union Type dari class parent
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $title = 'Monitoring Presensi Hari Ini'; 
    protected static ?string $navigationLabel = 'Monitoring Presensi'; 
    protected static ?string $slug = 'monitoring-presensi';

    protected static ?int $navigationSort = 1;

    // [PERBAIKAN ERROR KEDUA] Hapus 'static' karena $view di class parent non-static
    protected string $view = 'filament.pages.monitoring-presensi'; 
    
    protected function getHeaderWidgets(): array
    {
        return [
            PresensiOverview::class,
        ];
    }
}