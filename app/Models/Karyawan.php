<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class karyawan extends Authenticatable
{

    use HasFactory, Notifiable;

    protected $table = "karyawan";
    protected $primaryKey = "nip";
    protected $fillable = [
        'nip',
        'nama_lengkap',
        'jabatan',
        'no_hp',
        'password',
        'foto', // <-- BARIS INI DITAMBAHKAN/DIPERBAIKI
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
