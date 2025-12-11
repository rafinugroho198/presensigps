<?php

namespace App\Filament\Resources\Presensis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class PresensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nip')
                    ->required(),
                DatePicker::make('tgl_presensi')
                    ->required(),
                TimePicker::make('jam_in'),
                TimePicker::make('jam_out'),
                TextInput::make('lokasi_in'),
                TextInput::make('lokasi_out'),
                TextInput::make('status'),
                TextInput::make('foto_in'),
                TextInput::make('foto_out'),
            ]);
    }
}
