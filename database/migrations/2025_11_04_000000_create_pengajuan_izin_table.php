<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->id();
            // NIP (Nomor Induk Pegawai)
            $table->char('nip', 8); 
            
            // Tanggal Izin
            $table->date('tgl_izin_dari');
            $table->date('tgl_izin_sampai');

            // Jenis Status Pengajuan
            $table->enum('status', ['i', 's', 'c'])->default('i'); 

            // Keterangan atau alasan pengajuan
            $table->text('keterangan');
            
            // 💡 PERBAIKAN: Tambahkan kolom file_pendukung
            // Menggunakan string karena hanya menyimpan nama file, dan nullable karena opsional.
            $table->string('file_pendukung', 255)->nullable(); 

            // Status Persetujuan
            // 0 = Pending/Menunggu, 1 = Disetujui, 2 = Ditolak
            $table->integer('status_approved')->default(0); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};
