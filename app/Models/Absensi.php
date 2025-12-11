<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi'; // karena nama tabel tunggal
    protected $fillable = [
        'user_id',
        'foto',
        'latitude',
        'longitude',
        'waktu_absen'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
