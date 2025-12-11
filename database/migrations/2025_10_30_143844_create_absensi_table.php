<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasinya.
     */
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('foto');       // nama file foto hasil webcam
            $table->string('latitude');   // posisi GPS
            $table->string('longitude');
            $table->timestamp('waktu_absen')->useCurrent(); // waktu absen otomatis
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasinya (jika rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
