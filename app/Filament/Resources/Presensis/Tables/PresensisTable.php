<?php

namespace App\Filament\Resources\Presensis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PresensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip')
                    ->searchable(),
                TextColumn::make('tgl_presensi')
                    ->date()
                    ->sortable(),
                TextColumn::make('jam_in')
                    ->time()
                    ->sortable(),
                TextColumn::make('jam_out')
                    ->time()
                    ->sortable(),
                TextColumn::make('lokasi_in')
                    ->searchable(),
                TextColumn::make('lokasi_out')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('foto_in')
                    ->searchable(),
                TextColumn::make('foto_out')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
