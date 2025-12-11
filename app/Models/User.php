<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser; // <-- PENTING! Import interface ini
use Filament\Panel; // <-- PENTING! Import class Panel
use App\Models\Attendance; // <-- TAMBAH: Import Model Attendance
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- TAMBAH: Import HasMany

class User extends Authenticatable implements FilamentUser // <-- IMPLEMENTASI INTERFACE
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang bisa diisi (mass assignable).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nama_lengkap',
        'jabatan',
        'foto',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi (misal: JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konversi otomatis (casting) tipe data kolom.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==============================================
    // FILAMENT INTERFACE METHOD: WAJIB ADA
    // ==============================================
    /**
     * Menentukan apakah pengguna dapat mengakses Panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Untuk saat ini, kita akan mengizinkan semua pengguna yang ada di model User ini
        // (misalnya, hanya untuk admin yang dibuat via make:filament-user).
        // DI MASA DEPAN, Anda bisa menambahkan logika di sini
        // seperti: return $this->is_admin === true;
        return true; 
    }
    // ==============================================
    
    // ==============================================
    // RELASI ELOQUENT PRESENSI
    // ==============================================
    /**
     * Relasi ke Model Attendance. Satu User memiliki banyak entri presensi.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
    // ==============================================

    /**
     * Helper untuk ambil URL foto profil.
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            // Menggunakan storage/public/uploads/profile
            return asset('storage/uploads/profile/' . $this->foto);
        }

        // Default placeholder
        return 'https://placehold.co/128x128/00ffff/0d1117?text=PF';
    }
}