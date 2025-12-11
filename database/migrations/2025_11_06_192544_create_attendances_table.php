<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Waktu Masuk dan Keluar
            $table->dateTime('check_in_time');
            $table->dateTime('check_out_time')->nullable(); // Bisa null jika belum check-out
            
            // Kolom Opsional (jika menggunakan GPS)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('status')->default('Hadir'); // Opsional: Hadir, Terlambat, dll.

            $table->timestamps();
        });
    }

    // ... method down()
};