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
        Schema::table('karyawan', function (Blueprint $table) {
            // Menambahkan kolom 'foto' setelah kolom 'jabatan' sebagai string, dan diizinkan null
            $table->string('foto')->nullable()->after('jabatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // Menghapus kolom 'foto' saat rollback
            $table->dropColumn('foto');
        });
    }
};