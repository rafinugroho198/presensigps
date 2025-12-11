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
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            // Menambahkan kolom baru 'file_pendukung'
            // Gunakan tipe string dengan panjang yang cukup (misalnya 255)
            // Kolom ini diizinkan NULL (nullable) atau tidak, tergantung kebutuhan Anda.
            // Jika izin/cuti wajib melampirkan file, hapus ->nullable().
            $table->string('file_pendukung', 255)->nullable()->after('status_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            // Menghapus kolom 'file_pendukung' jika migration di-rollback
            $table->dropColumn('file_pendukung');
        });
    }
};