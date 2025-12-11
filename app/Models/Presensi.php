<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';
    protected $primaryKey = 'id';
    public $timestamps = false; // tabel ini tidak pakai created_at/updated_at

    protected $fillable = [
        'nip', 'tgl_presensi', 'jam_in', 'jam_out', 
        'foto_in', 'foto_out', 'lokasi_in', 'lokasi_out',
    ];

    public function karyawan()
    {
        return $this->belongsTo(User::class, 'nip', 'nip');
    }
}
