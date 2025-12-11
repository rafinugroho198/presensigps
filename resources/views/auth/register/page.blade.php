<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Presensi - Daftar Akun Baru</title>
    <style>
        /* --- STYLE CSS KONSISTEN DENGAN LOGIN PAGE --- */
        body {
            font-family: Arial, sans-serif;
            background-color: #0b1d51; /* Biru Tua/Navy, mirip dengan gambar */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .register-container {
            background-color: #ffffff;
            padding: 40px;
            padding-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .logo-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo-section img {  
            width: 100px;
            height: auto;
            margin-bottom: 15px;
        }

        .logo-section h1 {
            color: #0b1d51;
            font-size: 1.8em;
            margin: 0;
            font-weight: bold;
        }

        .logo-section p {
            color: #555;
            margin-top: 5px;
            font-size: 1em;
        }

        /* Styling Form */
        .input-group {
            margin-bottom: 15px;
            text-align: left; /* Penting untuk error message */
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
            text-align: left;
        }

        .input-group input:focus {
            border-color: #4c8ff8;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 143, 248, 0.5);
        }

        /* Tombol Utama (Warna Biru Cerah) */
        .main-button {
            background-color: #4c8ff8;
            color: white;
            padding: 14px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 700;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.2s;
        }

        .main-button:hover {
            background-color: #3b74d1;
        }

        /* Tautan Kecil di Bawah */
        .footer-links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .footer-links a {
            color: #4c8ff8;
            text-decoration: none;
            font-size: 0.9em;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
        
        /* Gaya Error Laravel */
        .error-message {
            color: #dc3545; /* Merah */
            font-size: 0.85em;
            margin-top: 4px;
            display: block;
        }

        /* Gaya Khusus untuk logo agar bisa ditampilkan */
        .logo-placeholder {
            width: 100px;
            height: 100px;
            background-color: #fff; 
            border: 1px solid #ccc;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.8em;
            color: #0b1d51;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-section">
            <div class="logo-placeholder">
                   <img src="{{ asset('assets/img/login/login.png') }}" alt="image" class="form-image">
            </div>
            
            <h1 style="color: #0b1d51;">E-Presensi</h1> <!-- Hapus background biru di sini agar konsisten -->
            <p style="color: #0b1d51;">Silakan Daftarkan Akun Anda</p>
        </div>
        
        <!-- PERBAIKAN 1: Tambahkan method POST dan action route -->
        <form method="POST" action="{{ route('register') }}">
            <!-- PERBAIKAN 2: Tambahkan token CSRF (Wajib di Laravel) -->
            @csrf 
            
            <div class="input-group">
                <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" value="{{ old('nama') }}" required>
                @error('nama')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="input-group">
                <input type="text" id="nip" name="nip" placeholder="NIP" value="{{ old('nip') }}" required>
                @error('nip')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="input-group">
                <!-- PERBAIKAN 3: Ganti name menjadi 'password_confirmation' (Wajib untuk validasi 'confirmed') -->
                <input type="password" id="confirmPassword" name="password_confirmation" placeholder="Konfirmasi Password" required>
            </div>
            
            <button type="submit" class="main-button">Daftar Akun</button>
        </form>

        <div class="footer-links">
            <!-- PERBAIKAN 4: Aktifkan tautan balik ke halaman login -->
            <a href="{{ route('login') }}" class="login-now-link">Sudah punya akun? Login</a>
            <a href="{{ route('filament.admin.auth.login') }}" class="admin-login-link">Login Admin</a>
        </div>
    </div>

    <!-- Hapus Script JavaScript statis, biarkan Laravel yang menangani validasi -->
</body>
</html>