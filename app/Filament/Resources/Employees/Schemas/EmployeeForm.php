<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nip')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('jabatan'),
                TextInput::make('departemen'),
                TextInput::make('no_hp'),
                DatePicker::make('tgl_masuk'),
            ]);
    }
}
