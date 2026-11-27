# SIKADES

SIKADES (Sistem Informasi Surat Keterangan Desa) adalah aplikasi web berbahasa
Indonesia untuk pengajuan, verifikasi, persetujuan, penerbitan, dan verifikasi
publik surat keterangan desa.

## Fitur

- Akun dan akses berbasis peran: admin, kades, sekretaris, dan warga.
- Pengelolaan penduduk, jenis surat, pejabat desa, pengguna petugas, dan profil desa.
- Pengajuan surat warga, unggah dokumen privat, antrean verifikasi/persetujuan,
  notifikasi, dan log aktivitas.
- Penerbitan arsip PDF dengan nomor surat otomatis dan QR Code verifikasi.
- Pencarian surat publik, laporan periode/jenis/status, serta ekspor Excel dan PDF.
- Dashboard ringkasan dengan grafik Chart.js dan pengingat pengajuan tertunda.
- Backup terjadwal atas database SQLite serta file unggahan dan PDF pada disk
  privat/publik Laravel. Arsip disimpan privat di `storage/app/backups`.

## Persyaratan

- PHP 8.2 atau lebih baru dengan ekstensi Laravel/PDO SQLite dan ekstensi ZIP.
- Composer 2.
- Node.js dan npm untuk membangun aset frontend.
- SQLite dan executable `sqlite3` di `PATH` (diperlukan Spatie Backup untuk
  membuat dump database).

## Instalasi lokal

Jalankan perintah berikut dari folder proyek:

1. Pasang dependensi PHP dan frontend:

   ```sh
   composer install
   npm install
   ```

2. Salin `.env.example` menjadi `.env`, lalu buat kunci aplikasi:

   ```sh
   cp .env.example .env
   php artisan key:generate
   ```

   Di PowerShell:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Pastikan `DB_CONNECTION=sqlite` dan file database tersedia:

   ```sh
   touch database/database.sqlite
   ```

   Di PowerShell:

   ```powershell
   if (-not (Test-Path database/database.sqlite)) {
       New-Item database/database.sqlite -ItemType File
   }
   ```

4. Migrasikan dan isi data awal, buat tautan berkas publik, dan build aset:

   ```sh
   php artisan migrate --seed
   php artisan storage:link
   npm run build
   ```

5. Jalankan aplikasi:

   ```sh
   php artisan serve
   ```

   Buka `http://127.0.0.1:8000`.

   Backup otomatis dijalankan setiap hari pukul 01.00 dan dibersihkan pukul
   01.30. Untuk menguji secara manual, jalankan `php artisan backup:run`, lalu
   periksa arsip di `storage/app/backups`. Atur `BACKUP_ARCHIVE_PASSWORD` di
   `.env` untuk mengenkripsi arsip; simpan kata sandi tersebut terpisah dari
   backup. Perintah backup memerlukan executable `sqlite3` di `PATH`.

### Akun demo

Semua akun demo menggunakan kata sandi `password`:

| Peran | Email |
| --- | --- |
| Admin | `admin@sikades.test` |
| Kepala Desa | `kades@sikades.test` |
| Sekretaris | `sekretaris@sikades.test` |
| Warga | `warga@sikades.test` |

Ganti kata sandi demo sebelum aplikasi dipublikasikan.

## Deployment

1. Siapkan server dengan PHP 8.2+, ekstensi PHP yang dibutuhkan Laravel,
   Composer, SQLite, executable `sqlite3`, HTTPS, serta akses ke Node.js/npm saat membangun aset.
   Arahkan document root web server ke folder `public`, bukan root proyek.
2. Buat `.env` dari `.env.example` dan isi nilai produksi, sekurangnya:

   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-desa.example
   APP_KEY=
   DB_CONNECTION=sqlite
   DB_DATABASE=/path/absolut/aplikasi/database/database.sqlite
   ```

   Jalankan `php artisan key:generate` untuk membuat `APP_KEY`. Jangan
   menggunakan akun demo atau menyimpan `.env` ke Git.
3. Pasang dependensi/build aset dan siapkan database:

   ```sh
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   ```

   Jika Node.js tidak tersedia di server, build aset pada mesin deployment dan
   kirim folder `public/build` sebagai bagian dari artefak rilis.
   Jangan jalankan `php artisan db:seed --force` di produksi karena seeder
   aplikasi membuat akun demo. Untuk inisialisasi pertama, buat role dan akun
   admin dengan kata sandi kuat melalui proses deployment terkontrol, misalnya:

   ```sh
   php artisan db:seed --class=RoleSeeder --force
   php artisan tinker
   ```

   Di Tinker, buat `User` admin dengan NIK/email/nomor HP produksi lalu
   panggil `$user->assignRole('admin')`. Jangan memakai kredensial contoh lokal.
4. Berikan izin tulis hanya kepada pengguna layanan web/PHP untuk `storage`,
   `bootstrap/cache`, `database/database.sqlite`, serta direktori `database`
   (SQLite mungkin perlu membuat berkas `-wal`/`-shm`). Contoh Linux setelah
   mengatur pemilik/grup sesuai konfigurasi server:

   ```sh
   chmod -R ug+rwX storage bootstrap/cache
   chmod ug+rw database/database.sqlite
   chmod ug+rwx database
   ```

   Jangan membuat folder dokumen privat di dalam `public`; berkas pengajuan
   dan PDF surat disimpan di disk privat Laravel.
5. Aktifkan scheduler Laravel dengan cron setiap menit:

   ```cron
   * * * * * cd /path/aplikasi && php artisan schedule:run >> /dev/null 2>&1
   ```

   Perintah ini menjalankan backup harian. Sesudah konfigurasi backup diubah,
   uji dengan `php artisan backup:run` dan pastikan `storage/app/backups`
   memiliki ruang serta izin tulis. File database SQLite, unggahan privat, dan
   file publik desa dicakup oleh backup; log dan cache runtime tidak.
6. Untuk rilis berikutnya, jalankan migrasi dengan `--force`, kemudian
   `php artisan optimize`. Jika konfigurasi environment berubah, jalankan
   `php artisan optimize:clear` sebelum membuat cache konfigurasi kembali.

Simpan `.env`, database produksi, dan berkas backup di luar Git. Lindungi tujuan
backup dengan akses terbatas dan uji proses pemulihan secara berkala.
