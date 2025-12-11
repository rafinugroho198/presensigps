<x-filament-panels::page>
    <div class="space-y-6">
        @livewire(\App\Filament\Widgets\PresensiOverview::class)

        <x-filament::section>
            <x-slot name="heading">
                Detail Presensi Harian
            </x-slot>

            @livewire(\App\Filament\Widgets\AttendanceDetailTable::class)
        </x-filament::section>
    </div>
</x-filament-panels::page>
