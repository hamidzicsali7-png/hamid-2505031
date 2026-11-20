# SIKADES

SIKADES (Sistem Informasi Surat Keterangan Desa) adalah aplikasi web Laravel
untuk pengurusan surat keterangan di kantor desa.

## Persyaratan

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan npm

## Instalasi lokal

1. Pasang dependensi PHP dan JavaScript:

   ```sh
   composer install
   npm install
   ```

2. Salin konfigurasi lingkungan dan buat kunci aplikasi:

   ```sh
   cp .env.example .env
   php artisan key:generate
   ```

   Pada PowerShell, gunakan `Copy-Item .env.example .env` sebagai pengganti
   perintah `cp`.

3. Pastikan `.env` menggunakan `DB_CONNECTION=sqlite`. Buat file database
   kosong jika belum tersedia:

   ```sh
   touch database/database.sqlite
   ```

   Pada PowerShell:

   ```powershell
   if (-not (Test-Path database/database.sqlite)) {
       New-Item database/database.sqlite -ItemType File
   }
   ```

4. Jalankan migrasi database dan build aset frontend:

   ```sh
   php artisan migrate
   npm run build
   ```

5. Jalankan server pengembangan:

   ```sh
   php artisan serve
   ```

   Buka `http://127.0.0.1:8000` di browser.
