<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data karyawan yang akan di-seeding
        DB::table('karyawan')->insert([
            [
                'nip'      => '2023001', // NIP baru
                'nama_lengkap'     => 'Admin Utama',
                // Password di-hash menggunakan Facade Hash::make()
                'password' => Hash::make('passwordku123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip'      => '2023002', // NIP baru lainnya
                'nama_lengkap'     => 'Karyawan Biasa',
                'password' => Hash::make('rahasia456'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tambahkan data karyawan lain di sini
        ]);
    }
}