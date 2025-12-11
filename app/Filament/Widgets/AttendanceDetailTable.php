<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;

class AttendanceDetailTable extends BaseWidget
{
    protected static ?string $heading = 'Detail Presensi Harian';
    protected static ?int $sort = 2;

    protected function getTableQuery(): Builder
    {
        return Presensi::query()
            ->whereDate('tgl_presensi', Carbon::today())
            ->orderByDesc('jam_in');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('nip')
                ->label('NIP')
                ->sortable()
                ->searchable(),

            TextColumn::make('tgl_presensi')
                ->label('Tanggal')
                ->date('d/m/Y'),

            TextColumn::make('jam_in')
                ->label('Masuk')
                ->time('H:i:s'),

            TextColumn::make('jam_out')
                ->label('Pulang')
                ->time('H:i:s')
                ->default('-'),

            TextColumn::make('lokasi_in')
                ->label('Lokasi Masuk')
                ->formatStateUsing(fn($state) => $state ?: '-'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('date')
                ->form([
                    DatePicker::make('date_filter')
                        ->label('Tanggal')
                        ->default(Carbon::today()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['date_filter'],
                        fn (Builder $query, $date) => $query->whereDate('tgl_presensi', $date)
                    );
                }),
        ];
    }

    protected function getTablePaginationPageOptions(): array
    {
        return [10, 25, 50];
    }
}
