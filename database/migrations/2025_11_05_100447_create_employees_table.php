<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Kolom Data Karyawan
            $table->string('nip')->unique()->comment('Nomor Induk Pegawai');
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('departemen')->nullable();
            $table->string('no_hp', 15)->nullable();
            $table->date('tgl_masuk')->nullable()->comment('Tanggal mulai bekerja');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};