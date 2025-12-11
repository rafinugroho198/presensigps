<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\KaryawanSeeder; // Pastikan Anda mengimpor KaryawanSeeder

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder lain di sini
        $this->call([
            KaryawanSeeder::class, // <-- MEMANGGIL KaryawanSeeder
            // Jika ada seeder lain, panggil juga di sini
        ]);

        // Contoh Seeder bawaan untuk model User (jika masih Anda gunakan)
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}